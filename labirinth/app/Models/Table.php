<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rooms() {
        return $this->belongsToMany(Room::class);
    }

    protected $fillable = ['name', 'statuses', 'win_row', 'win_col', 'was_won', 'is_active'];
}
