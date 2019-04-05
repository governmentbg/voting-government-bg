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
            $table->unique(['org_id']);
            $table->integer('voting_tour_id')->unsigned()->nullable();
            $table->foreign('voting_tour_id')->references('id')->on('voting_tour');
            $table->unique(['username', 'voting_tour_id']);
            $table->string('password');
            $table->tinyInteger('active')->nullable();
            $table->string('pw_reset_hash')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
            $table->integer('created_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('id')->on('users');
        });

        DB::unprepared("
            CREATE TRIGGER check_insert BEFORE INSERT ON users
            FOR EACH ROW
            BEGIN
                ". $this->preventUsernameQuery(false) ."
            END;
            CREATE TRIGGER check_update BEFORE UPDATE ON users
            FOR EACH ROW
            BEGIN
                ". $this->preventUsernameQuery(true) ."
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

    private function preventUsernameQuery($update)
    {
        $condition = $update ? 'OLD.id != NEW.id AND ' : '';

        return "
            IF (NEW.org_id IS NULL
                AND (NEW.first_name IS NULL OR NEW.first_name = ''
                OR NEW.last_name IS NULL OR NEW.last_name = ''
                OR NEW.email IS NULL OR NEW.email = ''
                OR NEW.active IS NULL)) THEN
                SIGNAL SQLSTATE '45000' SET message_text = 'If org_id is null the first_name, last_name, email and active fields are required!';
            ELSE
                IF ((NEW.org_id IS NULL AND NEW.voting_tour_id IS NOT NULL) OR (NEW.org_id IS NOT NULL AND NEW.voting_tour_id IS NULL)) THEN
                    SIGNAL SQLSTATE '45000' SET message_text = 'Inconsistent combination of voting_tour_id and org_id';
                ELSE
                    SET @countMinistryUsers = (SELECT count(*) FROM users as u WHERE ". $condition ."u.username = NEW.username AND u.org_id IS NULL);

                    SET @countOrgUsers = (SELECT count(*) FROM users as u WHERE u.username = NEW.username AND u.org_id IS NOT NULL);

                    IF (
                        (NEW.org_id IS NULL AND (@countMinistryUsers > 0 OR @countOrgUsers > 0))
                        OR
                        (NEW.org_id IS NOT NULL AND @countMinistryUsers > 0)
                    ) THEN
                        SIGNAL SQLSTATE '45000' SET message_text = 'Username already exists!';
                    END IF;
                END IF;
            END IF;
        ";
    }
}
