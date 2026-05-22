<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubliqAmountToThirteenthMonthPostingsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('thirteenth_month_postings') || Schema::hasColumn('thirteenth_month_postings', 'subliq_amount')) {
            return;
        }

        Schema::table('thirteenth_month_postings', function (Blueprint $table) {
            $table->decimal('subliq_amount', 15, 2)->default(0)->after('monthly_salary');
        });
    }

    public function down()
    {
        if (!Schema::hasTable('thirteenth_month_postings') || !Schema::hasColumn('thirteenth_month_postings', 'subliq_amount')) {
            return;
        }

        Schema::table('thirteenth_month_postings', function (Blueprint $table) {
            $table->dropColumn('subliq_amount');
        });
    }
}
