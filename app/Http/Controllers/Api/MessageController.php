<?php

namespace App\Http\Controllers\Api;

use App\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;
use App\File;
use App\VotingTour;
use App\ActionsHistory;
use App\Organisation;

class MessageController extends ApiController
{
    /**
     * Get all messages sent by specific organisation and order them if needed.
     *
     * @param integer org_id - required
     * @param string order_field - optional
     * @param string order_type - optional
     * @param integer page_number - optional
     *
     * @return json - response with status and message collection if successful
     */
    public function listByOrg(Request $request)
    {
        $rules = [
            'org_id'      => 'required|integer',
            'order_field' => 'nullable|string|in:'. implode(',', Message::ALLOWED_ORDER_FIELDS),
            'order_type'  => 'nullable|string|in:'. implode(',', Message::ALLOWED_ORDER_TYPES),
            'page_number' => 'nullable|int|min:1',
        ];

        $data = $request->only(array_keys($rules));
        $data['order_type'] = isset($data['order_type']) ? strtoupper($data['order_type']) : null;

        $validator = \Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->errorResponse(__('custom.validation_error'), $validator->errors()->messages());
        }

        $orgId = $data['org_id'];
        $orderField = isset($data['order_field']) ? $data['order_field'] : Message::DEFAULT_ORDER_FIELD;
        $orderType = isset($data['order_type']) ? $data['order_type'] : Message::DEFAULT_ORDER_TYPE;

        $page = isset($data['page_number']) ? $data['page_number'] : null;
        $request->request->add(['page' => $page]);

        try {
            $votingTour = VotingTour::getLatestTour();
            if (empty($votingTour)) {
                return $this->errorResponse(__('custom.voting_tour_not_found'));
            }

            $messages = Message::where(function($query) use ($orgId) {
                            $query->where('sender_org_id', $orgId)->orWhere('recipient_org_id', $orgId);
                        })->where('voting_tour_id', $votingTour->id)->sort($orderField, $orderType)->paginate();

            if (\Auth::user()) {
                $logData = [
                    'module' => ActionsHistory::ORGANISATION_MESSAGES,
                    'action' => ActionsHistory::TYPE_SEE,
                    'object' => $orgId
                ];

                ActionsHistory::add($logData);
            }

            return $this->successResponse($messages);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            return $this->errorResponse(__('custom.message_not_found'), __('custom.internal_server_error'));
        }
    }

    /**
     * Get all messages for specific conversation and order them if needed.
     *
     * @param integer parent_id - required
     * @param string order_field - optional
     * @param string order_type - optional
     *
     * @return json - response with status and message collection if successful
     */
    public function listByParent(Request $request)
    {
        $rules = [
            'parent_id'   => 'required|integer',
            'order_field' => 'nullable|string|in:'. implode(',', Message::ALLOWED_ORDER_FIELDS),
            'order_type'  => 'nullable|string|in:'. implode(',', Message::ALLOWED_ORDER_TYPES),
        ];

        $data = $request->only(array_keys($rules));
        $data['order_type'] = isset($data['order_type']) ? strtoupper($data['order_type']) : null;

        $validator = \Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->errorResponse(__('custom.validation_error'), $validator->errors()->messages());
        }

        $parentId = $data['parent_id'];
        $orderField = isset($data['order_field']) ? $data['order_field'] : Message::DEFAULT_ORDER_FIELD;
        $orderType = isset($data['order_type']) ? $data['order_type'] : 'ASC';

        try {
            $votingTour = VotingTour::getLatestTour();
            if (empty($votingTour)) {
                return $this->errorResponse(__('custom.voting_tour_not_found'));
            }

            $messages = Message::where(function($query) use ($parentId) {
                            $query->where('parent_id', $parentId)->orWhere('id', $parentId);
                        })->where('voting_tour_id', $votingTour->id)->with(['files' => function($query) {
                            $query->select('id', 'name', 'mime_type', 'message_id', 'org_id', 'created_at');
                        }])->sort($orderField, $orderType)->get();

            if (\Auth::user()) {
                $logData = [
                    'module' => ActionsHistory::MESSAGES,
                    'action' => ActionsHistory::TYPE_SEE,
                    'object' => $parentId
                ];

                ActionsHistory::add($logData);
            }

            return $this->successResponse($messages);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            return $this->errorResponse(__('custom.message_not_found'), __('custom.internal_server_error'));
        }
    }

    /**
     * Get filtered messages and order them if needed.
     *
     * @param array filters - optional
     * @param string order_field - optional
     * @param string order_type - optional
     * @param integer page_number - optional
     *
     * @return json - response with status and message collection if successful
     */
    public function search(Request $request)
    {
        $rules = [
            'filters'     => 'nullable|array',
            'order_field' => 'nullable|string|in:'. implode(',', Message::ALLOWED_ORDER_FIELDS),
            'order_type'  => 'nullable|string|in:'. implode(',', Message::ALLOWED_ORDER_TYPES),
            'page_number' => 'nullable|int|min:1',
        ];

        $data = $request->only(array_keys($rules));
        $data['order_type'] = isset($data['order_type']) ? strtoupper($data['order_type']) : null;

        $validator = \Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->errorResponse(__('custom.validation_error'), $validator->errors()->messages());
        }

        $filters = isset($data['filters']) ? $data['filters'] : [];
        $orderField = isset($data['order_field']) ? $data['order_field'] : Message::DEFAULT_ORDER_FIELD;
        $orderType = isset($data['order_type']) ? $data['order_type'] : Message::DEFAULT_ORDER_TYPE;

        $page = isset($data['page_number']) ? $data['page_number'] : null;
        $request->request->add(['page' => $page]);

        $validator = \Validator::make($filters, [
            'date_from' => 'nullable|date|date_format:Y-m-d',
            'date_to'   => 'nullable|date|date_format:Y-m-d',
            'subject'   => 'nullable|string|max:255',
            'org_name'  => 'nullable|string|max:255',
            'status'    => 'nullable|int|in:'. implode(',', array_keys(Message::getStatuses())),
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(__('custom.validation_error'), $validator->errors()->messages());
        }

        try {
            $votingTour = VotingTour::getLatestTour();
            if (empty($votingTour)) {
                return $this->errorResponse(__('custom.voting_tour_not_found'));
            }

            $messages = Message::where('voting_tour_id', $votingTour->id)->search($filters, $orderField, $orderType)->paginate();

            if (\Auth::user()) {
                $logData = [
                    'module' => ActionsHistory::MESSAGES,
                    'action' => ActionsHistory::TYPE_SEE
                ];

                ActionsHistory::add($logData);
            }

            return $this->successResponse($messages);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            return $this->errorResponse(__('custom.message_not_found'), __('custom.internal_server_error'));
        }
    }

    /**
     * Mark message as read.
     *
     * @param integer message_id - required
     *
     * @return json - response with status and message id if successful
     */
    public function markAsRead(Request $request)
    {
        $id = $request->get('message_id');

        $message = Message::find($id);

        if ($message) {
            if (!$message->read) {
                $message->update(['read' => date('Y-m-d H:i:s')]);
            }

            return $this->successResponse();
        }

        return $this->errorResponse(__('custom.message_not_found'));
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
        $statuses = Message::getStatuses();

        foreach ($statuses as $statusId => $statusName) {
            $results[] = [
                'id'   => $statusId,
                'name' => $statusName,
            ];
        }

        if ($results) {
            return $this->successResponse($results);
        }

        return $this->errorResponse('custom.status_list_not_found');
    }

    /**
     * Send message to organisation.
     *
     * @param integer sender_user_id - required
     * @param integer recipient_org  - required
     * @param string subject - required
     * @param string body - required
     * @param integer parent_id - required
     *
     * @return json - response with status and  id of the created message if successful
     */
    public function sendMessageToOrg(Request $request)
    {
        $paramKeys = [
            'sender_user_id',
            'recipient_org_id',
            'subject',
            'body',
            'parent_id',
        ];

        $params = $request->only($paramKeys);

        $validator = \Validator::make($params, [
            'sender_user_id'   => 'int|required',
            'recipient_org_id' => 'int|required',
            'subject'          => 'required|string|max:255',
            'body'             => 'required|string',
            'parent_id'        => 'nullable|int',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(__('custom.validation_error'), $validator->errors()->messages());
        }

        $votingTour = VotingTour::getLatestTour();
        if (!$votingTour) {
            return $this->errorResponse(__('custom.message_not_send'), __('custom.voting_tour_not_found'));
        }

        $params['voting_tour_id'] = $votingTour->id;

        try {
            $message = Message::create($params);

            if (\Auth::user()) {
                $logData = [
                    'module' => ActionsHistory::MESSAGES,
                    'action' => ActionsHistory::TYPE_ADD,
                    'object' => $message->id
                ];

                ActionsHistory::add($logData);
            }

            return $this->successResponse(['id' => $message->id], true);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            return $this->errorResponse(__('custom.message_not_send'), __('custom.internal_server_error'));
        }
    }

    /**
     * Send message from organisation.
     *
     * @param integer sender_org_id - required
     * @param string subject - required
     * @param string body - required
     * @param integer parent_id - required
     * @param array files - optional
     *
     * @return json - response with status and id of the created message if successful
     */
    public function sendMessageFromOrg(Request $request)
    {
        $paramKeys = [
            'sender_org_id',
            'subject',
            'body',
            'parent_id',
        ];

        $params = $request->only($paramKeys);

        $validator = \Validator::make($params, [
            'sender_org_id' => 'int|required',
            'subject'       => 'required|string|max:255',
            'body'          => 'required|string',
            'parent_id'     => 'nullable|int',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(__('custom.validation_error'), $validator->errors()->messages());
        }

        // validate files
        $files = $request->get('files', []);
        foreach ($files as $file) {
            if (empty($file['name']) || mb_strlen($file['name']) > 255 ||
                empty($file['data']) || mb_strlen($file['name']) > File::MAX_SIZE ||
                empty($file['mime_type']) || !in_array($file['mime_type'], File::getSupportedFormats())
            ) {
                return $this->errorResponse(__('custom.validation_error'), __('custom.file_save_error'));
            }
        }

        $votingTour = VotingTour::getLatestTour();
        if (!$votingTour) {
            return $this->errorResponse(__('custom.message_not_send'), __('custom.voting_tour_not_found'));
        }
        $params['voting_tour_id'] = $votingTour->id;

        try {
            DB::beginTransaction();
            $message = Message::create($params);

            foreach ($files as $key => $file) {
                $fileModel = new File([
                    'name'           => $file['name'],
                    'data'           => $file['data'],
                    'mime_type'      => $file['mime_type'],
                    'org_id'         => $params['sender_org_id'],
                    'voting_tour_id' => $votingTour->id,
                ]);
                $message->files()->save($fileModel);

                if (\Auth::user()) {
                    $logData = [
                        'module' => ActionsHistory::FILES_MESSAGE,
                        'action' => ActionsHistory::TYPE_ADD,
                        'object' => $message->id
                    ];

                    ActionsHistory::add($logData);
                }
            }

            DB::commit();

            $logData = [
                'module' => ActionsHistory::MESSAGES,
                'action' => ActionsHistory::TYPE_ADD,
                'object' => $message->id
            ];

            ActionsHistory::add($logData);

            return $this->successResponse(['id' => $message->id], true);
        } catch (\Exception $e) {
            DB::rollback();
            logger()->error($e->getMessage());
            return $this->errorResponse(__('custom.message_not_send'), __('custom.internal_server_error'));
        }
    }

    /**
     * Send message to organisations.
     *
     * @param integer sender_user_id - required
     * @param array recipient_filters - optional
     * @param array recipient_filters[org_statuses] - optional
     * @param string subject - required
     * @param string body - required
     * @param integer parent_id - optional
     *
     * @return json - response with status
     */
    public function sendBulkMessagesToOrg(Request $request)
    {
        $batchData = $request->all();

        $validator = \Validator::make($batchData, [
            'sender_user_id'                 => 'required|int|exists:users,id',
            'recipient_filters'              => 'nullable|array',
            'recipient_filters.org_statuses' => 'nullable|array',
            'subject'                        => 'required|string',
            'body'                           => 'required|string',
            'parent_id'                      => 'nullable|int|exists:messages,id'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(__('custom.validation_error'), $validator->errors()->messages());
        }

        $organisations = Organisation::select('id');

        if (isset($batchData['recipient_filters']['org_statuses']) && !empty($batchData['recipient_filters']['org_statuses'])) {
            $organisations->whereIn('status', $batchData['recipient_filters']['org_statuses']);
        }

        $organisationsList = $organisations->where('voting_tour_id', VotingTour::getLatestTour()->id)->get();

        if (empty($organisationsList)) {
            return $this->errorResponse(__('custom.get_orgs_failure'));
        }

        $votinTourId = VotingTour::getLatestTour()->id;

        foreach ($organisationsList as $index => $singleOrg) {
            $queryString[] = [
                'sender_user_id'   => $batchData['sender_user_id'],
                'recipient_org_id' => $singleOrg->id,
                'subject'          => $batchData['subject'],
                'body'             => $batchData['body'],
                'voting_tour_id'   => $votinTourId,
                'parent_id'        => isset($batchData['parent_id']) ? $batchData['parent_id'] : null,
                'created_by'       => $batchData['sender_user_id']
            ];
        }

        if (sizeof($queryString) > Message::BATCH_SIZE) {
            $arrayChunks = array_chunk($queryString, Message::BATCH_SIZE);
        }

        try {
            if (sizeof($queryString) > Message::BATCH_SIZE) {
                foreach ($arrayChunks as $index => $chunckData) {
                    $messages = Message::insert($chunckData);
                }
            } else {
                $messages = Message::insert($queryString);
            }

            if (\Auth::user()) {
                $logData = [
                    'module' => ActionsHistory::MESSAGES,
                    'action' => ActionsHistory::TYPE_ADD
                ];

                ActionsHistory::add($logData);
            }

            return $this->successResponse();
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            return $this->errorResponse(__('custom.message_not_send'), __('custom.internal_server_error'));
        }
    }
}
