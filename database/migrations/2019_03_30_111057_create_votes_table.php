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
