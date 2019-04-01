<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotingTourTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('voting_tour', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->tinyInteger('status');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users');
        });

        DB::unprepared("
            CREATE TRIGGER check_insert_voting_tour BEFORE INSERT ON voting_tour
            FOR EACH ROW
            BEGIN
                DECLARE t INT;
                SET t = (SELECT count(*) from voting_tour where status != 6);
                IF (t > 0) THEN
                    SIGNAL SQLSTATE '45000' SET message_text = 'Can not create new voting tour, when there is active one.';
                END IF;
            END;
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
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('voting_tour');
        
        DB::unprepared("DROP TRIGGER IF EXISTS check_insert_voting_tour");

        Schema::enableForeignKeyConstraints();
    }
}
