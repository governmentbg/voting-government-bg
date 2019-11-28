<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendSingleResultInvite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $org;
    private $votingTour;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($org, $votingTour)
    {
        $this->org = $org;
        $this->votingTour = $votingTour;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //send vote invitation
        $template = 'emails.results';
        $to = $this->org->email;
        $subject = __('custom.results_invite');

        sendEmail($template, ['name' => $this->org->name, 'tourName' => $this->votingTour->name], $to, $subject);
    }
}
