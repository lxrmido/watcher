<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteSnapshots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_snapshots', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('site_id');
            $table->integer('batch_id');
            $table->string('url');
            $table->integer('size');
            $table->string('oss_url')->nullable();
            $table->integer('timecost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_snapshots');
    }
}
