<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TradeRegister extends Model
{
    protected $table = 'tr_predefined_list';

    protected $guarded = [];

    public $timestamps = false;

    protected $primaryKey = 'eik';

    const STATUSES = ['E', 'C', 'L', 'N'];
    // N - Нова
    // Е - Пререгистрирана фирма по Булстат
    // L - Пререгистрирана фирма по Булстат затворена
    // C - Нова партида затворена
}
