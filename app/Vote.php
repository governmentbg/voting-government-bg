<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;

class Vote extends Model
{
    use Compoships;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public $timestamps = false;

    const MAX_VOTES = 14;
    const MIN_VOTES = 1;

    const GENESIS_RECORD = 1;
}
