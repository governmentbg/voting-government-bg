<?php

namespace App\Traits;

use App\User;
use Illuminate\Support\Facades\Auth;

trait RecordSignature
{
    protected static function bootRecordSignature()
    {
        static::updating(function ($model) {
            $userId = self::getUserId();

            if (array_key_exists('updated_by', $model->attributes)) {
                $model->updated_by = $userId;
            }
        });

        static::creating(function ($model) {
            $userId = self::getUserId();

            if (empty($model->created_by)) {
                $model->created_by = $userId;
            }
        });
    }

    private static function getUserId()
    {
        if (Auth::guard('backend')->check()) {
            $userId = Auth::guard('backend')->user()->id;
        } elseif (Auth::check()) {
            $userId = Auth::user()->id;
        } elseif (!empty($system = User::select('id')->where('username', config('auth.system.user'))->first())) {
            $userId = $system->id;
        } else {
            $userId = null;
        }

        return $userId;
    }
}
