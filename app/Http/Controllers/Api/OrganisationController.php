<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use App\Http\Controllers\ApiController;
use App\Organisation;
use App\VotingTour;
use App\User;
use App\File;

class OrganisationController extends ApiController
{
    /**
     * Register new organisation
     *
     * @param array org_data - required
     * @param big integer org_data[eik] - required
     * @param string org_data[name] - required
     * @param string org_data[address] - required
     * @param string org_data[representative] - required
     * @param string org_data[email] - required
     * @param boolean org_data[in_ap] - optional
     * @param boolean org_data[is_candidate] - optional
     * @param string org_data[description] - optional
     * @param string org_data[references] - optional
     * @param array files - optional
     * @param string files[name] - required
     * @param string files[mime_type] - required
     * @param string files[data] - optional
     *
     * @return json - response with status code and organisation id or errors
     */
    public function register(Request $request)
    {
        $votingTour = VotingTour::getLatestTour();
        if (!empty($votingTour) && $votingTour->status == VotingTour::STATUS_OPENED_REG) {
            $data = $request->get('org_data', []);
            $data['files'] = $request->get('files', []);

            $validator = \Validator::make($data, [
                'eik'               => 'required|digits_between:1,19|unique:organisations,eik,NULL,id,voting_tour_id,'. $votingTour->id,
                'name'              => 'required|string|max:255',
                'address'           => 'required|string|max:512',
                'representative'    => 'required|string|max:512',
                'email'             => 'required|string|max:255',
                'in_ap'             => 'bool',
                'is_candidate'      => 'bool',
                'description'       => 'nullable|max:8000',
                'references'        => 'nullable|max:8000',
                'files'             => 'nullable|array',
                'files.*.name'      => 'required|string|max:255',
                'files.*.mime_type' => 'required|string|max:255',
                'files.*.data'      => 'required|string|max:16777215',
            ]);

            if (!$validator->fails()) {
                try {
                    DB::beginTransaction();

                    $organisation = new Organisation;
                    $organisation->eik = $data['eik'];
                    $organisation->voting_tour_id = $votingTour->id;
                    $organisation->name = $data['name'];
                    $organisation->address = $data['address'];
                    $organisation->representative = $data['representative'];
                    $organisation->email = $data['email'];
                    if (isset($data['in_ap'])) {
                        $organisation->in_ap = $data['in_ap'];
                    } else {
                        $organisation->in_ap = Organisation::IN_AP_FALSE;
                    }
                    if (isset($data['is_candidate'])) {
                        $organisation->is_candidate = $data['is_candidate'];
                    } else {
                        $organisation->is_candidate = Organisation::IS_CANDIDATE_FALSE;
                    }
                    if (isset($data['description'])) {
                        $organisation->description = $data['description'];
                    }
                    if (isset($data['references'])) {
                        $organisation->references = $data['references'];
                    }

                    $organisation->save();

                    foreach ($data['files'] as $newFile) {
                        $file = new File;
                        $file->name = $newFile['name'];
                        $file->data = base64_decode($newFile['data']);
                        $file->mime_type = $newFile['mime_type'];
                        $file->org_id = $organisation->id;
                        $file->voting_tour_id = $votingTour->id;

                        $file->save();
                    }

                    DB::commit();

                    return $this->successResponse(['org_id' => $organisation->id], true);
                } catch (QueryException $e) {dd($e);
                    DB::rollback();
                    logger()->error($e->getMessage());
                } catch (\Exception $e) {dd($e);
                    logger()->error($e->getMessage());
                }
            }

            return $this->errorResponse(__('custom.reg_org_fail'), $validator->errors()->messages());
        }

        return $this->errorResponse(__('custom.reg_org_not_accepted'));
    }

    /**
     * Edit organisation record
     *
     * @param integer org_id - required
     * @param array org_data - required
     * @param string org_data[name] - optional
     * @param string org_data[address] - optional
     * @param string org_data[representative] - optional
     * @param string org_data[email] - optional
     * @param boolean org_data[in_ap] - optional
     * @param boolean org_data[is_candidate] - optional
     * @param string org_data[description] - optional
     * @param string org_data[references] - optional
     * @param integer org_data[status] - optional
     * @param integer org_data[status_hint] - optional
     *
     * @return json - response with status code and success or errors
     */
    public function edit(Request $request)
    {
        $votingTour = VotingTour::getLatestTour();
        if (!empty($votingTour) && in_array($votingTour->status, VotingTour::getRegStatuses())) {
            $data = $request->get('org_data', []);
            $data['org_id'] = $request->get('org_id', null);

            $validator = \Validator::make($data, [
                'org_id'         => 'required|int|exists:organisations,id|digits_between:1,10',
                'name'           => 'string|max:255',
                'address'        => 'string|max:512',
                'representative' => 'string|max:512',
                'email'          => 'string|max:255',
                'in_ap'          => 'bool',
                'is_candidate'   => 'bool',
                'description'    => 'nullable|max:8000',
                'references'     => 'nullable|max:8000',
                'status'         => 'int|in:'. implode(',', array_keys(Organisation::getStatuses())),
                'status_hint'    => 'nullable|int|in:'. implode(',', array_keys(Organisation::getStatusHints())),
            ]);

            if (!$validator->fails()) {
                try {
                    DB::beginTransaction();

                    $organisation = Organisation::findOrFail($data['org_id']);

                    if ($organisation) {
                        $orgData = [];
                        if (!empty($data['name'])) {
                            $orgData['name'] = $data['name'];
                        }
                        if (!empty($data['address'])) {
                            $orgData['address'] = $data['address'];
                        }
                        if (!empty($data['representative'])) {
                            $orgData['representative'] = $data['representative'];
                        }
                        if (isset($data['in_ap'])) {
                            $orgData['in_ap'] = $data['in_ap'];
                        }
                        if (isset($data['is_candidate'])) {
                            $orgData['is_candidate'] = $data['is_candidate'];
                        }
                        if (isset($data['description'])) {
                            $orgData['description'] = $data['description'];
                        }
                        if (isset($data['references'])) {
                            $orgData['references'] = $data['references'];
                        }
                        if (isset($data['status'])) {
                            $orgData['status'] = $data['status'];
                        }
                        if (isset($data['status_hint'])) {
                            $orgData['status_hint'] = $data['status_hint'];
                        }

                        if (!empty($orgData)) {
                            foreach ($orgData as $prop => $val) {
                                $organisation->$prop = $val;
                            }

                            $organisation->save();
                        }

                        DB::commit();

                        return $this->successResponse();
                    }
                } catch (QueryException $e) {
                    DB::rollback();
                    logger()->error($e->getMessage());
                }
            }

            return $this->errorResponse(__('custom.edit_org_fail'), $validator->errors()->messages());
        }

        return $this->errorResponse(__('custom.edit_org_not_allowed'));
    }

    /**
     * List organisations by filters
     *
     * @param array filters - optional
     * @param big integer filters[eik] - optional
     * @param string filters[name] - optional
     * @param string filters[email] - optional
     * @param boolean filters[in_ap] - optional
     * @param boolean filters[is_candidate] - optional
     * @param string filters[status] - optional
     * @param string filters[reg_date_from] - optional
     * @param string filters[reg_date_to] - optional
     * @param string order_field - optional
     * @param string order_type - optional
     * @param integer page_number - optional
     *
     * @return json - response with status code and list of organisations or errors
     */
    public function search(Request $request)
    {
        $filters = $request->get('filters', []);
        $orderField = $request->get('order_field', Organisation::DEFAULT_ORDER_FIELD);
        $orderType = strtoupper($request->get('order_type', Organisation::DEFAULT_ORDER_TYPE));
        $pageNumber = $request->get('page_number', 1);

        $validator = \Validator::make($filters, [
            'eik'           => 'nullable|digits_between:1,19',
            'name'          => 'nullable|string|max:255',
            'email'         => 'nullable|string|max:255',
            'in_ap'         => 'nullable|bool',
            'is_candidate'  => 'nullable|bool',
            'status'        => 'nullable|int|in:'. implode(',', array_keys(Organisation::getStatuses())),
            'reg_date_from' => 'nullable|date|date_format:Y-m-d',
            'reg_date_to'   => 'nullable|date|date_format:Y-m-d'. (!empty($filters['reg_date_from']) ? '|after_or_equal:reg_date_from' : ''),
        ]);

        if (!$validator->fails()) {
            if (!in_array($orderField, Organisation::getOrderColumns()) || !in_array($orderType, ['ASC', 'DESC'])) {
                return $this->errorResponse(__('custom.invalid_sort_field'));
            }

            $results = [];
            $count = 0;

            try {
                $votingTour = VotingTour::getLatestTour();
                if (!empty($votingTour)) {
                    $query = Organisation::where('voting_tour_id', $votingTour->id);
                    if (isset($filters['eik'])) {
                        $query->where('eik', $filters['eik']);
                    }
                    if (isset($filters['name'])) {
                        $query->where('name', 'LIKE', '%'. trim($filters['name']) .'%');
                    }
                    if (isset($filters['email'])) {
                        $query->where('email', 'LIKE', '%'. trim($filters['email']) .'%');
                    }
                    if (isset($filters['in_ap'])) {
                        $query->where('in_ap', $filters['in_ap']);
                    }
                    if (isset($filters['is_candidate'])) {
                        $query->where('is_candidate', $filters['is_candidate']);
                    }
                    if (isset($filters['status'])) {
                        $query->where('status', $filters['status']);
                    }
                    if (isset($filters['reg_date_from'])) {
                        $query->where('created_at', '>=', $filters['reg_date_from'] .' 00:00:00');
                    }
                    if (isset($filters['reg_date_to'])) {
                        $query->where('created_at', '<=', $filters['reg_date_to'] .' 23:59:59');
                    }

                    $count = $query->count();

                    $query->orderBy($orderField, $orderType);

                    $query->forPage(
                       (intval($pageNumber) > 0 ? intval($pageNumber) : 1),
                        Organisation::DEFAULT_RECORDS_PER_PAGE
                    );

                    foreach ($query->get() as $organisation) {
                        $results[] = [
                            'id'             => $organisation->id,
                            'eik'            => $organisation->eik,
                            'voting_tour_id' => $organisation->voting_tour_id,
                            'name'           => $organisation->name,
                            'address'        => $organisation->address,
                            'representative' => $organisation->representative,
                            'email'          => $organisation->email,
                            'in_ap'          => $organisation->in_ap,
                            'is_candidate'   => $organisation->is_candidate,
                            'description'    => $organisation->description,
                            'references'     => $organisation->references,
                            'status'         => $organisation->status,
                            'status_hint'    => $organisation->status_hint,
                            'created_at'     => $organisation->created_at->toDateTimeString(),
                            'updated_at'     => isset($organisation->updated_at) ? $organisation->updated_at->toDateTimeString() : null,
                            'created_by'     => isset($organisation->created_by) ? $organisation->created_by : null,
                            'updated_by'     => isset($organisation->updated_by) ? $organisation->updated_by : null,
                        ];
                    }
                }

                return $this->successResponse(['organisations' => $results, 'total_records' => $count], true);
            } catch (QueryException $e) {
                logger()->error($e->getMessage());
            }
        }

        return $this->errorResponse(__('custom.list_org_fail'), $validator->errors()->messages());
    }

    /**
     * Get organisation data
     *
     * @param integer org_id - required without eik
     * @param big integer eik - required without org_id
     *
     * @return json - response with status code and organisation data or errors
     */
    public function getData(Request $request)
    {
        $votingTour = VotingTour::getLatestTour();
        if (empty($votingTour)) {
            return $this->errorResponse(__('custom.org_not_found'));
        }

        $data = $request->all();

        $validator = \Validator::make($data, [
            'org_id' => 'required_without:eik|nullable|int|exists:organisations,id|digits_between:1,10',
            'eik'    => 'required_without:org_id|nullable|exists:organisations,eik,voting_tour_id,'. $votingTour->id .'|digits_between:1,19',
        ]);

        if (!$validator->fails()) {
            try {
                if (isset($data['org_id'])) {
                    $orgKey = 'id';
                    $orgVal = $data['org_id'];
                } else {
                    $orgKey = 'eik';
                    $orgVal = $data['eik'];
                }
                $organisation = Organisation::where($orgKey, $orgVal)->where('voting_tour_id', $votingTour->id)->first();

                if ($organisation) {
                    $result = [
                        'id'             => $organisation->id,
                        'eik'            => $organisation->eik,
                        'voting_tour_id' => $organisation->voting_tour_id,
                        'name'           => $organisation->name,
                        'address'        => $organisation->address,
                        'representative' => $organisation->representative,
                        'email'          => $organisation->email,
                        'in_ap'          => $organisation->in_ap,
                        'is_candidate'   => $organisation->is_candidate,
                        'description'    => $organisation->description,
                        'references'     => $organisation->references,
                        'status'         => $organisation->status,
                        'status_hint'    => $organisation->status_hint,
                        'created_at'     => $organisation->created_at->toDateTimeString(),
                        'updated_at'     => isset($organisation->updated_at) ? $organisation->updated_at->toDateTimeString() : null,
                        'created_by'     => isset($organisation->created_by) ? $organisation->created_by : null,
                        'updated_by'     => isset($organisation->updated_by) ? $organisation->updated_by : null,
                    ];

                    return $this->successResponse($result);
                }
            } catch (QueryException $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.get_org_fail'));
            }
        }

        return $this->errorResponse(__('custom.org_not_found'), $validator->errors()->messages());
    }

    /**
     * List organisation files
     *
     * @param integer org_id - required
     *
     * @return json - response with status code and list of organisation files or errors
     */
    public function getFileList(Request $request)
    {
        $orgId = $request->get('org_id', null);

        $validator = \Validator::make(['org_id' => $orgId], [
            'org_id' => 'required|int|exists:organisations,id|digits_between:1,10',
        ]);

        if (!$validator->fails()) {
            $results = [];

            try {
                $votingTour = VotingTour::getLatestTour();
                if (!empty($votingTour)) {
                    $files = File::select('id', 'name', 'mime_type', 'created_at')
                                ->where('org_id', $orgId)
                                ->where('voting_tour_id', $votingTour->id)
                                ->orderBy('id')->get();

                    foreach ($files as $file) {
                        $results[] = [
                            'id'         => $file->id,
                            'name'       => $file->name,
                            'mime_type'  => $file->mime_type,
                            'created_at' => $file->created_at,
                        ];
                    }
                }

                return $this->successResponse(['files' => $results]);
            } catch (QueryException $e) {
                logger()->error($e->getMessage());
            }
        }

        return $this->errorResponse(__('custom.list_org_files_fail'), $validator->errors()->messages());
    }

    /**
     * List statuses
     *
     * @param none
     *
     * @return json - response with status code and list of statuses or errors
     */
    public function listStatuses(Request $request)
    {
        $results = [];

        $statuses = Organisation::getStatuses();

        foreach ($statuses as $statusId => $statusName) {
            $results[] = [
                'id'   => $statusId,
                'name' => $statusName,
            ];
        }

        if ($results) {
            return $this->successResponse($results);
        } else {
            return $this->errorResponse('custom.status_list_not_found');
        }
    }

    /**
     * List candidate statuses
     *
     * @param none
     *
     * @return json - response with status code and list of candidate statuses or errors
     */
    public function listCandidateStatuses(Request $request)
    {
        $results = [];

        $statuses = Organisation::getCandidateStatuses();

        foreach ($statuses as $statusId => $statusName) {
            $results[] = [
                'id'   => $statusId,
                'name' => $statusName,
            ];
        }

        if ($results) {
            return $this->successResponse($results);
        } else {
            return $this->errorResponse('custom.status_list_not_found');
        }
    }
}
