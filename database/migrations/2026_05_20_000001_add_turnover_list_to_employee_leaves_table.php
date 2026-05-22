<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTurnoverListToEmployeeLeavesTable extends Migration
{
    public function up()
    {
        Schema::table('employee_leaves', function (Blueprint $table) {
            $table->string('turnover_list')->nullable()->after('leave_file');
            $table->tinyInteger('turnover_notif_3')->nullable()->after('turnover_list');
            $table->tinyInteger('turnover_notif_2')->nullable()->after('turnover_notif_3');
            $table->tinyInteger('turnover_notif_1')->nullable()->after('turnover_notif_2');
        });
    }

    public function down()
    {
        Schema::table('employee_leaves', function (Blueprint $table) {
            $table->dropColumn(['turnover_list', 'turnover_notif_3', 'turnover_notif_2', 'turnover_notif_1']);
        });
    }
}
