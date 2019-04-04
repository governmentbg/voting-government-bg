<?php

namespace App\Traits;


trait RecordSignature
{
    protected static function bootRecordSignature()
    {
        static::updating(function ($model) {
            if (array_key_exists('updated_by', $model->attributes) && empty($model->updated_by)) {
                $model->updated_by = \Auth::check() ? \Auth::user()->id : null;
            }
        });

        static::creating(function ($model) {
            if (empty($model->created_by)) {
                $model->created_by = \Auth::check() ? \Auth::user()->id : null;
            }
        });
    }
}
