<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseAdminController;
use App\Http\Controllers\Api\MessageController as ApiMessages;
use App\Http\Controllers\Api\OrganisationController as ApiOrganisation;

class MessagesController extends BaseAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->addBreadcrumb(__('breadcrumbs.start'), route('admin.org_list'));
    }

    public function list(Request $request)
    {
        $errors = [];

        if (session()->has('errors')) {
            $errors = session()->get('errors')->messages();
        }

        list($statuses, $statusErrors) = api_result(ApiMessages::class, 'listStatuses');
        $statuses = !empty($statuses) ? collect($statuses)->pluck('name', 'id')->toArray() : [];

        $filters = request('filters', []);
        $filters['sender_user_id'] = null;

        $field = request('orderBy', 'created_at');
        $orderType = request('order', 'DESC');

        list($messages, $msgErrors) = api_result(ApiMessages::class, 'search', [
            'filters'     => $filters,
            'order_field' => $field,
            'order_type'  => $orderType,
        ]);

        if (!empty($msgErrors)) {
            $request->session()->flash('alert-danger', __('custom.list_msg_fail'));
        } else {
            $messages = !empty($messages->data) ? $this->paginate($messages) : [];
        }

        $this->addBreadcrumb(__('breadcrumbs.message_list'), '');

        return view('admin.messages_list', [
            'statuses' => $statuses,
            'filters'  => $filters,
            'messages' => $messages,
        ])->withErrors($errors);
    }

    public function view($id, $orgId = null)
    {
        $orgData = [];

        if (isset($orgId)) {
            list($orgData, $orgErrors) = api_result(ApiOrganisation::class, 'getData', ['org_id' => (int) $orgId]);

            if (empty($orgData)) {
                return redirect()->back();
            }
        }

        list($messages, $errors) = api_result(ApiMessages::class, 'listByParent', ['parent_id' => $id]);

        if (!empty($errors)) {
            $errors = ['message' => __('custom.view_msg_fail')];
            return redirect()->back()->withErrors($errors);
        }

        $parent = [];
        if (!empty($messages)) {
            foreach ($messages as $message) {
                // set parent
                if (empty($parent) && $message->id == $id) {
                    $parent = $message;
                }
                // mark as read
                if ($message->sender_org_id && !$message->read) {
                    list($res, $errors) = api_result(ApiMessages::class, 'markAsRead', ['message_id' => $message->id]);
                }
            }
        }

        if (!empty($orgData)) {
            $this->addBreadcrumb($orgData->name, route('admin.org_edit', ['id'=> $orgData->id]));
        } else {
            $this->addBreadcrumb(__('breadcrumbs.message_list'), route('admin.messages.list'));
        }
        $this->addBreadcrumb(!empty($parent) ? $parent->subject : '', '');

        return view('admin.request', [
            'messages' => $messages,
            'parent'   => $parent,
            'orgId'    => $orgId,
        ]);
    }

    public function send(Request $request, $id = null)
    {
        $orgId = $request->get('orgId', null);

        $data = $request->except(['orgId']);

        $data['parent_id'] = $id;
        $data['body'] = $data['new_message'];
        $data['sender_user_id'] = auth()->guard('backend')->user()->id;

        list($result, $errors) = api_result(ApiMessages::class, 'sendMessageToOrg', $data, 'id');

        if (!empty($errors)) {
            if (is_string($errors)) {
                $errors = ['message' => $errors];
            } else {
                $errors = (array) $errors;
                if (!isset($errors['body'])) {
                    $errors = ['message' => __('custom.send_msg_fail')];
                }
            }
            return redirect()->to(back()->getTargetUrl() . (isset($id) ? '#error' : ''))->withErrors($errors)->withInput();
        }

        if ($id == null) {
            $id = isset($result) ? $result : null;
        }

        return redirect(route('admin.messages', ['id' => $id, 'orgId' => $orgId]) . (isset($result) ? '#'. $result : ''));
    }

    public function add($id)
    {
        list($orgData, $orgErrors) = api_result(ApiOrganisation::class, 'getData', ['org_id' => $id]);

        if (empty($orgData)) {
            return redirect()->back();
        }

        $this->addBreadcrumb($orgData->name, route('admin.org_edit', ['id'=> $orgData->id]));
        $this->addBreadcrumb(__('custom.new_message'), '');

        return view('admin.new_request', ['orgData' => $orgData]);
    }
}
