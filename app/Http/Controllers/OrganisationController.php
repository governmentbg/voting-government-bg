<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\OrganisationController as ApiOrganisation;
use App\Http\Controllers\Api\UserController as ApiUser;
use App\Http\Controllers\Api\FileController as ApiFile;
use App\Http\Controllers\Api\MessageController as ApiMessage;
use App\Organisation;
use App\VotingTour;
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
        if (!empty($this->votingTour) && $this->votingTour->status == VotingTour::STATUS_OPENED_REG) {
            $this->addBreadcrumb(__('breadcrumbs.register'), '');

            return view('organisation.register');
        }

        return redirect('/');
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

        if (empty($errors)) {
            $orgData = $request->except(['_token', 'terms_accepted', 'files']);
            $orgData['in_av'] = (isset($orgData['in_av']) && $orgData['in_av']) ? 1 : 0;
            $orgData['is_candidate'] = (isset($orgData['is_candidate']) && $orgData['is_candidate']) ? 1 : 0;

            $params = [
                'org_data' => $orgData
            ];
            if (!empty($files)) {
                $params['files'] = $files;
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

        if (!empty($files) && !empty($errors)) {
            $errors['reattach_files'] = __('custom.reattach_files');
        }

        return redirect()->back()->withErrors($errors)->withInput();
    }

    public function view()
    {
        $org = [];
        $status = '';
        $isApproved = false;
        $files = [];
        $messages = [];
        $errors = [];

        if (session()->has('errors')) {
            $errors = session()->get('errors')->messages();
        }

        if (auth()->check()) {
            $orgId = auth()->user()->org_id;
            list($org, $orgErrors) = api_result(ApiOrganisation::class, 'getData', ['org_id' => $orgId]);

            if (!empty($orgErrors)) {
                session()->flash('alert-danger', __('custom.get_org_fail'));
            } elseif (!empty($org)) {
                list($statuses, $statusErrors) = api_result(ApiOrganisation::class, 'listStatuses');
                $statuses = !empty($statuses) ? collect($statuses)->pluck('name', 'id')->toArray() : [];
                $status = isset($statuses[$org->status]) ? $statuses[$org->status] : $org->status;
                $isApproved = in_array($org->status, Organisation::getApprovedStatuses());

                list($files, $filesErrors) = api_result(ApiOrganisation::class, 'getFileList', ['org_id' => $orgId]);
                if (!empty($filesErrors)) {
                    $errors['files_message'] = __('custom.list_org_files_fail');
                }

                list($messages, $msgErrors) = api_result(ApiMessage::class, 'listByOrg', ['org_id' => $orgId]);
                if (!empty($msgErrors)) {
                    $errors['msg_message'] = __('custom.list_msg_fail');
                } else {
                    $messages = !empty($messages->data) ? $this->paginate($messages) : [];
                }
            }
        }

        $data = [
            'organisation' => $org,
            'status' => $status,
            'isApproved' => $isApproved,
            'messages' => $messages,
            'files' => $files
        ];

        return view('organisation.view', $data)->withErrors($errors);
    }

    public function downloadFile(Request $request)
    {
        if (auth()->check()) {
            $id = $request->offsetGet('id');
            list($file, $fileErrors) = api_result(ApiFile::class, 'getData', ['file_id' => $id]);

            if (!empty($file) && $file->org_id == auth()->user()->org_id) {
                if (!empty($file->data)) {
                    $file->data = base64_decode($file->data);
                    return response($file->data, 200, [
                        'Content-Type'        => $file->mime_type,
                        'Content-Disposition' => 'attachment; filename="'. $file->name .'"',
                    ]);
                }
            }

            if (!empty($fileErrors)) {
                $request->session()->flash('alert-danger', __('custom.dl_file_fail'));
                return redirect()->back()->withErrors($fileErrors);
            }
        }

        $request->session()->flash('alert-danger', __('custom.file_not_found'));
        return redirect()->back();
    }
}
