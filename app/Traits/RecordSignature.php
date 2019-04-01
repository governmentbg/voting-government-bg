<?php

namespace App\Traits;


trait RecordSignature
{
    protected static function bootRecordSignature()
    {
        static::updating(function ($model) {
            if(property_exists($model, 'updated_by')){
                $model->updated_by = \Auth::check() ? \Auth::user()->id : null;
            }
        });

        static::creating(function ($model) {
            if(property_exists($model, 'created_by')){
                $model->created_by = \Auth::check() ? \Auth::user()->id : null;
            }           
        });
    }
}