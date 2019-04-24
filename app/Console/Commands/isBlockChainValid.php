<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\VoteController as ApiVote;

class isBlockChainValid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validate:blockChain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validates votes blockchain';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        list($result, $errors) = api_result(ApiVote::class, 'isBlockChainValid');

        if (!empty($errors)) {
            $array = json_decode(json_encode($errors), true);
            print_r('['.date('d-m-Y H:i:s') .']'.' - '. __('custom.inconsistent_record') .' : '. $array['inconsistent_record'] . "\n");
        }

        if ($result) {
            print_r('['.date('d-m-Y H:i:s') .']'.' - '. $result . "\n");
        }
    }
}
