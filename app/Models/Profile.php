<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperProfile
 */
class Profile extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id', 'user_id'];
    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function getFullNameAttribute(): string
    {
        return $this->firstname.' '.$this->lastname;
    }
}
