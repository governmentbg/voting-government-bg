<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sender_org_id')->unsigned()->nullable();
            $table->foreign('sender_org_id')->references('id')->on('organisations');
            $table->integer('sender_user_id')->unsigned()->nullable();
            $table->foreign('sender_user_id')->references('id')->on('users');
            $table->string('subject');
            $table->text('body');
            $table->datetime('read')->nullable();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')->references('id')->on('messages');
            $table->integer('recipient_org_id')->unsigned()->nullable();
            $table->foreign('recipient_org_id')->references('id')->on('organisations');
            $table->integer('voting_tour_id')->unsigned();
            $table->foreign('voting_tour_id')->references('id')->on('voting_tour');
            $table->datetime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->datetime('updated_at')->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users');
        });

        DB::unprepared("
            CREATE TRIGGER check_insert_message BEFORE INSERT ON messages
            FOR EACH ROW
            BEGIN
                ". $this->checkMessageFieldConsistency() ."
            END;
            CREATE TRIGGER check_update_message BEFORE UPDATE ON messages
            FOR EACH ROW
            BEGIN
                ". $this->checkMessageFieldConsistency() ."
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

        Schema::dropIfExists('messages');

        DB::unprepared("DROP TRIGGER IF EXISTS check_insert_organisations");
        DB::unprepared("DROP TRIGGER IF EXISTS check_update_organisations");

        Schema::enableForeignKeyConstraints();
    }

    private function checkMessageFieldConsistency()
    {
        return "
            IF (NEW.sender_org_id IS NULL AND (NEW.sender_user_id IS NULL OR NEW.recipient_org_id IS NULL)) THEN
                SIGNAL SQLSTATE '45000' SET message_text = 'If sender_org_id is null, the sender_user_id and recipient_org_id fields are required!';
            ELSE
                IF (NEW.sender_org_id IS NOT NULL AND (NEW.sender_user_id IS NOT NULL OR NEW.recipient_org_id IS NOT NULL)) THEN
                    SIGNAL SQLSTATE '45000' SET message_text = 'If sender_org_id is not null, the sender_user_id and recipient_org_id fields must be null!';
                END IF;
            END IF;
        ";
    }
}
