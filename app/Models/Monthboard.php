<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class monthboard extends Model
{
    use HasFactory;
    protected $fillable = [
        'note',
        'user_id'
    ];

    protected $hidden = [
        'updated_at',
        'created_at'
    ];
    
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
}
