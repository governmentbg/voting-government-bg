<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseAdminController;
use App\Http\Controllers\Api\VotingTourController as ApiVotingTour;
use App\Http\Controllers\Api\MessageController as ApiMessages;
use App\Message;

class MessagesController extends BaseAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->addBreadcrumb(__('breadcrumbs.start'), route('admin.org_list'));
    }
    
    public function list(Request $request)
    {
        $filters = request('filters', []);
        $filters['parent_id'] = null;
        
        $field = request('orderBy', 'created_at');
        $orderType = request('order', 'DESC');

        list($votingTour, $tourErrors) = api_result(ApiVotingTour::class, 'getLatestVotingTour');
        
        list($messages, $errors) = api_result(ApiMessages::class, 'search', [
            'filters'     => $filters,
            'order_field' => $field,
            'order_type'  => $orderType,
        ]);
        
        $this->addBreadcrumb(__('breadcrumbs.message_list'), '');
                
        return view('admin.messages_list', [
            'tourData' => $votingTour,
            'statuses' => Message::getStatuses(),
            'filters'  => $filters,
            'messages' => $this->paginate($messages),
        ])->withErrors($errors);
    }
    
    public function view($id)
    {
        $this->addBreadcrumb(__('custom.request_for_resolution'), '');
        
        $parent = Message::where('id', $id)->first()->toArray();
             
        list($messages, $errors) = api_result(ApiMessages::class, 'listByParent', [
            'parent_id' => $id,
        ]);
        
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors);
        }
               
        array_unshift($messages, (object)$parent); //add parent message to the begging of the array
        
        //mark as read
        foreach($messages as $key => $message) {
            if($message->sender_org_id && !$message->read){
                list($res, $errors) = api_result(ApiMessages::class, 'markAsRead', ['message_id' => $message->id]);
            }
        }

        return view('admin.request', [
            'messages' => $messages,
            'parent' => (object)$parent
        ]);
    }
    
    public function send(Request $request, $id)
    {      
        $data = $request->all();
        
        $data['parent_id'] = $id;
        $data['body'] = $data['new_message'];
        $data['sender_user_id'] = auth()->guard('backend')->user()->id;

        list($result, $errors) = api_result(ApiMessages::class, 'sendMessageToOrg', $data);
        
        if(!empty($errors)){
            return redirect()->back()->withErrors($errors);
        }
        
        return redirect()->route('admin.messages', ['id' => $id]);
    }
}
