<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseAdminController;
use App\Http\Controllers\Api\VotingTourController as ApiVotingTour;
use App\VotingTour;
use App\Jobs\SendAllVoteInvites;
use App\Organisation;

class VotingTourController extends BaseAdminController
{
    protected $redirectTo = 'admin/votingTours';
    
    const CREATE_SUCCESS = 'custom.create_success';
    
    const UPDATE_SUCCESS = 'custom.update_success';

    public function __construct()
    {
        parent::__construct();

        $this->addBreadcrumb(__('breadcrumbs.start'), route('admin.org_list'));
    }

    public function index()
    {
        $this->addBreadcrumb(__('breadcrumbs.settings'), 'settings');
        $this->addBreadcrumb(__('breadcrumbs.voting_tours'), '');
        list($votingTours, $errors) = api_result(ApiVotingTour::class, 'list');

        return view('tours.list', ['votingTours' => $votingTours, 'errors' => $errors]);
    }

    public function create()
    {
//        list($votingTour, $errors) = api_result(ApiVotingTour::class, 'getLatestVotingTour');
//
//        if($votingTour && $votingTour->status != VotingTour::STATUS_FINISHED){
//            return redirect()->back()->withErrors(['messsage' => __('custom.active_tour_exists')]);
//        }

        $this->addBreadcrumb(__('breadcrumbs.settings'), 'settings');
        $this->addBreadcrumb(__('breadcrumbs.voting_tours'), route('admin.voting_tour.list'));
        $this->addBreadcrumb(__('custom.create_voting_tour'), '');

        return view('tours.create');
    }

    public function edit($id)
    {
        list($votingTour, $errors) = api_result(ApiVotingTour::class, 'getData', ['tour_id' => $id]);

        if($votingTour->status == VotingTour::STATUS_FINISHED){
            return redirect()->back()->withErrors(['message' => __('custom.voting_tour_finished')]);
        }

        $this->addBreadcrumb(__('breadcrumbs.settings'), route('admin.settings'));
        $this->addBreadcrumb(__('breadcrumbs.voting_tours'), route('admin.voting_tour.list'));
        $this->addBreadcrumb($votingTour->name, '');
        
        $count = Organisation::whereIn('status', Organisation::getApprovedStatuses())->where('voting_tour_id', $votingTour->id)->count();

        return view('tours.edit', ['votingTour' => $votingTour, 'errors' => $errors, 'count' => $count]);
    }

    public function update($id)
    {
        $status = request()->get('status');
        list($votingTour, $errors) = api_result(ApiVotingTour::class, 'getLatestVotingTour');
        $oldStatus = $votingTour ? $votingTour->status : VotingTour::STATUS_FINISHED;

        list($data, $errors) = api_result(ApiVotingTour::class, 'changeStatus', ['new_status' => $status]);

        if(empty($errors)){
            if($oldStatus != $status && ($status == VotingTour::STATUS_VOTING || $status == VotingTour::STATUS_BALLOTAGE)){
                //send emails to all orgs - voting is open
                $this->sendEmails();
            }

            session()->flash('alert-success', trans(self::UPDATE_SUCCESS));
            return redirect($this->redirectTo);
        }

        return redirect()->back()->withErrors($errors)->withInput();
    }

    public function store()
    {
        list($id, $errors) = api_result(ApiVotingTour::class, 'add', request()->all());

        if(empty($errors)){
            session()->flash('alert-success', trans(self::CREATE_SUCCESS));
            return redirect($this->redirectTo);
        }

        return redirect()->back()->withErrors($errors)->withInput();
    }
    
    private function sendEmails()
    {
        SendAllVoteInvites::dispatch();
    }
}
