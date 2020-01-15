<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionRequest extends Model
{
    const UPDATED_AT = null;

    protected $guarded = [];

    protected $primaryKey = 'uid';

    public $timestamps = ['created_at'];

    const SUB_TYPE_TRADE = 1;
    const SUB_TYPE_BULSTAT = 2;
}
