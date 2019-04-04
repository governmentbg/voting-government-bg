<?php

namespace App\Http\Controllers\Api;

use App\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;
use App\File;
use App\VotingTour;

class MessageController extends ApiController
{
    /**
     * Get all messages send by specific organisation and order them if needed.
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
        $field = $request->get('order_field');
        $order = $request->get('order_type', 'ASC');
        $page = $request->get('page_number');
        $request->request->add(['page' => $page]);
        
        $validator = \Validator::make(['org_id' => $orgId], [
            'org_id' => 'required|integer',
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse(__('custom.validation_error'), $validator->errors()->messages());
        }
        
        try {
            $messages = Message::where('sender_org_id', $orgId)->sort($field, $order)->paginate();

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
    public function listByParentId(Request $request)
    {
        $orgId = $request->get('parent_id');
        $field = $request->get('order_field', 'created_at');
        $order = $request->get('order_type', 'ASC');
        $page = $request->get('page_number');
        $request->request->add(['page' => $page]);
        
        $validator = \Validator::make(['parent_id' => $orgId], [
            'parent_id' => 'required|integer',
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse(__('custom.validation_error'), $validator->errors()->messages());
        }
        
        try {
            $messages = Message::where('parent_id', $orgId)->sort($field, $order)->paginate();
            
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
        $filters = $request->get('filters');
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
            $messages = Message::search($filters, $field, $order)->paginate();
            
            return $this->successResponse($messages);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            return $this->errorResponse(__('custom.message_not_found'), $e->getMessage());
        }
    }
   
    /**
     * Mark messasge as read.
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
            
            return $this->successResponse(['id' => $id], true);
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
            
            return $this->successResponse(['id' => $message->id]);
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
        
        //validate files
        $files = $request->get('files', []);
        foreach ($files as $file) {
            if (empty($file['name']) || empty($file['data']) || empty($file['mime_type'])) {
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
            }
                      
            DB::commit();
            return $this->successResponse(['id' => $message->id]);
        } catch (\Exception $e) {
            DB::rollback();
            logger()->error($e->getMessage());
            return $this->errorResponse(__('custom.message_not_send'), $e->getMessage());
        }
    }
}
