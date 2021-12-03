<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    
    protected $table = 'articles';
    protected $fillable = [
        'title', 'description', 'trainer', 'user_id', 'attachment'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
      
    //define $article->submitted_date
    public function getSubmittedDateAttribute(){
        return $this->created_at->format('d/m/Y');
    }
    
}

