<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendVoteInvite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $org;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($org)
    {
        $this->org = $org;
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
        
        sendEmail($template, ['name' => $this->org->name ], $to, $subject);
    }
}
