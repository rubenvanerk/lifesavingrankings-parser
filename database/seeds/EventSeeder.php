<?php

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
        DB::table('rankings_event')->insert([
            'id' => 1,
            'code' => '100mcf',
            'type' => 1,
        ]);
        DB::table('rankings_event')->insert([
            'id' => 2,
            'code' => '50mc',
            'type' => 1,
        ]);
        DB::table('rankings_event')->insert([
            'id' => 3,
            'code' => '200os',
            'type' => 1,
        ]);
        DB::table('rankings_event')->insert([
            'id' => 4,
            'code' => '100mtf',
            'type' => 1,
        ]);
        DB::table('rankings_event')->insert([
            'id' => 5,
            'code' => '100rm',
            'type' => 1,
        ]);
        DB::table('rankings_event')->insert([
            'id' => 6,
            'code' => '200sls',
            'type' => 1,
        ]);
        DB::table('rankings_event')->insert([
            'id' => 7,
            'code' => '50os',
            'type' => 2,
        ]);
        DB::table('rankings_event')->insert([
            'id' => 8,
            'code' => '50f',
            'type' => 2,
        ]);
        DB::table('rankings_event')->insert([
            'id' => 9,
            'code' => '50ff',
            'type' => 2,
        ]);
        DB::table('rankings_event')->insert([
            'id' => 10,
            'code' => '50mcr',
            'type' => 2,
        ]);
        DB::table('rankings_event')->insert([
            'id' => 11,
            'code' => '50mpt',
            'type' => 2,
        ]);
        DB::table('rankings_event')->insert([
            'id' => 12,
            'code' => '25mc',
            'type' => 2,
        ]);
        DB::table('rankings_event')->insert([
            'id' => 13,
            'code' => '50tt',
            'type' => 2,
        ]);
        DB::table('rankings_event')->insert([
            'id' => 14,
            'code' => '50mcf',
            'type' => 2,
        ]);
        DB::table('rankings_event')->insert([
            'id' => 15,
            'code' => '4-50or',
            'type' => 2,
        ]);
        DB::table('rankings_event')->insert([
            'id' => 16,
            'code' => '4-25mc',
            'type' => 3,
        ]);
        DB::table('rankings_event')->insert([
            'id' => 17,
            'code' => '4-50mr',
            'type' => 3,
        ]);
        DB::table('rankings_event')->insert([
            'id' => 18,
            'code' => '100os',
            'type' => 1,
        ]);
    }
}
