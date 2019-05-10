<?php

use Illuminate\Database\Seeder;
use App\VotingTour;
use App\Organisation;
use App\Vote;
use Faker\Factory as Faker;

class VotesSeeder extends Seeder
{
    const VOTE_RECORDS = 13000;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->faker = Faker::create();

        foreach (range(1, self::VOTE_RECORDS) as $i) {
            $tourIds = VotingTour::select('id')->orderBy('created_at', 'DESC')->first();

            $tourId = $tourIds ? $tourIds['id'] : '';

            $orgIds = Organisation::select('id')->pluck('id');

            $singleOrg = !empty($orgIds) ? $this->faker->unique()->randomElement($orgIds) : '';

            $voteData = $orgIds->isNotEmpty() ? $this->faker->randomElements($orgIds, 14) : '';

            if ($voteData) {
                $voteData = implode(', ', $voteData);
            }

            $prevRecord = Vote::orderBy('vote_time', 'DESC')->first();

            $t = microtime(true);
            $micro = sprintf('%06d',($t - floor($t)) * 1000000);
            $d = new \DateTime(date('Y-m-d H:i:s.'. $micro, $t));

            $hash = '';
            if (!is_null($prevRecord)) {
                $hash = hash('sha256',
                    $prevRecord->vote_time .
                    $prevRecord->voter_id .
                    $prevRecord->voting_tour_id .
                    $prevRecord->vote_data .
                    $prevRecord->tour_status .
                    $prevRecord->prev_hash
                );
            }

            Vote::create([
                'vote_time' => $d->format('Y-m-d H:i:s.u'),
                'voter_id' => $singleOrg,
                'voting_tour_id' => $tourId,
                'vote_data' => $voteData,
                'tour_status' => VotingTour::STATUS_VOTING,
                'prev_hash' => $hash
            ]);
        }
    }
}
