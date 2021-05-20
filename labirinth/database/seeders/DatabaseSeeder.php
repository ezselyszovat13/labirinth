<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Table;
use App\Models\Room;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tables')->truncate();
        DB::table('rooms')->truncate();

        $table = array (
            array(0,3,0,0,0,0,0,0),
            array(1,2,4,0,0,0,0,0),
            array(0,4,0,0,0,0,0,0),
            array(0,0,0,0,0,0,0,0),
            array(0,0,0,0,0,0,0,0),
            array(0,0,0,0,0,0,0,0),
            array(0,0,0,0,0,0,0,0),
            array(0,0,0,0,0,0,0,0),
        );

        $json_table = json_encode($table);

        Table::create(['name' => 'Tábla2',
                       'statuses' => $json_table,
                       'win_row' => 4,
                       'win_col' => 4,
                       'was_won' => false]);
    }
}
