<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\VotingTour;

class SendVoteInvite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $org;
    
    /**
     *  Voting Tour status.
     * @var int
     */
    private $status;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($org, $status)
    {
        $this->org = $org;
        $this->status = $status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //send vote invitation
        $template = 'emails.vote_invite'; 
        $to = $this->org->email;
        $subject = __('custom.vote_invite');
        
        if($this->status == VotingTour::STATUS_BALLOTAGE){
            $subject .= ' - ' . __('custom.ballotage'); 
        }
        
        sendEmail($template, ['name' => $this->org->name ], $to, $subject);
    }
}
