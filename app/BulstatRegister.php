<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BulstatRegister extends Model
{
    protected $table = 'bul_predefined_list';

    protected $guarded = [];

    public $timestamps = false;

    protected $primaryKey = 'eik';
}
