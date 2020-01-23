<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BulstatRegister extends Model
{
    const PREDEFINED_LIST_TYPE = 1;

    const STATUS_ACTIVE = 571; //развиващ дейност
    const STATUS_LIQUIDATION = 574; //в ликвидация
    const STATUS_INACTIVE = 575; //неактивен
    const STATUS_REREGISTRED = 1; //пререгистриран в ТР
    const STATUS_ARCHIVED = 2; //архивиран

    const ACTIVE_STATUSES = [self::STATUS_ACTIVE];

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
}
