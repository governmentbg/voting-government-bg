<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('voter_id')->unsigned();
            $table->foreign('voter_id')->references('id')->on('organisations');
            $table->integer('voting_tour_id')->unsigned();
            $table->foreign('voting_tour_id')->references('id')->on('voting_tour');
            $table->string('vote_data');
            $table->tinyInteger('tour_status');
            $table->string('prev_hash');
        });

        DB::unprepared("
            ALTER TABLE votes ADD COLUMN vote_time DATETIME(6) AFTER id;
        ");

        Schema::disableForeignKeyConstraints();

        // Genesis block for votes
        DB::unprepared("
            INSERT INTO votes (vote_time, voter_id, voting_tour_id, vote_data, tour_status, prev_hash) VALUES (CURRENT_TIMESTAMP, 0, 0, 'genesis', 99, '". hash('sha256', config('database.INITIAL_HASH')) ."')
        ");

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('votes');
    }
}
