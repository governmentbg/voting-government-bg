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
     * @param int    $org_id      - required
     * @param string $order_field - optional
     * @param string $order_type  - optional
     *
     * @return json $response - response with status and message collection if successful
     */
    public function listByOrg(Request $request)
    {
        $orgId = $request->get('org_id');
        $field = $request->get('order_field', 'created_at');
        $order = $request->get('order_type', 'DESC');
        $page = $request->get('page_number');
        $request->request->add(['page' => $page]);

        $validator = \Validator::make(['org_id' => $orgId], [
            'org_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(__('custom.validation_error'), $validator->errors()->messages());
        }

        try {
            $votingTour = VotingTour::getLatestTour();
            if (empty($votingTour)) {
                return $this->errorResponse(__('custom.voting_tour_not_found'));
            }

            $messages = Message::where(function($query) use ($orgId) {
                            $query->where('sender_org_id', $orgId)->orWhere('recipient_org_id', $orgId);
                        })->where('voting_tour_id', $votingTour->id)->sort($field, $order)->paginate();

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
            return $this->errorResponse(__('custom.message_not_found'), $e->getMessage());
        }
    }

    /**
     * Get all messages for specific conversation and order them if needed.
     *
     * @param int    $parent_id   - required
     * @param string $order_field - optional
     * @param string $order_type  - optional
     *
     * @return json $response - response with status and message collection if successful
     */
    public function listByParent(Request $request)
    {
        $parentId = $request->get('parent_id');
        $field = $request->get('order_field', 'created_at');
        $order = $request->get('order_type', 'ASC');

        $validator = \Validator::make(['parent_id' => $parentId], [
            'parent_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(__('custom.validation_error'), $validator->errors()->messages());
        }

        try {
            $votingTour = VotingTour::getLatestTour();
            if (empty($votingTour)) {
                return $this->errorResponse(__('custom.voting_tour_not_found'));
            }

            $messages = Message::where(function($query) use ($parentId) {
                            $query->where('parent_id', $parentId)->orWhere('id', $parentId);
                        })->where('voting_tour_id', $votingTour->id)->with(['files' => function($query) {
                            $query->select('id', 'name', 'mime_type', 'message_id', 'org_id', 'created_at');
                        }])->sort($field, $order)->get();

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
            return $this->errorResponse(__('custom.message_not_found'), $e->getMessage());
        }
    }

    /**
     * Get filtered messages and order them if needed.
     *
     * @param array  $filters     - optional
     * @param string $order_field - optional
     * @param string $order_type  - optional
     *
     * @return json $response - response with status and message collection if successful
     */
    public function search(Request $request)
    {
        $filters = $request->get('filters', []);
        $field = $request->get('order_field');
        $order = $request->get('order_type', 'ASC');
        $page = $request->get('page_number');
        $request->request->add(['page' => $page]);

        $validator = \Validator::make($filters, [
            'date_from' => 'nullable|date|date_format:Y-m-d',
            'date_to'   => 'nullable|date|date_format:Y-m-d',
            'subject'   => 'nullable|string|max:255',
            'org_name'  => 'nullable|string|max:255',
            'status'    => 'nullable|int|in:' . implode(',', array_keys(Message::getStatuses())),
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(__('custom.invalid_sort_field'), $validator->errors()->messages());
        }

        try {
            $votingTour = VotingTour::getLatestTour();
            if (empty($votingTour)) {
                return $this->errorResponse(__('custom.voting_tour_not_found'));
            }

            $messages = Message::where('voting_tour_id', $votingTour->id)->search($filters, $field, $order)->paginate();

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
            return $this->errorResponse(__('custom.message_not_found'), $e->getMessage());
        }
    }

    /**
     * Mark message as read.
     *
     * @param int $message_id - required
     *
     * @return json $response - response with status and message id if successful
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
     * Get array with message read statuses.
     * @param  Request $request
     * @return json
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
     * @param int    $sender_user_id - required
     * @param int    $recipient_org  - required
     * @param string $subject        - required
     * @param string $body           - required
     * @param int    $parent_id      - required
     *
     * @return json $response - response with status and  id of the created message if successful
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
            return $this->errorResponse(__('custom.message_not_send'), $e->getMessage());
        }
    }

    /**
     * Send message from organisation.
     *
     * @param int    $sender_org_id - required
     * @param array  $files         - optional
     * @param string $subject       - required
     * @param string $body          - required
     * @param int    $parent_id     - required
     *
     * @return json $response - response with status and id of the created message if successful
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
            return $this->errorResponse(__('custom.message_not_send'), $e->getMessage());
        }
    }

    /**
     * Send message to organisations.
     *
     * @param array $query - required
     *
     * @return json $response - response with status
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
            return $this->errorResponse(__('custom.message_not_send'), $e->getMessage());
        }
    }
}
