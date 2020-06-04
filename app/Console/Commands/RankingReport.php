<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\VotingTourController as ApiVotingTour;
use App\Http\Controllers\Api\VoteController as ApiVote;
use App\Vote;
use Carbon\Carbon;

class RankingReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'generate:rankingReport'.
                           ' {--votingTour= : Voting tour id (default value: latest voting tour)}'.
                           ' {--votingIndex=0 : 0 - main voting, 1 - first balotage, 2 - second balotage, etc.}'.
                           ' {--orgsCount=14 : Number of the organisations to be included in the result strating from the most voted one}'.
                           ' {--show : Display CSV file contents on the screen}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates ranking report containing information about voted organisations and stores it in CSV file';

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
        if ($this->option('votingTour')) {
            $tourId = $this->option('votingTour');
        } else {
            list($votingTour, $errors) = api_result(ApiVotingTour::class, 'getLatestVotingTour');
            if (!empty($votingTour)) {
                $tourId = $votingTour->id;
            } else {
                $this->error($errors->message);
                return;
            }
        }

        $orgsCount = $this->option('orgsCount') ?? 14;
        $votingIndex = $this->option('votingIndex') ?? 0;

        $this->info('Voting tour: '. $tourId .', voting index: '. $votingIndex .', number of the organisations to be included in the result: '. $orgsCount ."\n");

        // list ranking
        $params = [
            'tour_id'            => $tourId,
            'with_voter_turnout' => false
        ];
        list($data, $errors) = api_result(ApiVote::class, 'getLatestRanking', $params);

        if (!empty($data) && isset($data->ranking) && !empty($data->ranking)) {
            $votingCount = $data->voting_count;
            if ($votingIndex < $votingCount) {
                $data = collect($data->ranking)->take($orgsCount)->all();

                // set csv file name
                $filename = 'rankingReport-'. $tourId .'-'. $votingIndex .'-'. $orgsCount .'.csv';

                // generate and store csv file in default storage path
                $this->generateCSV($filename, $data, $tourId, $votingIndex);

                if (Storage::disk('local')->exists('csv/'. $filename)) {
                    $this->info('CSV file with name "'. $filename .'" successfully created in "'. storage_path('app/csv') .'" directory');

                    if ($this->option('show')) {
                        // print file contents on the screen
                        print_r("\n". Storage::disk('local')->get('csv/'. $filename));
                    }
                } else {
                    $this->error('Failed to store CSV file');
                }
            } else {
                $this->error('Invalid voting index - it should be lower than '. $votingCount);
            }
        } else {
            $errors = collect($errors)->all();
            if (isset($errors['message'])) {
                $errMsg = $errors['message'];
            } else {
                $errors = array_shift($errors);
                $errMsg = is_array($errors) ? array_shift($errors) : __('custom.list_ranking_fail') .' - '. $errors;
            }
            $this->error($errMsg);
        }
    }

    private function generateCSV($filename, $data, $tourId, $votingIndex)
    {
        if (!Storage::disk('local')->exists('csv')) {
            Storage::disk('local')->makeDirectory('csv');
        }

        $handle = fopen(storage_path('app/csv/'. $filename), 'w+');

        // add bom UTF-8
        fputs($handle, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

        $csvRow = [
            __('custom.ranking_number'),
            __('custom.organisation'),
            __('custom.eik'),
            __('custom.voter_number'),
            __('custom.voter'),
            __('custom.voter_eik'),
            __('custom.vote_datetime')
        ];
        fputcsv($handle, $csvRow);

        $counter = 0;
        foreach ($data as $orgId => $orgData) {
            $votersData = Vote::getVotersByOrg($tourId, $votingIndex, $orgId);
            ++$counter;
            if (!empty($votersData)) {
                foreach ($votersData as $key => $voterData) {
                    $csvRow = [
                        ($key == 0) ? $counter : '',
                        ($key == 0) ? $orgData->name : '',
                        ($key == 0) ? $orgData->eik : '',
                        $key + 1,
                        $voterData['name'],
                        $voterData['eik'],
                        Carbon::parse($voterData['vote_time'])->format('Y-m-d H:i:s')
                    ];
                    fputcsv($handle, $csvRow);
                }
            } else {
                $csvRow = [
                    $counter,
                    $orgData->name,
                    $orgData->eik,
                    (isset($orgData->votes) && isset($orgData->votes->$votingIndex)) ? 0 : '',
                    '',
                    '',
                    ''
                ];
                fputcsv($handle, $csvRow);
            }
        }

        fclose($handle);
    }
}
