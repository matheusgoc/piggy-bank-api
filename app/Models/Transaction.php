<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * @property TransactionUser $currentUserTransaction
 * @property Transaction[] $subTransactions
 * @mixin IdeHelperTransaction
 */
class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'place',
        'description',
        'ordered_at'
    ];

    protected $casts = [
        'ordered_at' => 'timestamp'
    ];

    public function users() {

        return $this->belongsToMany('App\Models\User');
    }

    public function currentUserTransaction() {

        return $this->hasOne('App\Models\TransactionUser')
            ->where('user_id', Auth::id());
    }

    public function subTransactions() {

        return $this->hasMany('App\Models\Transaction', 'parent_id');
    }
}
