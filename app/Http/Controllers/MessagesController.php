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
        list($messages, $errors) = api_result(ApiMessages::class, 'listByParent', [
            'parent_id' => $id,
        ]);

        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors);
        }

        $parent = [];
        if (!empty($messages)) {
            foreach ($messages as $key => $message) {
                // set parent
                if (empty($parent) && is_null($message->parent_id)) {
                    $parent = $message;
                }
                // mark as read
                if ($message->sender_org_id == null && !$message->read) {
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

        //todo file type validation

        $files = [];
        if (isset($data['files'])) {
            foreach ($data['files'] as $key => $file) {
                $files[$key]['name'] = $file->getClientOriginalName();
                $files[$key]['mime_type'] = $file->getMimeType();
                $files[$key]['data'] = base64_encode(file_get_contents($file->getRealPath()));
            }
        }

        $data['files'] = $files;

        list($result, $errors) = api_result(ApiMessages::class, 'sendMessageFromOrg', $data, 'id');

        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        if ($id == null) {
            $id = isset($result) ? $result : null;
        }

        return redirect()->route('organisation.messages', ['id' => $id]);
    }

    public function add()
    {
        $this->addBreadcrumb(__('custom.new_message'), '');

        return view('organisation.new_request');
    }
}
