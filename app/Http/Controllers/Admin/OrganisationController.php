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
        return view('admin.org_edit');
    }
}
