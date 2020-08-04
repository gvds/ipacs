<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eventStatus', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned();
            $table->text('eventstatus',25);
        });
        DB::unprepared("INSERT INTO `eventStatus` (`id`, `eventstatus`) VALUES
        (0, 'Pending'),
        (1, 'Primed'),
        (2, 'Labels Generated'),
        (3, 'Logged'),
        (4, 'Logged Late'),
        (5, 'Missed'),
        (6, 'Cancelled')");
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eventStatus');
    }
}