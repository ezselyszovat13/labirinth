<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    public function enemy()
    {
        return $this->belongsTo(Enemy::class);
    }

    public function tables() {
        return $this->belongsToMany(Table::class);
    }
}
