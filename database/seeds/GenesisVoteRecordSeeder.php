<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Vote;

class GenesisVoteRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Vote::where('voter_id', 0)->where('voting_tour_id', 0)->first()) {
            if (config('database.INITIAL_HASH')) {
                Schema::disableForeignKeyConstraints();

                // Genesis block for votes
                Vote::create([
                    'vote_time' => date('Y-m-d H:i:s'),
                    'voter_id' => 0,
                    'voting_tour_id' => 0,
                    'vote_data' => 'genesis',
                    'tour_status' => 99,
                    'prev_hash' => hash('sha256', config('database.INITIAL_HASH'))
                ]);

                Schema::enableForeignKeyConstraints();
            } else {
                $this->command->warn("Initial hash not set for environment!");
            }
        } else {
            if (isset($this->command)) {
                $this->command->warn("Genesis block already created!");
            }
        }
    }
}
