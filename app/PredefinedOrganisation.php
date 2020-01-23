<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PredefinedOrganisation extends Model
{
    const PREDEFINED_LIST_TYPE = 3;

    const STATUS_ENTERED = 'Вписано';
    const STATUS_DELETED = 'Заличено';
    const STATUS_AUTO_DELETED = 'Заличено служебно по чл. 48';
    const STATUS_NEW = 'Новосъздадено';
    const STATUS_REJECTED = 'Отказано вписване';
    const STATUS_TERMINATED = 'Прекратено';

    protected $table = 'predefined_list';

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
            self::PREDEFINED_LIST_TYPE => __('custom.predefined_list_type')
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_ENTERED      => self::STATUS_ENTERED,
            self::STATUS_DELETED      => self::STATUS_DELETED,
            self::STATUS_AUTO_DELETED => self::STATUS_AUTO_DELETED,
            self::STATUS_NEW          => self::STATUS_NEW,
            self::STATUS_REJECTED     => self::STATUS_REJECTED,
            self::STATUS_TERMINATED   => self::STATUS_TERMINATED,
        ];
    }

    public static function getActiveStatuses()
    {
        return [
            self::STATUS_ENTERED,
        ];
    }
}
