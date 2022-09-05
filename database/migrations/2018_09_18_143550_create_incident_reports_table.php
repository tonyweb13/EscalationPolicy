<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncidentReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('complainant_id');
            $table->integer('hr_user_id');
            $table->text('remarks');
            $table->boolean('is_generate_nte_invalid_ir');
            $table->date('date_admin_hearing')->nullable();
            $table->string('time_admin_hearing')->nullable();
            $table->string('meeting_place')->nullable();
            $table->boolean('is_under_investigation')->nullable();
            $table->integer('irr_id')->nullable();
            $table->string('type_invite')->nullable();
            $table->integer('ageing')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incident_reports');
    }
}
