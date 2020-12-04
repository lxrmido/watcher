<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteSnapshotBatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_snapshot_batches', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('timecost')->default(0);
            $table->integer('total')->default(0);
            $table->integer('finished')->default(0);
            $table->integer('error')->default(0);
            $table->integer('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_snapshot_batches');
    }
}
