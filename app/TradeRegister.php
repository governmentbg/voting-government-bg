<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TradeRegister extends Model
{
    const PREDEFINED_LIST_TYPE = 2;

    const STATUS_NEW = 'N';                 // N - Нова
    const STATUS_REREGISTERED = 'E';        // E - Пререгистрирана фирма по Булстат
    const STATUS_REREGISTERED_CLOSED = 'L'; // L - Пререгистрирана фирма по Булстат затворена
    const STATUS_NEW_BATCH_CLOSED = 'C';    // C - Нова партида затворена

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

    public static function getType()
    {
        return [
            self::PREDEFINED_LIST_TYPE => __('custom.predefined_list_type_tr')
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_NEW                 => __('custom.tr_reg_status_new'),
            self::STATUS_REREGISTERED        => __('custom.tr_reg_status_reregistered'),
            self::STATUS_REREGISTERED_CLOSED => __('custom.tr_reg_status_rereg_closed'),
            self::STATUS_NEW_BATCH_CLOSED    => __('custom.tr_reg_status_new_batch_closed'),
        ];
    }

    public static function getActiveStatuses()
    {
        return [
            self::STATUS_NEW,
            self::STATUS_REREGISTERED,
        ];
    }
}
