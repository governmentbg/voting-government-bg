<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganisationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('organisations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('eik');
            $table->integer('voting_tour_id')->unsigned();
            $table->foreign('voting_tour_id')->references('id')->on('voting_tour');
            $table->unique(['eik', 'voting_tour_id']);
            $table->string('name');
            $table->string('address', 512);
            $table->string('representative', 512);
            $table->string('email');
            $table->boolean('in_ap');
            $table->boolean('is_candidate');
            $table->text('description')->nullable();
            $table->text('reference')->nullable();
            $table->tinyInteger('status');
            $table->tinyInteger('status_hint');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users');
        });

        DB::unprepared("
            CREATE TRIGGER check_insert_organisations BEFORE INSERT ON organisations
            FOR EACH ROW
            BEGIN
                ". $this->checkFieldConsistency() ."
            END;
            CREATE TRIGGER check_update_organisations BEFORE UPDATE ON organisations
            FOR EACH ROW
            BEGIN
                ". $this->checkFieldConsistency() ."
            END;"
        );

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

        Schema::dropIfExists('organisations');

        DB::unprepared("DROP TRIGGER IF EXISTS check_insert_organisations");
        DB::unprepared("DROP TRIGGER IF EXISTS check_update_organisations");

        Schema::enableForeignKeyConstraints();
    }

    private function checkFieldConsistency()
    {
        return "
            IF (NEW.is_candidate != 0 AND (NEW.description IS NULL OR NEW.reference IS NULL)) THEN
                SIGNAL SQLSTATE '45000' SET message_text = 'If is_candidate is not null, the description and reference fields are required!';
            END IF;
        ";
    }
}
