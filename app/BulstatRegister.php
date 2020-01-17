<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BulstatRegister extends Model
{
    const PREDEFINED_LIST_TYPE = 1;

    const STATUS_ACTIVE = 'Y'; //Y => Актуален код
    const STATUS_DELETED = 'D'; //D => Изтрит код
    const STATUS_INACTIVE = 'N'; //N => Неактуален код

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
