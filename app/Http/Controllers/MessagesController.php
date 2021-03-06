<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Api\MessageController as ApiMessages;

class MessagesController extends BaseFrontendController
{
    public function __construct()
    {
        parent::__construct();
        $this->addBreadcrumb(__('breadcrumbs.start'), '/view');
    }

    public function view($id)
    {
        list($messages, $errors) = api_result(ApiMessages::class, 'listByParent', ['parent_id' => $id]);

        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors);
        }

        $parent = [];
        if (!empty($messages)) {
            foreach ($messages as $message) {
                if (($message->sender_org_id != \Auth::user()->org_id) && ($message->recipient_org_id != \Auth::user()->org_id)) {
                    return redirect()->route('organisation.view');
                }

                // set parent
                if (empty($parent) && $message->id == $id) {
                    $parent = $message;
                }
                // mark as read
                if (is_null($message->sender_org_id) && !$message->read) {
                    list($res, $errors) = api_result(ApiMessages::class, 'markAsRead', ['message_id' => $message->id]);
                }
            }
        }

        $this->addBreadcrumb(!empty($parent) ? $parent->subject : '', '');

        return view('organisation.request', [
            'messages' => $messages,
            'parent'   => $parent,
        ]);
    }

    public function send(Request $request, $id = null)
    {
        $data = $request->all();

        $data['parent_id'] = $id;
        $data['body'] = $data['new_message'];
        $data['sender_org_id'] = auth()->user()->org_id;

        $files = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $files[] = [
                    'name'      => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'data'      => base64_encode(\File::get($file->getPathName())),
                ];
            }
        }

        $data['files'] = $files;

        list($result, $errors) = api_result(ApiMessages::class, 'sendMessageFromOrg', $data, 'id');

        if (!empty($errors)) {
            if (is_string($errors)) {
                $errors = ['message' => $errors];
            } else {
                $errors = (array) $errors;
                if (!isset($errors['subject']) && !isset($errors['body'])) {
                    $errors = ['message' => __('custom.send_msg_fail')];
                }
            }
            if (!empty($files)) {
                $errors['reattach_files'] = __('custom.reattach_files');
            }
            return redirect()->to(back()->getTargetUrl() . (isset($id) ? '#error' : ''))->withErrors($errors)->withInput();
        }

        if ($id == null) {
            $id = isset($result) ? $result : null;
        }

        return redirect(route('organisation.messages', ['id' => $id]) . (isset($result) ? '#'. $result : ''));
    }

    public function add()
    {
        $this->addBreadcrumb(__('custom.new_message'), '');

        return view('organisation.new_request');
    }
}
