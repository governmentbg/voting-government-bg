<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BulstatRegister extends Model
{
    const STATUS_ACTIVE = 'Y'; //Y => Актуален код
    const STATUS_DELETED = 'D'; //D => Изтрит код
    const STATUS_INACTIVE = 'N'; //N => Неактуален код
    
    protected $table = 'bul_predefined_list';

    protected $guarded = [];

    public $timestamps = false;

    protected $primaryKey = 'eik';
}
