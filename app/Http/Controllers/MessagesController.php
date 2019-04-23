<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Api\MessageController as ApiMessages;
use App\Message;

class MessagesController extends BaseFrontendController
{
    public function __construct()
    {
        parent::__construct();
        $this->addBreadcrumb(__('breadcrumbs.start'), '/view');
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

        array_unshift($messages, (object) $parent); //add parent message to the begging of the array

        //mark as read
        foreach ($messages as $key => $message) {
            if ($message->sender_org_id == null && !$message->read) {
                list($res, $errors) = api_result(ApiMessages::class, 'markAsRead', ['message_id' => $message->id]);
            }
        }

        return view('organisation.request', [
            'messages' => $messages,
            'parent'   => (object) $parent,
        ]);
    }

    public function send(Request $request, $id)
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

        list($result, $errors) = api_result(ApiMessages::class, 'sendMessageFromOrg', $data);

        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors);
        }

        return redirect()->route('organisation.messages', ['id' => $id]);
    }
}
