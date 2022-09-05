<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnFormTypeCoachingLogTable extends Migration
{
    public function up()
    {
        Schema::connection('mysql-coaching')->table('coaching_logs', function ($table) {
            $table->string('form_type')->nullable()->after('performance_id');
        });
    }

    public function down()
    {
        Schema::connection('mysql-coaching')->table('coaching_logs', function ($table) {
            $table->dropColumn('form_type');
        });
    }
}
