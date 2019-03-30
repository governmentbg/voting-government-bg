<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
        Schema::disableForeignKeyConstraints();

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username');
            $table->integer('org_id')->unsigned()->nullable();
            $table->foreign('org_id')->references('id')->on('organisations');
            $table->unique(['username', 'org_id']);
            $table->string('password');
            $table->tinyInteger('active')->nullable();
            $table->string('pw_reset_hash')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->datetime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->datetime('updated_at')->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users');
        });

        DB::unprepared("
            CREATE TRIGGER check_insert BEFORE INSERT ON users
            FOR EACH ROW
            BEGIN
                ". $this->preventUsernameQuery() ."
            END;
            CREATE TRIGGER check_update BEFORE UPDATE ON users
            FOR EACH ROW
            BEGIN
                ". $this->preventUsernameQuery() ."
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

       Schema::dropIfExists('users');

       DB::unprepared("DROP TRIGGER IF EXISTS check_insert");
       DB::unprepared("DROP TRIGGER IF EXISTS check_update");

       Schema::enableForeignKeyConstraints();
   }

   private function preventUsernameQuery()
   {
       return "
        IF (NEW.org_id IS NULL
            AND (NEW.first_name IS NULL OR NEW.first_name = ''
            OR NEW.last_name IS NULL OR NEW.last_name = ''
            OR NEW.email IS NULL OR NEW.email = ''
            OR NEW.active IS NULL)) THEN
            SIGNAL SQLSTATE '45000' SET message_text = 'If org_id is null the first_name, last_name, email and active fields are required!';
        ELSE
            SET @count = (
                SELECT COUNT(*) FROM users AS u
                WHERE u.username = NEW.username AND NOT (u.org_id IS NOT NULL AND NEW.org_id IS NOT NULL)
            );

            IF (@count > 0) THEN
                SIGNAL SQLSTATE '45000' SET message_text = 'Username already exists!';
            END IF;
        END IF;
       ";
   }
}
