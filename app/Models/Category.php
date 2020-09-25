<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCategory
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function users() {
        return $this->belongsToMany('App\Models\User')->withTimestamps();
    }

    public function parents() {

        return $this->belongsToMany(
            'App\Models\Category',
            'categories_groups',
            'sub_id',
            'parent_id'
        );
    }

    public function subs() {

        return $this->belongsToMany(
            'App\Models\Category',
            'categories_groups',
            'parent_id',
            'sub_id'
        );
    }

    public function transactions() {

        return $this->belongsToMany(
            'App\Models\Transaction',
            'transaction_user',
            'transaction_id',
            'category_id'
        );
    }
}
