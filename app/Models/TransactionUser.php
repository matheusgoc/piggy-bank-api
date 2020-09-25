<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperTransactionUser
 */
class TransactionUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaction_user';
    protected $primaryKey = ['user_id', 'transaction_id'];
    public $incrementing = false;
    protected $fillable = [
        'type',
        'currency',
        'currency_exchange'
    ];
    protected $casts = [
        'is_owner' => 'boolean',
    ];

    public function transaction() {

        return $this->belongsTo('App\Models\Transaction');
    }

    public function category() {

        return $this->belongsTo('App\Models\Category');
    }

    public function user() {

        return $this->belongsTo('App\Models\User');
    }

    /**
     * Set the keys for a save update query.
     *
     * @param Builder $query
     * @return Builder
     */
    protected function setKeysForSaveQuery(Builder $query)
    {
        $keys = $this->getKeyName();
        if(!is_array($keys)){
            return parent::setKeysForSaveQuery($query);
        }

        foreach($keys as $keyName){
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     *
     * @param mixed $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if(is_null($keyName)){
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }
}
