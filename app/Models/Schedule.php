<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'trainer', 'user_id', 'attachment'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
