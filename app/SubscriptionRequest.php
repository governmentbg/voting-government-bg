<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionRequest extends Model
{
    const UPDATED_AT = null;
    
    const STATUS_NEW = 0;
    const STATUS_PROCESSED =1;
    const STATUS_ERROR = 9;
    
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

    public function scopeFilter($query, $filter)
    {
        if(isset($filter['status']) && in_array($filter['status'], [self::STATUS_NEW, self::STATUS_PROCESSED, self::STATUS_ERROR])){
            return $query->where('status', $filter['status']);
        }

        if(isset($filter['uid'])){
            return $query->where('uid', '>', $filter['uid']);
        }
    }
}
