<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\OrganisationController as ApiOrganisation;
use App\Http\Controllers\Api\UserController as ApiUser;

class OrganisationController extends Controller
{
    public function __construct()
    {
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
                    if (sendEmail('mails/registrationConfirm', $userData, $orgData['email'], __('custom.register_subject'))) {
                        session()->flash('alert-info', __('custom.register_send_mail_success'));
                    } else {dd(__('custom.register_send_mail_failed'));
                        session()->flash('alert-info', __('custom.register_send_mail_failed'));
                    }

                    return redirect('/');
                } else {
                    DB::rollback();
                    $errors = !empty($result->errors) ? $result->errors : [$result->error->message];
                    session()->flash('alert-danger', isset($result->error) ? $result->error->message : __('custom.register_error'));
                }
            } else {
                $errors = !empty($result->errors) ? $result->errors : [$result->error->message];
                session()->flash('alert-danger', isset($result->error) ? $result->error->message : __('custom.register_org_error'));
            }
        }

        return redirect()->back()->withErrors($errors)->withInput();
    }
}
