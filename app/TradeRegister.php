<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TradeRegister extends Model
{
    protected $table = 'tr_predefined_list';

    protected $guarded = [];

    public $timestamps = false;

    protected $primaryKey = 'eik';

    /**
    * The "type" of the primary key eik.
    *
    * @var string
    */
    protected $keyType = 'string';

    const STATUSES = ['E', 'C', 'L', 'N'];
    // N - Нова
    // Е - Пререгистрирана фирма по Булстат
    // L - Пререгистрирана фирма по Булстат затворена
    // C - Нова партида затворена
}
