<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\User;
use App\Models\Room;
use App\Models\Enemy;
use Auth;

class TableController extends Controller
{
    public function stepOnTable(Request $request){
        $data = $request->all();
        $clicked_id = $data['clickedId'];
        $pieces = explode('-',$clicked_id);
        $row = $pieces[0];
        $col = $pieces[1];
        $clicked_table_id = $data['clickedTableId'];
        $user = Auth::user();

        $table = Table::where('id', $clicked_table_id)->first();
        if(!$table){
            return redirect()->route('getTable');
        }

        $json_table = json_decode($table->statuses);

        $winner_row = $table->win_row;
        $winner_col = $table->win_col;

        foreach ($json_table as &$table_row){
            foreach ($table_row as &$status){
                if($status === 2)
                    $status = 3;
                elseif($status === 4)
                    $status = 0;
            }
        }

        if($row === $winner_row && $col === $winner_col){
            $table->update(['was_won' => true,
                            'is_active' => false]);
            
            $new_table = array (
                array(0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0),
            );
    
            $start_row = rand(0,7);
            $start_col = rand(0,7);

            $new_table[$start_row][$start_col] = 1;

            if($start_row-1>=0)
                $new_table[$start_row-1][$start_col] = 4;
            if($start_row+1<=7)
                $new_table[$start_row+1][$start_col] = 4;
            if($start_col-1>=0)
                $new_table[$start_row][$start_col-1] = 4;
            if($start_col+1<=7)
                $new_table[$start_row][$start_col+1] = 4;

            $json_table = json_encode($new_table);
    
            $win_row = rand(0,7);
            $win_col = rand(0,7); 

            do {
                $win_row = rand(0,7);
                $win_col = rand(0,7); 
            } while ($start_row == $win_row && $start_col == $win_col);

            $count = Table::count();

            $table = Table::create(['name' => 'T'.$count,
                           'statuses' => $json_table,
                           'win_row' => $win_row,
                           'win_col' => $win_col,
                           'was_won' => false,
                           'is_active' => true]);

            $user->tables()->save($table);

            return ['win' => true, 'table' => ''];
        }
        $user->increasePoints();
        $user->decreaseSteps();

        $json_table[$row][$col] = 2;

        //possible step up
        if($row-1 >= 0){
            if($json_table[$row-1][$col] != 3 && $json_table[$row-1][$col] != 1){
                $json_table[$row-1][$col] = 4;
            }
        }
        //possible step down
        if($row+1 <= 7){
            if($json_table[$row+1][$col] != 3 && $json_table[$row+1][$col] != 1){
                $json_table[$row+1][$col] = 4;
            }
        }

        //possible step left
        if($col-1 >= 0){
            if($json_table[$row][$col-1] != 3 && $json_table[$row][$col-1] != 1){
                $json_table[$row][$col-1] = 4;
            }
        }
        //possible step down
        if($col+1 <= 7){
            if($json_table[$row][$col+1] != 3 && $json_table[$row][$col+1] != 1){
                $json_table[$row][$col+1] = 4;
            }
        }

        $table->update(['statuses' => json_encode($json_table)]);
        $user->update(['points' => $user->points, 'remaining_steps' => $user->remaining_steps]);

        $room = $table->rooms()->where('row',$row)->where('col',$col)->first();
        $enemy = Enemy::where('id',$room->enemy_id)->first();

        return ['win' => false, 'table' => $json_table, 'room' => $room, 'enemy' => $enemy, 'user' => $user];
    }
}
