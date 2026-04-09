<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Category extends Model
{
    //
    use HasApiTokens;
    protected $table = 'categories';

     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cat_name',
        'cat_description',
        'cat_image',
        'cat_parent_id',
    ];

    public function parent(){
        return $this->belongsTo(Category::class,'cat_parent_id','id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'cat_parent_id', 'id');
    }

    public function childrenRecursive()
    {
        return $this->hasMany(Category::class, 'cat_parent_id')
                    ->with('childrenRecursive');
    }
}
