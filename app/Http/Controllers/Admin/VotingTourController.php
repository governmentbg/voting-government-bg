<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseAdminController;
use App\Http\Controllers\Api\VotingTourController as ApiVotingTour;
use App\VotingTour;

class VotingTourController extends BaseAdminController
{
    protected $redirectTo = 'admin/votingTours';

    public function __construct()
    {
        parent::__construct();

        $this->addBreadcrumb(__('breadcrumbs.start'), '#');
        $this->addBreadcrumb(__('breadcrumbs.settings'), 'settings');
    }

    public function index()
    {
        $this->addBreadcrumb(__('breadcrumbs.voting_tour'), '');
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

        $this->addBreadcrumb(__('custom.create_voting_tour'), '');

        return view('tours.create');
    }

    public function edit($id)
    {
        list($votingTour, $errors) = api_result(ApiVotingTour::class, 'getData', ['tour_id' => $id]);

        if($votingTour->status == VotingTour::STATUS_FINISHED){
            return redirect()->back()->withErrors(['message' => __('custom.voting_tour_finished')]);
        }

        $this->addBreadcrumb($votingTour->name, '');

        return view('tours.edit', ['votingTour' => $votingTour, 'errors' => $errors]);
    }

    public function update($id)
    {
        $status = request()->get('status');
        list($votingTour, $errors) = api_result(ApiVotingTour::class, 'getLatestVotingTour');
        $oldStatus = $votingTour ? $votingTour->status : VotingTour::STATUS_FINISHED;

        list($data, $errors) = api_result(ApiVotingTour::class, 'changeStatus', ['new_status' => $status]);

        if(empty($errors)){
            if($oldStatus != $status && ($status == VotingTour::STATUS_VOTING || $status == VotingTour::STATUS_BALLOTAGE)){
                //TODO send emails to all orgs - voting is open
            }

            return redirect($this->redirectTo);
        }

        return redirect()->back()->withErrors($errors)->withInput();
    }

    public function store()
    {
        list($id, $errors) = api_result(ApiVotingTour::class, 'add', request()->all());

        if(empty($errors)){
            return redirect($this->redirectTo);
        }

        return redirect()->back()->withErrors($errors)->withInput();
    }
}
