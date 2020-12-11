<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'user_id'
    ];


    public function user(){
        return $this->belongsTo('App\Models\User');
    }


    public static function get_all()
    {
        return DB::select("select * from `labels`;");
    }
}
