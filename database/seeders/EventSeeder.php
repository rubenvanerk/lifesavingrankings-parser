<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('events')->insert([
            'code' => '100mcf',
            'type' => 1,
        ]);
        DB::table('events')->insert([
            'code' => '50mc',
            'type' => 1,
        ]);
        DB::table('events')->insert([
            'code' => '200os',
            'type' => 1,
        ]);
        DB::table('events')->insert([
            'code' => '100mtf',
            'type' => 1,
        ]);
        DB::table('events')->insert([
            'code' => '100rm',
            'type' => 1,
        ]);
        DB::table('events')->insert([
            'code' => '200sls',
            'type' => 1,
        ]);
        DB::table('events')->insert([
            'code' => '50os',
            'type' => 2,
        ]);
        DB::table('events')->insert([
            'code' => '50f',
            'type' => 2,
        ]);
        DB::table('events')->insert([
            'code' => '50ff',
            'type' => 2,
        ]);
        DB::table('events')->insert([
            'code' => '50mcr',
            'type' => 2,
        ]);
        DB::table('events')->insert([
            'code' => '50mpt',
            'type' => 2,
        ]);
        DB::table('events')->insert([
            'code' => '25mc',
            'type' => 2,
        ]);
        DB::table('events')->insert([
            'code' => '50tt',
            'type' => 2,
        ]);
        DB::table('events')->insert([
            'code' => '50mcf',
            'type' => 2,
        ]);
        DB::table('events')->insert([
            'code' => '4-50or',
            'type' => 2,
        ]);
        DB::table('events')->insert([
            'code' => '4-25mc',
            'type' => 3,
        ]);
        DB::table('events')->insert([
            'code' => '4-50mr',
            'type' => 3,
        ]);
        DB::table('events')->insert([
            'code' => '100os',
            'type' => 1,
        ]);
    }
}
