<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompetitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competition_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city');
            $table->integer('country_id')->unsigned()->index();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('timekeeping')->nullable();
            $table->json('parser_config')->nullable();
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
        Schema::dropIfExists('competitions');
    }
}
