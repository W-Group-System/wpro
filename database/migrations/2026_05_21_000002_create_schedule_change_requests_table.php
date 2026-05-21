<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleChangeRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('schedule_change_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->date('date_from');
            $table->date('date_to');
            $table->string('time_in_from')->nullable();
            $table->string('time_in_to')->nullable();
            $table->string('time_out_from')->nullable();
            $table->string('time_out_to')->nullable();
            $table->decimal('working_hours', 5, 2)->nullable();
            $table->text('reason')->nullable();
            $table->string('status')->default('Pending');
            $table->tinyInteger('level')->default(0);
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->date('approved_date')->nullable();
            $table->text('approval_remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedule_change_requests');
    }
}
