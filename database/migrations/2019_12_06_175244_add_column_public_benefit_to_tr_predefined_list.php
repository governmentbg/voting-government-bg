<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnPublicBenefitToTrPredefinedList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tr_predefined_list')) {
            if (! Schema::hasColumn('tr_predefined_list', 'public_benefits')) {
                Schema::table('tr_predefined_list', function (Blueprint $table) {
                    $table->tinyInteger('public_benefits')->default(0);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('tr_predefined_list')) {
            if (Schema::hasColumn('tr_predefined_list', 'public_benefits')) {
                Schema::table('tr_predefined_list', function (Blueprint $table) {
                    $table->dropColumn('public_benefits');
                });
            }
        }
    }
}
