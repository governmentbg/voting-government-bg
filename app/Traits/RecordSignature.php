<?php

namespace App\Traits;

use App\User;
use Illuminate\Support\Facades\Auth;

trait RecordSignature
{
    protected static function bootRecordSignature()
    {
        if (Auth::check()) {
            $userId = Auth::user()->id;
        } elseif (!empty($system = User::select('id')->where('username', config('auth.system.user'))->first())) {
            $userId = $system->id;
        } else {
            $userId = null;
        }

        static::updating(function ($model) use ($userId) {
            if (array_key_exists('updated_by', $model->attributes) && empty($model->updated_by)) {
                $model->updated_by = $userId;
            }
        });

        static::creating(function ($model) use ($userId) {
            if (empty($model->created_by)) {
                $model->created_by = $userId;
            }
        });
    }
}
