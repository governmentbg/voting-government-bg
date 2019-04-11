<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseAdminController;
use App\Http\Controllers\Api\OrganisationController as ApiOrganisation;
use App\Http\Controllers\Api\VotingTourController as ApiVotingTour;

class OrganisationController extends BaseAdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->addBreadcrumb(__('breadcrumbs.start'), '#');
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

        list($organisations, $errors) = api_result(ApiOrganisation::class, 'search', [
            'with_pagination' => true,
            'filters' => $allFilters
        ]);

        list($votingTour, $tourErrors) = api_result(ApiVotingTour::class, 'getLatestVotingTour');
        list($statuses, $statusErrors) = api_result(ApiOrganisation::class, 'listStatuses' );
        list($candidateStatuses, $candidateErrors) = api_result(ApiOrganisation::class, 'listCandidateStatuses');

        $this->addBreadcrumb(__('breadcrumbs.organisations'), $votingTour->name);
        $statuses = collect($statuses)->pluck('name', 'id')->toArray();
        $candidateStatuses = collect($candidateStatuses)->pluck('name', 'id')->toArray();

        $paginatedData = $organisations ? $this->paginate($organisations) : [];

        return view('admin.org_list', [
            'organisationList'  => $paginatedData,
            'tourData'          => $votingTour,
            'statuses'          => $statuses,
            'candidateStatuses' => $candidateStatuses,
            'filters'           => $allFilters
        ]);
    }

    public function view(Request $request)
    {
        return view('admin.org_view');
    }

    public function edit(Request $request)
    {
        $id = $request->offsetGet('id');
        list($org_data, $errors) = api_result(ApiOrganisation::class, 'getData', [
            'org_id' => $id
        ]);

        list($statuses, $statusErrors) = api_result(ApiOrganisation::class, 'listStatuses' );
        $candidateStatuses = collect($statuses)->pluck('name', 'id')->toArray();

        if ($request->has('edit')) {
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
                return back();
            } else {
                $request->session()->flash('alert-danger', __('custom.edit_error'));
                return back()->withErrors(isset($editErrors) ? $editErrors : []);
            }
        }

        return view('admin.org_edit', [
            'org_data'          => $org_data,
            'candidateStatuses' => $candidateStatuses
        ]);
    }
}
