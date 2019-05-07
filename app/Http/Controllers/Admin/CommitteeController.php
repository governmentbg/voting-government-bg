<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseAdminController;
use App\Http\Controllers\Api\UserController as ApiUsers;

class CommitteeController extends BaseAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->addBreadcrumb(__('breadcrumbs.start'), route('admin.org_list'));
    }

    public function list(Request $request)
    {
        $this->addBreadcrumb(__('breadcrumbs.settings'), route('admin.settings'));
        $this->addBreadcrumb(__('breadcrumbs.committee'), '');
         // apply sort parameters
         if ($request->has('sort')) {
            $orderField = $request->sort;
        } else {
            $orderField = 'name';
        }

        if ($request->has('order')) {
            $orderType = $request->order;
        } else {
            $orderType = 'asc';
        }

        list($users, $errors) = api_result(ApiUsers::class, 'list', [
            'order_field' => $orderField,
            'order_type'  => $orderType
        ]);

        if (!empty($errors)) {
            return view('admin.committeeList')->withErrors($errors)->with('users', []);
        }

        return view('admin.committee_list', ['users' => $this->paginate($users)]);
    }

    public function create(Request $request)
    {
        $this->addBreadcrumb(__('breadcrumbs.settings'), route('admin.settings'));
        $this->addBreadcrumb(__('breadcrumbs.committee'), route('admin.committee.list'));
        $this->addBreadcrumb(__('breadcrumbs.add'), '');

        return view('admin.committee_add');
    }

    public function edit(Request $request, $id)
    {
        $this->addBreadcrumb(__('breadcrumbs.settings'), route('admin.settings'));
        $this->addBreadcrumb(__('breadcrumbs.committee'), route('admin.committee.list'));
        $this->addBreadcrumb(__('breadcrumbs.edit'), '');

        list($user, $errors) = api_result(ApiUsers::class, 'getData', ['user_id' => $id]);

        if (!empty($errors)) {
            return back()->withErrors($errors);
        }

        if($user->username == config('auth.system.user')){
            return back()->withErrors(['message' => __('messages.unauthorized_access')]);
        }

        return view('admin.committee_edit', ['user' => $user]);
    }

    public function store(Request $request)
    {
        $data = $request->only('username', 'first_name', 'last_name', 'email', 'active');

        $password = str_random(16);

        $data['password'] = $password;
        $data['password_confirm'] = $password;

        $data['active'] = $request->get('active', 0);

        list($user, $errors) = api_result(ApiUsers::class, 'add', ['user_data' => $data]);

        if(!empty($errors)){
            return redirect()->back()->withErrors($errors)->withInput();
        }

        session()->flash('alert-success', trans('custom.create_success'));

        $data['name'] = $data['first_name'] . ' ' . $data['last_name'];
        $data['isAdmin'] = true;
        if (sendEmail('emails.registrationConfirm', $data, $data['email'], __('custom.register_subject'))) {
            session()->flash('alert-info', __('custom.register_send_mail_success'));
        } else {
            session()->flash('alert-info', __('custom.register_send_mail_failed'));
        }

        return redirect()->route('admin.committee.list');
    }

    public function update(Request $request, $id)
    {
        $data = $request->only('username', 'first_name', 'last_name', 'email', 'active');

        $data['active'] = $request->get('active', 0);

        list($result, $errors) = api_result(ApiUsers::class, 'edit', ['user_data' => $data, 'user_id' => $id]);

       if(!empty($errors)){
            return redirect()->back()->withErrors($errors)->withInput();
        }

        session()->flash('alert-success', trans('custom.update_success'));
        return redirect()->route('admin.committee.list');
    }
}
