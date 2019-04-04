<?php

namespace App\Traits;


trait RecordSignature
{
    protected static function bootRecordSignature()
    {
        static::updating(function ($model) {
            if (empty($model->updated_by)) {
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
