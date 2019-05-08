<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PredefinedOrganisation extends Model
{
    protected $table = 'predefined_list';
    
    protected $guarded = [];
    
    public $timestamps = false;
    
    protected $primaryKey = 'eik';
}
