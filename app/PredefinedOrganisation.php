<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PredefinedOrganisation extends Model
{
    const PREDEFINED_LIST_TYPE = 3;

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
}
