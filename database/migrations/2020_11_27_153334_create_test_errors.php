<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestErrors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_errors', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('result_id');
            $table->integer('site_id');
            $table->string('url');
            $table->integer('timecost');
            $table->integer('status_code');
            $table->longText('error_message');
            $table->longText('error_content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_errors');
    }
}
