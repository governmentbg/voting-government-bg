<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TradeRegister extends Model
{
    protected $table = 'tr_predefined_list';

    protected $guarded = [];

    public $timestamps = false;

    protected $primaryKey = 'eik';
}