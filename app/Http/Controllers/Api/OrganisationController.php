<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Organisation;
use App\VotingTour;
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
     * @param string org_data[phone] - required
     * @param boolean org_data[in_av] - optional
     * @param boolean org_data[is_candidate] - optional
     * @param string org_data[description] - required if is_candidate
     * @param string org_data[references] - optional
     * @param integer org_data[status] - optional
     * @param integer org_data[status_hint] - optional
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
                'email'             => 'required|email',
                'phone'             => 'required|string|max:40',
                'in_av'             => 'bool',
                'is_candidate'      => 'bool',
                'description'       => 'required_if:is_candidate,'. Organisation::IS_CANDIDATE_TRUE .'|nullable|max:8000',
                'references'        => 'nullable|max:8000',
                'status'            => 'nullable|int|in:'. implode(',', array_keys(Organisation::getStatuses())),
                'status_hint'       => 'nullable|int|in:'. implode(',', array_keys(Organisation::getStatusHints())),
                'files'             => 'required_if:in_av,'. Organisation::IN_AV_TRUE .'|nullable|array',
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
                    $organisation->phone = $data['phone'];
                    if (isset($data['in_av'])) {
                        $organisation->in_av = $data['in_av'];
                    } else {
                        $organisation->in_av = Organisation::IN_AV_FALSE;
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
                    } elseif ($organisation->is_candidate == Organisation::IS_CANDIDATE_TRUE) {
                        $organisation->references = '';
                    }
                    if (isset($data['status'])) {
                        $organisation->status = $data['status'];
                    } else {
                        $organisation->status = Organisation::STATUS_NEW;
                    }
                    if (isset($data['status_hint'])) {
                        $organisation->status_hint = $data['status_hint'];
                    } else {
                        $organisation->status_hint = Organisation::STATUS_HINT_NONE;
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
                } catch (\Exception $e) {
                    DB::rollback();
                    logger()->error($e->getMessage());
                    return $this->errorResponse(__('custom.reg_org_fail'));
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
     * @param string org_data[phone] - optional
     * @param boolean org_data[in_av] - optional
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
                'email'          => 'string|email',
                'phone'          => 'string|max:40',
                'in_av'          => 'bool',
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
                        $isCandidate = (isset($data['is_candidate']) ? $data['is_candidate'] : $organisation->is_candidate);
                        $description = (isset($data['description']) ? $data['description'] : $organisation->description);
                        if ($isCandidate == Organisation::IS_CANDIDATE_TRUE && empty($description)) {
                            return $this->errorResponse(__('custom.edit_org_fail'), [__('custom.org_descr_required')]);
                        }

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
                        if (!empty($data['email'])) {
                            $orgData['email'] = $data['email'];
                        }
                        if (!empty($data['phone'])) {
                            $orgData['phone'] = $data['phone'];
                        }
                        if (isset($data['in_av'])) {
                            $orgData['in_av'] = $data['in_av'];
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
                } catch (\Exception $e) {
                    DB::rollback();
                    logger()->error($e->getMessage());
                    return $this->errorResponse(__('custom.edit_org_fail'));
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
     * @param boolean filters[in_av] - optional
     * @param boolean filters[is_candidate] - optional
     * @param array filters[statuses] - optional
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
        $votingTour = VotingTour::getLatestTour();
        if (empty($votingTour)) {
            return $this->errorResponse(__('custom.voting_tour_not_found'));
        }

        $filters = $request->get('filters', []);
        $orderField = $request->get('order_field', Organisation::DEFAULT_ORDER_FIELD);
        $orderType = strtoupper($request->get('order_type', Organisation::DEFAULT_ORDER_TYPE));
        $page = $request->get('page_number');
        $request->request->add(['page' => $page]);

        $validator = \Validator::make($filters, [
            'eik'           => 'nullable|digits_between:1,19',
            'name'          => 'nullable|string|max:255',
            'email'         => 'nullable|string|max:255',
            'in_av'         => 'nullable|bool',
            'is_candidate'  => 'nullable|bool',
            'statuses'      => 'nullable|array',
            'statuses.*'    => 'nullable|int|in:'. implode(',', array_keys(Organisation::getStatuses())),
            'reg_date_from' => 'nullable|date|date_format:Y-m-d',
            'reg_date_to'   => 'nullable|date|date_format:Y-m-d'. (!empty($filters['reg_date_from']) ? '|after_or_equal:reg_date_from' : ''),
        ]);

        if (!$validator->fails()) {
            if (!in_array($orderField, Organisation::getOrderColumns()) || !in_array($orderType, ['ASC', 'DESC'])) {
                return $this->errorResponse(__('custom.invalid_sort_field'));
            }

            try {
                $organisations = Organisation::where('voting_tour_id', $votingTour->id);
                if (isset($filters['eik'])) {
                    $organisations->where('eik', $filters['eik']);
                }
                if (isset($filters['name'])) {
                    $organisations->where('name', 'LIKE', '%'. trim($filters['name']) .'%');
                }
                if (isset($filters['email'])) {
                    $organisations->where('email', 'LIKE', '%'. trim($filters['email']) .'%');
                }
                if (isset($filters['in_av'])) {
                    $organisations->where('in_av', $filters['in_av']);
                }
                if (isset($filters['is_candidate'])) {
                    $organisations->where('is_candidate', $filters['is_candidate']);
                }
                if (isset($filters['statuses'])) {
                    $organisations->whereIn('status', $filters['statuses']);
                }
                if (isset($filters['reg_date_from'])) {
                    $organisations->where('created_at', '>=', $filters['reg_date_from'] .' 00:00:00');
                }
                if (isset($filters['reg_date_to'])) {
                    $organisations->where('created_at', '<=', $filters['reg_date_to'] .' 23:59:59');
                }

                $count = $organisations->count();

                $organisations->orderBy($orderField, $orderType)->paginate();

                return $this->successResponse(['organisations' => $organisations->get(), 'total_records' => $count], true);
            } catch (\Exception $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.list_org_fail'), $e->getMessage());
            }
        }

        return $this->errorResponse(__('custom.org_list_not_found'), $validator->errors()->messages());
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
            return $this->errorResponse(__('custom.voting_tour_not_found'));
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
                    return $this->successResponse($organisation);
                }
            } catch (\Exception $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.get_org_fail'), $e->getMessage());
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
        $votingTour = VotingTour::getLatestTour();
        if (empty($votingTour)) {
            return $this->errorResponse(__('custom.voting_tour_not_found'));
        }

        $orgId = $request->get('org_id', null);

        $validator = \Validator::make(['org_id' => $orgId], [
            'org_id' => 'required|int|exists:organisations,id|digits_between:1,10',
        ]);

        if (!$validator->fails()) {
            try {
                $files = File::select('id', 'name', 'mime_type', 'created_at')
                            ->where('org_id', $orgId)
                            ->where('voting_tour_id', $votingTour->id)
                            ->orderBy('id')->get();

                return $this->successResponse($files);
            } catch (\Exception $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.list_org_files_fail'), $e->getMessage());
            }
        }

        return $this->errorResponse(__('custom.org_files_not_found'), $validator->errors()->messages());
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
