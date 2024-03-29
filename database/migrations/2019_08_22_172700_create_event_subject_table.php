<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventSubjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_subject', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('event_id')->unsigned();
            $table->bigInteger('subject_id')->unsigned();
            $table->tinyInteger('iteration')->unsigned()->default(1);
            $table->tinyInteger('eventstatus_id')->unsigned()->default(0);
            $table->tinyInteger('labelStatus')->default(0);
            $table->date('eventDate')->nullable();
            $table->date('minDate')->nullable();
            $table->date('maxDate')->nullable();
            // $table->dateTime('reg_timestamp')->nullable();
            // $table->dateTime('log_timestamp')->nullable();
            $table->date('logDate')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'subject_id', 'iteration']);
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_subject');
    }
}
