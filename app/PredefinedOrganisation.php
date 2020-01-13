<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PredefinedOrganisation extends Model
{
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

    public function scopeGetData($query, $eik)
    {
        $query->where('eik', $eik)->select('name', 'city', 'address', 'phone', 'email');

        return $query;
    }

    public function getFullAddressAttribute()
    {
        if (trim($this->city) != '') {
            return $this->city . (trim($this->address) != '' ? ', '. $this->address : '');
        }

        return $this->address;
    }
}
