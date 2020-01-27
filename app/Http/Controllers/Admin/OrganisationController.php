<?php

namespace App\Http\Controllers\Admin;

use App\Vote;
use App\VotingTour;
use App\Organisation;
use App\ActionsHistory;
use App\PredefinedOrganisation;
use App\BulstatRegister;
use App\TradeRegister;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseAdminController;
use App\Http\Controllers\Api\OrganisationController as ApiOrganisation;
use App\Http\Controllers\Api\PredefinedListController as ApiPredefined;
use App\Http\Controllers\Api\VotingTourController as ApiVotingTour;
use App\Http\Controllers\Api\FileController as ApiFile;
use App\Http\Controllers\Api\MessageController as ApiMessage;
use App\Http\Controllers\Api\VoteController as ApiVote;
use Illuminate\Support\Facades\Cache;
use App\Jobs\SendResultsInvitation;

class OrganisationController extends BaseAdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->addBreadcrumb(__('breadcrumbs.start'), route('admin.org_list'));
    }

    public function list(Request $request)
    {
        $organisations = [];
        $status = $request->offsetGet('status');
        $eik = $request->offsetGet('eik');
        $email = $request->offsetGet('email');
        $name = $request->offsetGet('name');
        $is_candidate = $request->offsetGet('is_candidate');
        $reg_date_from = $request->offsetGet('reg_date_from');
        $reg_date_to = $request->offsetGet('reg_date_to');

        $this->addBreadcrumb(__('custom.registered_orgs'), '');

        $allFilters = [];
        if (isset($status)) {
            $allFilters['statuses'] = $status == 'all' ? null : [(int) $status];
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
            $orderField = 'created_at';
        }

        if ($request->has('order')) {
            $orderType = $request->order;
        } else {
            $orderType = 'desc';
        }

        if (!empty(session('filters'))) {
            if (empty($allFilters)) {
                $allFilters = session('filters');
            }
        }

        session(['filters' => $allFilters]);

        if ($request->has('forget')) {
            session()->forget('filters');
            return redirect(route('admin.org_list'));
        }

        if (!empty($this->votingTour)) {
            list($organisations, $errors) = api_result(ApiOrganisation::class, 'search', [
                'with_pagination' => $request->has('download') ? false : true,
                'filters'         => $allFilters,
                'order_field'     => $orderField,
                'order_type'      => $orderType,
            ]);

            if (!empty($errors)) {
                $request->session()->flash('alert-danger', __('custom.list_org_fail'));
            } else {
                $exportOrgs = $organisations;
                $organisations = !empty($organisations->data) ? $this->paginate($organisations) : [];
            }
        }

        list($statuses, $statusErrors) = api_result(ApiOrganisation::class, 'listStatuses');
        $statuses = !empty($statuses) ? collect($statuses)->pluck('name', 'id')->toArray() : [];

        list($candidateStatuses, $candidateErrors) = api_result(ApiOrganisation::class, 'listCandidateStatuses');
        $candidateStatuses = !empty($candidateStatuses) ? collect($candidateStatuses)->pluck('name', 'id')->toArray() : [];

        if ($request->has('download')) {
            $filename = 'organisationsList.csv';
            $tempname = tempnam(sys_get_temp_dir(), 'csv_');
            $temp = fopen($tempname, 'w+');
            $path = stream_get_meta_data($temp)['uri'];

            fputcsv($temp, [
                __('custom.organisation'),
                __('custom.eik'),
                __('custom.status'),
                __('custom.candidate'),
                __('custom.registered_at'),
                __('custom.email')
            ]);

            foreach ($exportOrgs as $singleOrg) {
                fputcsv($temp, [
                    $singleOrg->name,
                    $singleOrg->eik,
                    $statuses[$singleOrg->status],
                    $singleOrg->is_candidate == true ? __('custom.status_yes') :  __('custom.status_no'),
                    $singleOrg->created_at,
                    $singleOrg->email
                ]);
            }

            $headers = ['Content-Type' => 'text/csv'];

            $logData = [
                'module' => ActionsHistory::ORGANISATIONS,
                'action' => ActionsHistory::TYPE_DOWNLOADED
            ];

            ActionsHistory::add($logData);

            return response()->download($path, $filename, $headers)->deleteFileAfterSend(true);
        }

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
        $disabledStatuses = [];

        if (session()->has('errors')) {
            $errors = session()->get('errors')->messages();
        }

        $id = $request->offsetGet('id');
        list($orgData, $orgErrors) = api_result(ApiOrganisation::class, 'getData', ['org_id' => $id]);

        list($orgDataPred, $orgErrorsPred) = api_result(ApiPredefined::class, 'getData', ['type' => PredefinedOrganisation::PREDEFINED_LIST_TYPE, 'eik' => $orgData->eik]);
        if (!empty($orgErrorsPred)) {
            $errors['pred_list'] = __('custom.get_org_fail') . (is_string($orgErrorsPred) ? ' - '. $orgErrorsPred : '');
        }

        list($orgDataPredBul, $orgErrorsPredBul) = api_result(ApiPredefined::class, 'getData', ['type' => BulstatRegister::PREDEFINED_LIST_TYPE, 'eik' => $orgData->eik]);
        if (!empty($orgErrorsPredBul)) {
            $errors['pred_list_bul'] = __('custom.get_org_fail') . (is_string($orgErrorsPredBul) ? ' - '. $orgErrorsPredBul : '');
        }

        list($orgDataPredTrade, $orgErrorsPredTrade) = api_result(ApiPredefined::class, 'getData', ['type' => TradeRegister::PREDEFINED_LIST_TYPE, 'eik' => $orgData->eik]);
        if (!empty($orgErrorsPredTrade)) {
            $errors['pred_list_trade'] = __('custom.get_org_fail') . (is_string($orgErrorsPredTrade) ? ' - '. $orgErrorsPredTrade : '');
        }

        if (empty($orgData) || empty($this->votingTour)) {
            return back();
        }

        if ($orgData->status == Organisation::STATUS_DECLASSED) {
            $disabledStatuses = [
                Organisation::STATUS_NEW,
                Organisation::STATUS_PARTICIPANT,
                Organisation::STATUS_CANDIDATE,
                Organisation::STATUS_PENDING,
                Organisation::STATUS_BALLOTAGE,
                Organisation::STATUS_REJECTED,
            ];
        } else {
            if ($this->votingTour->status != VotingTour::STATUS_RANKING ||
                !in_array($orgData->status, Organisation::getApprovedCandidateStatuses())
            ) {
                $disabledStatuses[] = Organisation::STATUS_DECLASSED;

                if ($orgData->status != Organisation::STATUS_BALLOTAGE) {
                    $disabledStatuses[] = Organisation::STATUS_BALLOTAGE;
                }
            }

            if ($this->votingTour->status == VotingTour::STATUS_RANKING &&
                in_array($orgData->status, Organisation::getApprovedCandidateStatuses())
            ) {
                $disabledStatuses = Organisation::getRejectionStatuses();
            }
        }

        if (!empty($orgErrors)) {
            session()->flash('alert-danger', __('custom.get_org_fail'));
        } elseif (!empty($orgData)) {
            list($statuses, $statusErrors) = api_result(ApiOrganisation::class, 'listStatuses');
            $statuses = !empty($statuses) ? collect($statuses)->pluck('name', 'id')->toArray() : [];

            list($statusHints, $statusHintErrors) = api_result(ApiOrganisation::class, 'listStatusHints');
            $statusHints = !empty($statusHints) ? collect($statusHints)->pluck('name', 'id')->toArray() : [];

            if (isset($orgData->status_hint) && isset($statusHints[$orgData->status_hint])) {
                $orgData->status_hint = $statusHints[$orgData->status_hint];
            }

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
            'orgData'          => $orgData,
            'statuses'         => $statuses,
            'files'            => $files,
            'messages'         => $messages,
            'disabledStatuses' => $disabledStatuses,
            'orgDataPred'      => $orgDataPred,
            'orgDataPredBul'   => $orgDataPredBul,
            'orgDataPredTrade' => $orgDataPredTrade
        ])->withErrors($errors);
    }

    public function update(Request $request)
    {
        $id = $request->offsetGet('id');
        $name = $request->get('name', '');
        $address = $request->get('address', '');
        $representative = $request->get('representative', '');
        $phone = $request->get('phone', '');
        $email = $request->get('email', '');
        $in_av = $request->offsetGet('in_av') ? $request->offsetGet('in_av') : false;
        $is_candidate = $request->offsetGet('is_candidate') ? $request->offsetGet('is_candidate') : false;
        $description = $request->get('description', '');
        $references = $request->get('references', '');
        $status = $request->offsetGet('status');
        $rankErrors = [];
        $editErrors = [];

        $callRanking = false;
        list($votingTour, $errors) = api_result(ApiVotingTour::class, 'getLatestVotingTour');

        if ($status == Organisation::STATUS_DECLASSED) {
            list($orgData, $orgErrors) = api_result(ApiOrganisation::class, 'getData', ['org_id' => $id]);

            if (!empty($orgData) && $orgData->status != $status) {
                $callRanking = true;
            }
        }

        \DB::beginTransaction();

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

        $ranking = [];
        if ($callRanking) {
            list($ranking, $rankErrors) = api_result(ApiVote::class, 'ranking', ['status' => Vote::TOUR_ORGANISATION_DECLASSED_RANKING, 'declass_org_id' => $id]);

            $sender = auth()->guard('backend')->user()->id;

            $bulkData = [
                'sender_user_id'   => $sender,
                'subject'          => __('custom.results_invite'),
                'body'             => __('custom.results_from_last_tour', ['name' => $votingTour->name]) .' <a href="'. route('list.ranking').'"> '. __('custom.results'). '</a>',
            ];

            list($sent, $errors) = api_result(ApiMessage::class, 'sendBulkMessagesToOrg', $bulkData);

            $this->sendResultsEmails($votingTour);
        }

        if (empty($editErrors) && empty($rankErrors)) {
            \DB::commit();

            // clear cached ranking
            if (!empty($ranking) && !empty($this->votingTour)) {
                $cacheKey = VotingTour::getCacheKey($this->votingTour->id);
                if (Cache::has($cacheKey)) {
                    Cache::forget($cacheKey);
                }
            }

            $request->session()->flash('alert-success', __('custom.edit_success'));
            return redirect()->back();
        } else {
            \DB::rollBack();
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

    public function settings(Request $request)
    {
       return view('organisation.settings');
    }

    private function sendResultsEmails($votingTour)
    {
        try {
            SendResultsInvitation::dispatch($votingTour);
        } catch (\Exception $e) {
            logger()->error('Send results invites error: '. $e->getMessage());
            session()->flash('alert-info', __('custom.send_results_invites_failed'));
        }
    }
}
