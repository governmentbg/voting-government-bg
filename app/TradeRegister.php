<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TradeRegister extends Model
{
    const PREDEFINED_LIST_TYPE = 2;

    const STATUSES = ['E', 'C', 'L', 'N'];
    // N - Нова
    // Е - Пререгистрирана фирма по Булстат
    // L - Пререгистрирана фирма по Булстат затворена
    // C - Нова партида затворена

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

    // N - Нова
    // Е - Пререгистрирана фирма по Булстат
    // L - Пререгистрирана фирма по Булстат затворена
    // C - Нова партида затворена

    public static function getType()
    {
        return [
            self::PREDEFINED_LIST_TYPE => __('custom.predefined_list_type_tr')
        ];
    }
}
