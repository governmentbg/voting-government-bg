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
            'filters' => $filters,
            'order_field' => $field,
            'order_type' => $orderType
        ]);
        
        $this->addBreadcrumb(__('breadcrumbs.message_list'), '');
                
        return view('admin.messages_list', [
            'tourData'          => $votingTour,
            'statuses'          => Message::getStatuses(),
            'filters'           => $filters,
            'messages'          => $this->paginate($messages)
        ])->withErrors($errors);
    }
}
