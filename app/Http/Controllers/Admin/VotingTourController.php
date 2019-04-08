<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseAdminController;
use App\Http\Controllers\Api\VotingTourController as ApiVotingTour;
use App\VotingTour;

class VotingTourController extends BaseAdminController
{
    protected $redirectTo = 'votingTour';
    
    public function __construct()
    {
        parent::__construct();
        
        $this->addBreadcrumb(__('breadcrumbs.start'), '');
        $this->addBreadcrumb(__('breadcrumbs.settings'), '');
    }
    
    public function index()
    {
        $this->addBreadcrumb(__('breadcrumbs.settings'), '');
        list($votingTours, $errors) = api_result(ApiVotingTour::class, 'list');
        
        return view('placeholder', ['votingTours' => $votingTours, 'errors' => $errors]);
    }
    
    public function create()
    {
        list($votingTour, $errors) = api_result(ApiVotingTour::class, 'getLatestVotingTour');
        
        if(!empty($errors)&& $votingTour && $votingTour->status != VotingTour::STATUS_FINISHED){
            return redirect()->back()->with(['error' => __('custom.active_tour_exists')]);
        }
        
        return view('placeholder');
    }
    
    public function edit($id)
    {         
        list($votingTour, $errors) = api_result(ApiVotingTour::class, 'getData', ['id' => $id]);
        
        if($votingTour->status == VotingTour::STATUS_FINISHED){
            return redirect()->back()->with(['error' => __('custom.voting_tour_finished')]);
        }
        
        $this->addBreadcrumb($votingTour->name, '');
                
        return view('placeholder', ['votingTour' => $votingTour, 'errors' => $errors]);
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
                
        return redirect()->back()->with(['errors' => $errors])->withInput();
    }
    
    public function store()
    {
        list($id, $errors) = api_result(ApiVotingTour::class, 'add', [request()->all()]);
        
        if(empty($errors)){
            return redirect($this->redirectTo);
        }
                
        return redirect()->back()->with(['errors' => $errors])->withInput();
    }
}
