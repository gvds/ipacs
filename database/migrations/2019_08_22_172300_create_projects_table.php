<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('project',50);
            $table->bigInteger('redcapProject_id')->unsigned()->nullable();
            $table->integer('owner')->unsigned()->nullable();
            $table->boolean('active')->default(true);
            $table->string('subject_id_prefix',10)->nullable();
            $table->smallInteger('subject_id_digits')->unsigned()->nullable();
            $table->string('last_subject_id')->nullable();
            $table->string('storageProjectName',40)->nullable();
            $table->string('label_id',40)->nullable();
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
        Schema::dropIfExists('projects');
    }
}
