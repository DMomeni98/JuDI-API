<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory;

    protected $table = 'labels';
    
    protected $fillable = [
        'name',
        'user_id'
    ];

    protected $hidden = [
        'updated_at',
        'created_at'
    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }


    public static function get_all()
    {
        return DB::select("select * from `labels`;");
    }
}
