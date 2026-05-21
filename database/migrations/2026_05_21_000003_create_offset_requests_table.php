<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffsetRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('offset_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->date('ot_date');
            $table->decimal('ot_hours', 5, 2)->nullable();
            $table->date('date_to_use');
            $table->text('reason')->nullable();
            $table->string('attachment')->nullable();
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
        Schema::dropIfExists('offset_requests');
    }
}
