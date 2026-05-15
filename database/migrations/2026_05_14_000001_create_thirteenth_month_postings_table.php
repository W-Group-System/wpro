<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThirteenthMonthPostingsTable extends Migration
{
    public function up()
    {
        Schema::create('thirteenth_month_postings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable()->index();
            $table->string('employee_no')->index();
            $table->string('employee_name')->nullable();
            $table->string('department')->nullable();
            $table->string('account_number')->nullable();
            $table->string('half', 10);
            $table->integer('year')->index();
            $table->date('posting_cut_off')->nullable()->index();
            $table->decimal('monthly_salary', 15, 2)->default(0);
            $table->decimal('subliq_amount', 15, 2)->default(0);
            $table->decimal('annual_thirteenth_month', 15, 2)->default(0);
            $table->decimal('first_half_released', 15, 2)->default(0);
            $table->decimal('release_amount', 15, 2)->default(0);
            $table->integer('posted_by')->nullable();
            $table->timestamps();

            $table->unique(['employee_no', 'half', 'year'], 'thirteenth_month_unique_posting');
        });
    }

    public function down()
    {
        Schema::dropIfExists('thirteenth_month_postings');
    }
}
