<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Table;
use App\Models\Room;
use App\Models\User;
use App\Models\Enemy;

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

        $table_array = array (
            array(4,0,0,0,0,0,0,0),
            array(1,4,0,0,0,0,0,0),
            array(4,0,0,0,0,0,0,0),
            array(0,0,0,0,0,0,0,0),
            array(0,0,0,0,0,0,0,0),
            array(0,0,0,0,0,0,0,0),
            array(0,0,0,0,0,0,0,0),
            array(0,0,0,0,0,0,0,0),
        );

        $table2_array = array (
            array(0,0,0,0,0,0,0,0),
            array(0,0,0,0,0,0,0,0),
            array(0,0,0,0,0,0,0,0),
            array(0,0,0,0,0,0,0,0),
            array(4,0,0,0,0,0,0,0),
            array(1,4,0,0,0,0,0,0),
            array(4,0,0,0,0,0,0,0),
            array(0,0,0,0,0,0,0,0),
        );

        $json_table = json_encode($table_array);
        $json_table2 = json_encode($table2_array);

        $table = Table::create(['name' => 'Tábla1',
                       'statuses' => $json_table,
                       'win_row' => 4,
                       'win_col' => 4,
                       'was_won' => false,
                       'is_active' => true]);

        $table2 = Table::create(['name' => 'Tábla2',
                        'statuses' => $json_table2,
                        'win_row' => 6,
                        'win_col' => 3,
                        'was_won' => false,
                        'is_active' => true]);

        $user = User::factory()->create(['name' => 'Teszt Péter',
                                'email' => 'testp@labirinth.hu',
                                'remaining_steps' => 20,
                                'health' => 100,
                                'points' => 0,
                                'gold' => 0]);

        $user2 = User::factory()->create(['name' => 'Teszt Béla',
                                'email' => 'testb@labirinth.hu',
                                'remaining_steps' => 20,
                                'health' => 100,
                                'points' => 0,
                                'gold' => 0]);

                                
        $enemy1 = Enemy::create(['type' => 'óriás',
                                 'name' => 'Nagyláb']);

        $enemy2 = Enemy::create(['type' => 'óriás',
                                 'name' => 'Gülüszemű']);

        $enemy3 = Enemy::create(['type' => 'óriás',
                                 'name' => 'Bunkósbotos']);

        $enemy4 = Enemy::create(['type' => 'boszorkány',
                                 'name' => 'Bibircsóka']);

        $enemy5 = Enemy::create(['type' => 'akadály',
                                 'name' => 'kő']);
    
        $enemy6 = Enemy::create(['type' => 'akadály',
                                 'name' => 'fa']);

        //FREE
        Room::create(['type' => 'FREE']);

        //ENEMY ROOMS
        $room = Room::create(['type' => 'ENEMY']);
        $room->enemy()->associate($enemy1)->save();

        $room2 = Room::create(['type' => 'ENEMY']);
        $room2->enemy()->associate($enemy2)->save();

        $room3 = Room::create(['type' => 'ENEMY']);
        $room3->enemy()->associate($enemy3)->save();

        $room4 = Room::create(['type' => 'ENEMY']);
        $room4->enemy()->associate($enemy4)->save();

        //BARRIER ROOMS

        $room5 = Room::create(['type' => 'BARRIER']);
        $room5->enemy()->associate($enemy5)->save();

        $room6 = Room::create(['type' => 'BARRIER']);
        $room6->enemy()->associate($enemy6)->save();

        $user->tables()->save($table);
        $user2->tables()->save($table2);

        $enemy_count = Enemy::count();
        for($x = 0; $x < 8; $x++){
            for($y = 0; $y < 8; $y++){
                if($table_array[$x][$y]!=1){
                    $gen_enemy = rand(0,1);
                    if($gen_enemy){
                        $enemy_id = rand(1,$enemy_count);
                        $room_id = Room::where('enemy_id',$enemy_id)->first()->id;
                        $table->rooms()->attach($room_id,['row' => $x, 'col' => $y]);
                    }
                    else{
                        $table->rooms()->attach(1,['row' => $x, 'col' => $y]);
                    }
                }   
            }
        }

        for($x = 0; $x < 8; $x++){
            for($y = 0; $y < 8; $y++){
                if($table2_array[$x][$y]!=1){
                    $gen_enemy = rand(0,1);
                    if($gen_enemy){
                        $enemy_id = rand(1,$enemy_count);
                        $room_id = Room::where('enemy_id',$enemy_id)->first()->id;
                        $table2->rooms()->attach($room_id,['row' => $x, 'col' => $y]);
                    }
                    else{
                        $table2->rooms()->attach(1,['row' => $x, 'col' => $y]);
                    }
                }
            }
        }
    }
}
