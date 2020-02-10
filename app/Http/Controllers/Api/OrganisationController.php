<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Organisation;
use App\VotingTour;
use App\File;
use App\ActionsHistory;

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
     * @param string org_data[description] - required if is_candidate is true
     * @param string org_data[references] - optional
     * @param integer org_data[status] - optional
     * @param integer org_data[status_hint] - optional
     * @param array files - required unless in_av is true
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

            $validator = \Validator::make(['org_data' => $data], [
                'org_data' => 'required|array',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(__('custom.reg_org_fail'), $validator->errors()->messages());
            }

            $data['files'] = $request->get('files', []);

            $validator = \Validator::make($data, [
                'eik'               => 'required|digits_between:1,19|unique:organisations,eik,NULL,id,voting_tour_id,'. $votingTour->id,
                'name'              => 'required|string|max:255',
                'address'           => 'required|string|max:512',
                'representative'    => 'required|string|max:512',
                'email'             => 'required|email|unique:organisations,email,NULL,id,voting_tour_id,' . $votingTour->id,
                'phone'             => 'required|string|max:40',
                'in_av'             => 'nullable|bool',
                'is_candidate'      => 'nullable|bool',
                'description'       => 'nullable|string|max:8000',
                'references'        => 'nullable|string|max:8000',
                'status'            => 'nullable|int|in:'. implode(',', array_keys(Organisation::getStatuses())),
                'status_hint'       => 'nullable|int|in:'. implode(',', array_keys(Organisation::getStatusHints())),
                'files'             => 'nullable|array',
                'files.*.name'      => 'required|string|max:255',
                'files.*.mime_type' => 'required|string|in:'. implode(',', File::getSupportedFormats()),
                'files.*.data'      => 'required|string|max:'. File::MAX_SIZE,
            ]);

            $validator->after(function ($validator) use ($data) {
                if (isset($data['is_candidate']) && $data['is_candidate'] == Organisation::IS_CANDIDATE_TRUE) {
                    if (!isset($data['description']) || trim($data['description']) == '') {
                        $validator->errors()->add('description', __('custom.org_descr_required'));
                    }
                }

                if (!$validator->errors()->has('description') && !empty($data['description'])) {
                    $words = preg_split( '|\s+|s', $data['description']);
                    if (($words = count($words)) > 500) {
                        $validator->errors()->add('description', __('custom.org_descr_words_exceeded', ['words' => $words]));
                    }
                }

                if (!$validator->errors()->has('files')) {
                    if (!(isset($data['in_av']) && $data['in_av'] == Organisation::IN_AV_TRUE) && empty($data['files'])) {
                        $validator->errors()->add('files', __('custom.org_files_required'));
                    }
                }
            });

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

                    $logData = [
                        'module' => ActionsHistory::ORGANISATIONS,
                        'action' => ActionsHistory::TYPE_ADD,
                        'object' => $organisation->id,
                        'actor'  => $organisation->created_by
                    ];

                    ActionsHistory::add($logData);

                    foreach ($data['files'] as $newFile) {
                        $file = new File;
                        $file->name = $newFile['name'];
                        $file->data = $newFile['data'];
                        $file->mime_type = $newFile['mime_type'];
                        $file->org_id = $organisation->id;
                        $file->voting_tour_id = $votingTour->id;

                        $file->save();
                    }

                    if (!empty($data['files'])) {
                        $logData = [
                            'module' => ActionsHistory::ORGANISATIONS_FILES,
                            'action' => ActionsHistory::TYPE_ADD,
                            'object' => $organisation->id,
                            'actor'  => $organisation->created_by
                        ];

                        ActionsHistory::add($logData);
                    }

                    DB::commit();

                    return $this->successResponse(['id' => $organisation->id], true);
                } catch (\Exception $e) {
                    DB::rollback();
                    logger()->error($e->getMessage());
                    return $this->errorResponse(__('custom.reg_org_fail'), __('custom.internal_server_error'));
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
     * @param string org_data[description] - required if is_candidate is true
     * @param string org_data[references] - optional
     * @param integer org_data[status] - optional
     * @param integer org_data[status_hint] - optional
     *
     * @return json - response with status code and success or errors
     */
    public function edit(Request $request)
    {
        $votingTour = VotingTour::getLatestTour();

        if (!empty($votingTour) && !array_key_exists($votingTour->status, VotingTour::getNonEditableStatuses())) {
            $orgId = $request->get('org_id', null);
            $data = $request->get('org_data', []);

            $validator = \Validator::make(['org_id' => $orgId, 'org_data' => $data], [
                'org_id'   => 'required|int|exists:organisations,id',
                'org_data' => 'required|array',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(__('custom.edit_org_fail'), $validator->errors()->messages());
            }

            $validator = \Validator::make($data, [
                'name'           => 'nullable|string|max:255',
                'address'        => 'nullable|string|max:512',
                'representative' => 'nullable|string|max:512',
                'email'          => 'nullable|string|email|unique:organisations,email,NULL,id,voting_tour_id,' . $votingTour->id,
                'phone'          => 'nullable|string|max:40',
                'in_av'          => 'nullable|bool',
                'is_candidate'   => 'nullable|bool',
                'description'    => 'nullable|string|max:8000',
                'references'     => 'nullable|string|max:8000',
                'status'         => 'nullable|int|in:'. implode(',', array_keys(Organisation::getStatuses())),
                'status_hint'    => 'nullable|int|in:'. implode(',', array_keys(Organisation::getStatusHints())),
            ]);

            $validator->after(function ($validator) use ($data) {
                if (isset($data['is_candidate']) && $data['is_candidate'] == Organisation::IS_CANDIDATE_TRUE) {
                    if (!isset($data['description']) || trim($data['description']) == '') {
                        $validator->errors()->add('description', __('custom.org_descr_required'));
                    }
                }

                if (!$validator->errors()->has('description') && !empty($data['description'])) {
                    $words = preg_split( '|\s+|s', $data['description']);
                    if (($words = count($words)) > 500) {
                        $validator->errors()->add('description', __('custom.org_descr_words_exceeded', ['words' => $words]));
                    }
                }
            });

            if (!$validator->fails()) {
                try {
                    DB::beginTransaction();

                    $organisation = Organisation::where('id', $orgId)->where('voting_tour_id', $votingTour->id)->first();

                    if ($organisation) {
                        $isCandidate = (isset($data['is_candidate']) ? $data['is_candidate'] : $organisation->is_candidate);

                        if ($isCandidate == Organisation::IS_CANDIDATE_TRUE) {
                            $description = (array_key_exists('description', $data) ? $data['description'] : $organisation->description);
                            if (trim($description) == '') {
                                return $this->errorResponse(__('custom.edit_org_fail'), ['description' => [__('custom.org_descr_required')]]);
                            }

                            $references = (array_key_exists('references', $data) ? $data['references'] : $organisation->references);
                            if (trim($references) == '') {
                                $data['references'] = '';
                            }
                        }

                        if (isset($data['status']) && $data['status'] != $organisation->status) {
                            if ($organisation->status == Organisation::STATUS_DECLASSED) {
                                return $this->errorResponse(__('custom.org_status_update_not_allowed'));
                            }

                            if ($votingTour->status != VotingTour::STATUS_RANKING ||
                                !in_array($organisation->status, Organisation::getApprovedCandidateStatuses())
                            ) {
                                if ($data['status'] == Organisation::STATUS_DECLASSED) {
                                    return $this->errorResponse(__('custom.declass_not_allowed'));
                                }

                                if ($data['status'] == Organisation::STATUS_BALLOTAGE) {
                                    return $this->errorResponse(__('custom.ballotage_not_allowed'));
                                }
                            }

                            if ($votingTour->status == VotingTour::STATUS_RANKING &&
                                in_array($organisation->status, Organisation::getApprovedCandidateStatuses()) &&
                                in_array($data['status'], Organisation::getRejectionStatuses())
                            ) {
                                return $this->errorResponse(__('custom.rejection_not_allowed', ['status' => Organisation::getStatuses()[$organisation->status]]));
                            }
                        }

                        $orgData = [];
                        if (isset($data['name']) && $data['name'] != '') {
                            $orgData['name'] = $data['name'];
                        }
                        if (isset($data['address']) && $data['address'] != '') {
                            $orgData['address'] = $data['address'];
                        }
                        if (isset($data['representative']) && $data['representative'] != '') {
                            $orgData['representative'] = $data['representative'];
                        }
                        if (isset($data['email']) && $data['email'] != '') {
                            $orgData['email'] = $data['email'];
                        }
                        if (isset($data['phone']) && $data['phone'] != '') {
                            $orgData['phone'] = $data['phone'];
                        }
                        if (isset($data['in_av'])) {
                            $orgData['in_av'] = $data['in_av'];
                        }
                        if (isset($data['is_candidate'])) {
                            $orgData['is_candidate'] = $data['is_candidate'];
                        }
                        if (array_key_exists('description', $data)) {
                            $orgData['description'] = $data['description'];
                        }
                        if (array_key_exists('references', $data)) {
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

                        if (\Auth::user()) {
                            $logData = [
                                'module' => ActionsHistory::ORGANISATIONS,
                                'action' => ActionsHistory::TYPE_MOD,
                                'object' => $organisation->id
                            ];

                            ActionsHistory::add($logData);
                        }

                        return $this->successResponse();
                    } else {
                        return $this->errorResponse(__('custom.org_not_found'));
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    logger()->error($e->getMessage());
                    return $this->errorResponse(__('custom.edit_org_fail'), __('custom.internal_server_error'));
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
     * @param string filters[tour_id] - optional
     * @param boolean filters[only_main_fields] - optional
     * @param string order_field - optional
     * @param string order_type - optional
     * @param boolean with_pagination - optional
     * @param integer page_number - optional
     *
     * @return json - response with status code and list of organisations or errors
     */
    public function search(Request $request)
    {
        $rules = [
            'filters'         => 'nullable|array',
            'order_field'     => 'nullable|string|in:'. implode(',', Organisation::ALLOWED_ORDER_FIELDS),
            'order_type'      => 'nullable|string|in:'. implode(',', Organisation::ALLOWED_ORDER_TYPES),
            'with_pagination' => 'nullable|bool',
            'page_number'     => 'nullable|int|min:1',
        ];

        $data = $request->only(array_keys($rules));
        $data['order_type'] = isset($data['order_type']) ? strtoupper($data['order_type']) : null;

        $validator = \Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->errorResponse(__('custom.validation_error'), $validator->errors()->messages());
        }

        $filters = isset($data['filters']) ? $data['filters'] : [];
        $orderField = isset($data['order_field']) ? $data['order_field'] : Organisation::DEFAULT_ORDER_FIELD;
        $orderType = isset($data['order_type']) ? $data['order_type'] : Organisation::DEFAULT_ORDER_TYPE;
        $withPagination = isset($data['with_pagination']) ? $data['with_pagination'] : false;
        $page = isset($data['page_number']) ? $data['page_number'] : null;

        $validator = \Validator::make($filters, [
            'eik'              => 'nullable|digits_between:1,19',
            'name'             => 'nullable|string|max:255',
            'email'            => 'nullable|string|max:255',
            'in_av'            => 'nullable|bool',
            'is_candidate'     => 'nullable|bool',
            'statuses'         => 'nullable|array',
            'statuses.*'       => 'nullable|int|in:'. implode(',', array_keys(Organisation::getStatuses())),
            'reg_date_from'    => 'nullable|date|date_format:Y-m-d',
            'reg_date_to'      => 'nullable|date|date_format:Y-m-d'. (!empty($filters['reg_date_from']) ? '|after_or_equal:reg_date_from' : ''),
            'tour_id'          => 'nullable|int|exists:voting_tour,id',
            'only_main_fields' => 'nullable|bool',
        ]);

        if (!$validator->fails()) {
            try {
                if (!empty($filters['tour_id'])) {
                    $votingTour = VotingTour::where('id', $filters['tour_id'])->first();
                } else {
                    $votingTour = VotingTour::getLatestTour();
                }
                if (empty($votingTour)) {
                    return $this->errorResponse(__('custom.voting_tour_not_found'));
                }

                if (isset($filters['only_main_fields']) && $filters['only_main_fields']) {
                    $fields = ['id', 'eik', 'name', 'is_candidate', 'created_at'];
                } else {
                    $fields = '*';
                }

                $organisations = Organisation::select($fields)->where('voting_tour_id', $votingTour->id);
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
                $organisations->orderBy($orderField, $orderType);

                if ($withPagination) {
                    $request->request->add(['page' => $page]);
                    $organisations = $organisations->paginate();
                } else {
                    $organisations = $organisations->get();
                }

                if (\Auth::user()) {
                    $logData = [
                        'module' => ActionsHistory::ORGANISATIONS,
                        'action' => ActionsHistory::TYPE_SEE
                    ];

                    ActionsHistory::add($logData);
                }

                return $this->successResponse($organisations);
            } catch (\Exception $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.list_org_fail'), __('custom.internal_server_error'));
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
            'org_id' => 'required_without:eik|nullable|int|exists:organisations,id',
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
                    if (\Auth::user()) {
                        $logData = [
                            'module' => ActionsHistory::ORGANISATIONS,
                            'action' => ActionsHistory::TYPE_SEE,
                            'object' => $organisation->id
                        ];

                        ActionsHistory::add($logData);
                    }

                    return $this->successResponse($organisation);
                }
            } catch (\Exception $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.get_org_fail'), __('custom.internal_server_error'));
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
            'org_id' => 'required|int|exists:organisations,id',
        ]);

        if (!$validator->fails()) {
            try {
                $files = File::select('id', 'name', 'mime_type', 'created_at')
                            ->where('org_id', $orgId)
                            ->where('voting_tour_id', $votingTour->id)
                            ->orderBy('id')->get();

                if (\Auth::user()) {
                    $logData = [
                        'module' => ActionsHistory::ORGANISATIONS_FILES,
                        'action' => ActionsHistory::TYPE_SEE,
                        'object' => $orgId
                    ];

                    ActionsHistory::add($logData);
                }

                return $this->successResponse($files);
            } catch (\Exception $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.list_org_files_fail'), __('custom.internal_server_error'));
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

    /**
     * List status hints
     *
     * @param none
     *
     * @return json - response with status code and list of status hints or errors
     */
    public function listStatusHints(Request $request)
    {
        $results = [];

        $statusHints = Organisation::getStatusHints();

        foreach ($statusHints as $statusHintId => $statusHintName) {
            $results[] = [
                'id'   => $statusHintId,
                'name' => $statusHintName,
            ];
        }

        if ($results) {
            return $this->successResponse($results);
        } else {
            return $this->errorResponse('custom.status_hint_list_not_found');
        }
    }
}
