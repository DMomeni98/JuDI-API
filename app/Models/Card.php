<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'due',
        'category_id',
        'with_star',
        'is_done',
        'user_id',
        'repetitive_id'
    ];
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    public static function get_all()
    {
        return DB::select("select * from `cards`;");
    }
}
