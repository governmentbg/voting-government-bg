<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionRequest extends Model
{
    const UPDATED_AT = null;
    
    const STATUS_NEW = 0;
    const STATUS_ERROR = 1; //D => Изтрит код
    
    protected $guarded = [];

    protected $primaryKey = 'uid';

    protected $keyType = 'string';
    
    public $timestamps = ['created_at'];

    const SUB_TYPE_TRADE = 1;
    const SUB_TYPE_BULSTAT = 2;

    public function scopeBulstat($query)
    {
        return $query->where('type', self::SUB_TYPE_BULSTAT);
    }
}
