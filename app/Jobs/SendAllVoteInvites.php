<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Jobs\SendVoteInvite;
use App\Http\Controllers\Api\OrganisationController as ApiOrg;
use App\Organisation;

class SendAllVoteInvites implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     *  Current Voting tour status.
     * @var int
     */
    private $status;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($status)
    {
        $this->status = $status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        list($organisations, $errors) = api_result(ApiOrg::class, 'search', [
            'filters' => [
                'statuses' => Organisation::getApprovedStatuses(),
            ],
            'with_pagination' => false
        ]);

        if (!empty($errors)) {
            if (!is_string($errors)) {
                $error = json_encode($errors, JSON_UNESCAPED_UNICODE);
            } else {
                $error = $errors;
            }

            throw new \Exception($error);
        }

        foreach ($organisations as $key => $organisation) {
            SendVoteInvite::dispatch($organisation, $this->status);
        }
    }
}
