<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseAdminController;
use App\Http\Controllers\Api\UserController as ApiUsers;

class CommitteeController extends BaseAdminController
{
    public function list(Request $request)
    {
        list($users, $errors) = api_result(ApiUsers::class, 'list');
        
        if (!empty($errors)) {
            return view('admin.committeeList')->withErrors($errors)->with('users', []);
        }
        
        return view('admin.committee_list', ['users' => $this->paginate($users)]);
    }
    
    public function create(Request $request)
    {
        return view('admin.committee_add');
    }
    
    public function edit(Request $request, $id)
    {
        list($user, $errors) = api_result(ApiUsers::class, 'getData', ['user_id' => $id]);
        
        if (!empty($errors)) {
            return back()->withErrors($errors);
        }
        
        if($user->username == config('auth.system.user')){
            return back()->withErrors(['message' => __('messages.unauthorized_access')]);
        }
        
        return view('admin.committee_edit', ['user' => $user]);
    }
}
