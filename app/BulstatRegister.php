<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BulstatRegister extends Model
{
    const PREDEFINED_LIST_TYPE = 1;

    const STATUS_ACTIVE = 571;      // 571 - Развиващ дейност
    const STATUS_LIQUIDATION = 574; // 574 - В ликвидация
    const STATUS_INACTIVE = 575;    // 575 - Неактивен
    const STATUS_REREGISTRED = 1;   // 1 - Пререгистриран в ТР
    const STATUS_ARCHIVED = 2;      // 2 - Архивиран

    protected $table = 'bul_predefined_list';

    protected $guarded = [];

    public $timestamps = false;

    protected $primaryKey = 'eik';

    protected $keyType = 'string';

    public static function getType()
    {
        return [
            self::PREDEFINED_LIST_TYPE => __('custom.predefined_list_type_bul'),
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE       => __('custom.bul_reg_status_active'),
            self::STATUS_LIQUIDATION  => __('custom.bul_reg_status_liquidation'),
            self::STATUS_INACTIVE     => __('custom.bul_reg_status_inactive'),
            self::STATUS_REREGISTRED  => __('custom.bul_reg_status_reregistered'),
            self::STATUS_ARCHIVED     => __('custom.bul_reg_status_archived'),
        ];
    }

    public static function getActiveStatuses()
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_REREGISTRED,
        ];
    }
}
