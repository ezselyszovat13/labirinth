<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;

class MainController extends Controller
{
    public function getTable(){
        //0 - not visited
        //1 - start
        //2 - your place
        //3 - already visited
        //4 - visitable (not visited yet)
        //5 - end

        $table = Table::all()->first();
        $table_id = $table->id;
        $table = json_decode($table->statuses);

        return view('main', compact('table','table_id'));
    }
}
