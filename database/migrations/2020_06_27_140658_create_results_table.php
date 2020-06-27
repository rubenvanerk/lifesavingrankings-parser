<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->integer('time')->unsigned();
            $table->foreignId('event_id')->constrained()->onDelete('set null');
            $table->foreignId('competition_id')->constrained()->onDelete('cascade');
            $table->integer('time_setter_id')->unsigned();
            $table->string('time_setter_type')->unsigned();
            $table->integer('round')->unsigned()->nullable();
            $table->integer('heat')->unsigned()->nullable();
            $table->integer('lane')->unsigned()->nullable();
            $table->integer('reaction_time')->unsigned()->nullable();
            $table->timestamps();

            $table->index(['time_setter_id', 'time_setter_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('results');
    }
}
