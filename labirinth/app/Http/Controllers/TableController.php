<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;

class TableController extends Controller
{
    public function stepOnTable(Request $request){
        $data = $request->all();
        $clicked_id = $data['clickedId'];
        $pieces = explode('-',$clicked_id);
        $row = $pieces[0];
        $col = $pieces[1];
        $clicked_table_id = $data['clickedTableId'];

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
            $table->update(['was_won' => true]);
            return ['win' => true, 'table' => ''];
        }
            

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

        return ['win' => false, 'table' => $json_table];
    }
}
