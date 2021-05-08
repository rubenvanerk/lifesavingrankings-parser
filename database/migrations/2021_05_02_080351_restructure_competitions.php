<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RestructureCompetitions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('competition_configs', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'city',
                'country_id',
                'start_date',
                'end_date',
                'timekeeping',
                'original_name',
                'comment',
            ]);
            $table->foreignId('competition_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('competition_configs', function (Blueprint $table) {
            $table->string('name');
            $table->string('city');
            $table->integer('country_id')->unsigned()->index();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('timekeeping')->nullable();
            $table->string('original_name')->nullable();
            $table->string('comment')->nullable();
            $table->dropColumn('competition_id');
        });
    }
}
