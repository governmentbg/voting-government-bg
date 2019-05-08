<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PredefinedOrganisation extends Model
{
    protected $table = 'predefined_list';

    protected $guarded = [];

    public $timestamps = false;

    protected $primaryKey = 'eik';

    public function scopeGetData($query, $eik)
    {
        $query->where('eik', $eik)->select('name', 'city', 'address', 'phone', 'email');

        return $query->first();
    }

    public function getFullAddressAttribute()
    {
        if (trim($this->city) != '') {
            return $this->city . (trim($this->address) != '' ? ', '. $this->address : '');
        }

        return $this->address;
    }
}
