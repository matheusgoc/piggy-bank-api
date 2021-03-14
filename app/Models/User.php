<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes, HasFactory;

    protected $fillable = [
        'email', 'password', 'pin', 'pinned_at'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $timestamps = true;

    public function profile() {

        return $this->hasOne('App\Models\Profile');
    }

    public function categories() {

        return $this->belongsToMany('App\Models\Category')->withTimestamps();
    }

    public function transactions() {

        return $this->belongsToMany('App\Models\Transaction')->withTimestamps();
    }

    public function institutions() {

        return $this->hasMany('App\Models\Institution');
    }
}
