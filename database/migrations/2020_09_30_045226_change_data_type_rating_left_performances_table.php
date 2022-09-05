<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDataTypeRatingLeftPerformancesTable extends Migration
{
    public function up()
    {
        Schema::connection('mysql-coaching')->table('rating_left_performances', function ($table) {
            $table->decimal('loan_amount', 11, 2)->nullable()->change();
            $table->decimal('qa_score', 11, 2)->nullable()->change();
            $table->decimal('correction', 11, 2)->nullable()->change();
            $table->decimal('compliance', 11, 2)->nullable()->change();
            $table->decimal('attendance_reliability', 11, 2)->nullable()->change();
            $table->decimal('total', 11, 2)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::connection('mysql-coaching')->table('rating_left_performances', function ($table) {
            $table->integer('loan_amount')->nullable()->change();
            $table->integer('qa_score')->nullable()->change();
            $table->integer('correction')->nullable()->change();
            $table->integer('compliance')->nullable()->change();
            $table->integer('attendance_reliability')->nullable()->change();
            $table->integer('total')->nullable()->change();
        });
    }
}
