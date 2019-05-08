<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseAdminController;
use App\Http\Controllers\Api\OrganisationController as ApiOrganisation;
use App\Http\Controllers\Api\FileController as ApiFile;
use App\Http\Controllers\Api\MessageController as ApiMessage;

class OrganisationController extends BaseAdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->addBreadcrumb(__('breadcrumbs.start'), route('admin.org_list'));
    }

    public function list(Request $request)
    {
        $status = $request->offsetGet('status');
        $eik = $request->offsetGet('eik');
        $email = $request->offsetGet('email');
        $name = $request->offsetGet('name');
        $is_candidate = $request->offsetGet('is_candidate');
        $reg_date_from = $request->offsetGet('reg_date_from');
        $reg_date_to = $request->offsetGet('reg_date_to');

        $this->addBreadcrumb(__('custom.registered_orgs'), '');

        $allFilters = [];
        if (isset($status) && $status != 'all') {
            $allFilters['statuses'] = [(int) $status];
        }
        if (isset($eik) && $eik != '') {
            $allFilters['eik'] = $eik;
        }
        if (isset($email) && $email != '') {
            $allFilters['email'] = $email;
        }
        if (isset($name) && $name != '') {
            $allFilters['name'] = $name;
        }
        if (isset($is_candidate) && $is_candidate != 'all') {
            $allFilters['is_candidate'] = $is_candidate;
        }
        if (isset($reg_date_from)) {
            $allFilters['reg_date_from'] = $reg_date_from;
        }
        if (isset($reg_date_to)) {
            $allFilters['reg_date_to'] = $reg_date_to;
        }

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

        list($organisations, $errors) = api_result(ApiOrganisation::class, 'search', [
            'with_pagination' => true,
            'filters'         => $allFilters,
            'order_field'     => $orderField,
            'order_type'      => $orderType,
        ]);

        if (!empty($errors)) {
            $request->session()->flash('alert-danger', __('custom.list_org_fail'));
        } else {
            $organisations = !empty($organisations->data) ? $this->paginate($organisations) : [];
        }

        list($statuses, $statusErrors) = api_result(ApiOrganisation::class, 'listStatuses');
        $statuses = !empty($statuses) ? collect($statuses)->pluck('name', 'id')->toArray() : [];

        list($candidateStatuses, $candidateErrors) = api_result(ApiOrganisation::class, 'listCandidateStatuses');
        $candidateStatuses = !empty($candidateStatuses) ? collect($candidateStatuses)->pluck('name', 'id')->toArray() : [];

        return view('admin.org_list', [
            'organisationList'  => $organisations,
            'statuses'          => $statuses,
            'candidateStatuses' => $candidateStatuses,
            'filters'           => $allFilters
        ]);
    }

    public function edit(Request $request)
    {
        $statuses = [];
        $files = [];
        $messages = [];
        $errors = [];

        if (session()->has('errors')) {
            $errors = session()->get('errors')->messages();
        }

        $id = $request->offsetGet('id');
        list($orgData, $orgErrors) = api_result(ApiOrganisation::class, 'getData', ['org_id' => $id]);

        if (!empty($orgErrors)) {
            session()->flash('alert-danger', __('custom.get_org_fail'));
        } elseif (!empty($orgData)) {
            list($statuses, $statusErrors) = api_result(ApiOrganisation::class, 'listStatuses');
            $statuses = !empty($statuses) ? collect($statuses)->pluck('name', 'id')->toArray() : [];

            list($files, $filesErrors) = api_result(ApiOrganisation::class, 'getFileList', ['org_id' => $id]);
            if (!empty($filesErrors)) {
                $errors['files_message'] = __('custom.list_org_files_fail');
            }

            list($messages, $msgErrors) = api_result(ApiMessage::class, 'listByOrg', ['org_id' => $id]);
            if (!empty($msgErrors)) {
                $errors['msg_message'] = __('custom.list_msg_fail');
            } else {
                $messages = !empty($messages->data) ? $this->paginate($messages) : [];
            }
        }

        $this->addBreadcrumb(!empty($orgData) ? $orgData->name : '');

        return view('admin.org_edit', [
            'orgData'  => $orgData,
            'statuses' => $statuses,
            'files'    => $files,
            'messages' => $messages,
        ])->withErrors($errors);
    }

    public function update(Request $request)
    {
        $id = $request->offsetGet('id');
        $name = $request->offsetGet('name') ? $request->offsetGet('name') : '';
        $address = $request->offsetGet('address') ? $request->offsetGet('address') : '';
        $representative = $request->offsetGet('representative') ? $request->offsetGet('representative') : '';
        $phone = $request->offsetGet('phone') ? $request->offsetGet('phone') : '';
        $email = $request->offsetGet('email') ? $request->offsetGet('email') : '';
        $in_av = $request->offsetGet('in_av') ? $request->offsetGet('in_av') : false;
        $is_candidate = $request->offsetGet('is_candidate') ? $request->offsetGet('is_candidate') : false;
        $references = $request->offsetGet('references') ? $request->offsetGet('references') : '';
        $description = $request->offsetGet('description') ? $request->offsetGet('description') : '';
        $status = $request->offsetGet('status');

        list($edit, $editErrors) = api_result(ApiOrganisation::class, 'edit', [
            'org_id'   => $id,
            'org_data' => [
                'name'           => $name,
                'email'          => $email,
                'representative' => $representative,
                'address'        => $address,
                'phone'          => $phone,
                'email'          => $email,
                'in_av'          => $in_av,
                'is_candidate'   => $is_candidate,
                'references'     => $references,
                'description'    => $description,
                'status'         => $status
            ]
        ]);

        if (empty($editErrors)) {
            $request->session()->flash('alert-success', __('custom.edit_success'));
            return redirect()->back();
        } else {
            $request->session()->flash('alert-danger', __('custom.edit_error'));
            return redirect()->back()->withErrors($editErrors)->withInput();
        }
    }

    public function downloadFile(Request $request)
    {
        $id = $request->offsetGet('id');
        list($file, $fileErrors) = api_result(ApiFile::class, 'getData', ['file_id' => $id]);

        if (!empty($file) && !empty($file->data)) {
            $file->data = base64_decode($file->data);
            return response($file->data, 200, [
                'Content-Type'        => $file->mime_type,
                'Content-Disposition' => 'attachment; filename="'. $file->name .'"',
            ]);
        }

        if (!empty($fileErrors)) {
            $request->session()->flash('alert-danger', __('custom.dl_file_fail'));
            return redirect()->back()->withErrors($fileErrors);
        }

        $request->session()->flash('alert-danger', __('custom.file_not_found'));
        return redirect()->back();
    }
}
