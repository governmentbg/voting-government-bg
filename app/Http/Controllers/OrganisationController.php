<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\OrganisationController as ApiOrganisation;
use App\Http\Controllers\Api\UserController as ApiUser;
use App\Http\Controllers\Api\FileController as ApiFile;
use App\Organisation;
use App\Message;
use \Validator;

class OrganisationController extends BaseFrontendController
{
    protected $redirectTo = '/publicLists/registered';

    public function __construct()
    {
        parent::__construct();
        $this->addBreadcrumb(__('breadcrumbs.start'), '/');
    }

    public function register(Request $request)
    {
        $this->addBreadcrumb(__('breadcrumbs.register'), '');

        return view('organisation.register');
    }

    public function store(Request $request)
    {
        $errors = [];
        if (!$request->get('terms_accepted', null)) {
            $errors = ['terms_accepted' => __('custom.terms_not_accepted')];
        }

        $validator = Validator::make(['captcha' => $request->captcha], [
            'captcha' => 'required|captcha'
        ]);

        if ($validator->fails()) {
            $errors['captcha'] = __('custom.chaptcha_fail');
        }

        if (empty($errors)) {
            $orgData = $request->except(['_token', 'terms_accepted', 'files']);
            $orgData['in_av'] = (isset($orgData['in_av']) && $orgData['in_av']) ? 1 : 0;
            $orgData['is_candidate'] = (isset($orgData['is_candidate']) && $orgData['is_candidate']) ? 1 : 0;

            $params = [
                'org_data' => $orgData
            ];

            $files = $request->offsetGet('files');
            if (is_array($files) && !empty($files)) {
                $params['files'] = [];
                foreach ($files as $file) {
                    $params['files'][] = [
                        'name'      => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'data'      => base64_encode(\File::get($file->getPathName())),
                    ];
                }
            }

            DB::beginTransaction();
            $result = api(ApiOrganisation::class, 'register', $params);
            if (isset($result->success) && $result->success && isset($result->id)) {
                $userData = [
                    'name' => $orgData['name'],
                    'username' => (string) $orgData['eik'],
                    'password' => str_random(10),
                ];
                $params = [
                    'user_data' => [
                        'org_id' => $result->id,
                        'username' => $userData['username'],
                        'password' => $userData['password'],
                        'password_confirm' => $userData['password'],
                    ]
                ];

                $result = api(ApiUser::class, 'add', $params);
                if (isset($result->success) && $result->success) {
                    DB::commit();

                    session()->flash('alert-success', __('custom.register_success'));
                    if (sendEmail('emails/registrationConfirm', $userData, $orgData['email'], __('custom.register_subject'))) {
                        session()->flash('alert-info', __('custom.register_send_mail_success'));
                    } else {
                        session()->flash('alert-info', __('custom.register_send_mail_failed'));
                    }

                    return redirect($this->redirectTo);
                } else {
                    DB::rollback();
                    $errors = !empty($result->errors) ? $result->errors : [];
                    session()->flash('alert-danger', isset($result->error) ? $result->error->message : __('custom.register_error'));
                }
            } else {
                $errors = !empty($result->errors) ? $result->errors : [];
                session()->flash('alert-danger', isset($result->error) ? $result->error->message : __('custom.register_org_error'));
            }
        }

        return redirect()->back()->withErrors($errors)->withInput();
    }

    public function view()
    {
        $id = auth()->user()->org_id;
        list($org, $errors) = api_result(ApiOrganisation::class, 'getData', ['org_id' => $id]);

        if(!empty($errors)){
           return back()->withErrors($errors);
        }

//        list($messages, $errors) = api_result(APIMessage::class, 'listByOrg', ['org_id' => $id]);
//
//        if(!empty($errors)){
//           return back()->withErrors($errors);
//        }

        $messages = Message::where('recipient_org_id', $org->id)->get();

        list($files, $errors) = api_result(ApiOrganisation::class, 'getFileList', ['org_id' => $id]);

        if(!empty($errors)){
           return back()->withErrors($errors);
        }

        $data = [
            'organisation' => $org,
            'status' => (Organisation::getStatuses())[$org->status],
            'isApproved' => in_array($org->status, Organisation::getApprovedStatuses()),
            'messages' => $messages,
            'files' => $files
        ];

        return view('organisation.view', $data);
    }

    public function downloadFile(Request $request)
    {
        $id = $request->offsetGet('id');
        list($file, $filesErrors) = api_result(ApiFile::class, 'getData', ['file_id' => $id]);

        if (!empty($file->data)) {
            $file->data = base64_decode($file->data);
            return response($file->data, 200, [
                'Content-Type'          => $file->mime_type,
                'Content-Disposition'   => 'attachment; filename="'. $file->name .'"',
            ]);
        }

        if ($filesErrors) {
            $request->session()->flash('alert-danger', __('custom.edit_error'));
            return redirect()->back()->withErrors(isset($filesErrors) ? $filesErrors : []);
        }

        $request->session()->flash('alert-danger', __('custom.file_not_found'));
        return redirect()->back();
    }
}
