<?php

namespace App\Traits;

trait MetaData
{
    protected function getArrayableAppends()
    {
        $userFields = [];
        if (array_key_exists('updated_by', $this->attributes)) {
            $userFields = array_merge($userFields, ['updated_by_name', 'updated_by_username']);
        }

        if (array_key_exists('created_by', $this->attributes)) {
            $userFields = array_merge($userFields, ['created_by_name', 'created_by_username']);
        }

        if (array_key_exists('user_id', $this->attributes)) {
            $userFields = array_merge($userFields, ['user_id_username']);
        }

        $this->appends = array_unique(array_merge($this->appends, $userFields));

        return parent::getArrayableAppends();
    }

    public function getUpdatedByNameAttribute()
    {
        if ($this->updater) {
            if ($this->org_id == null) {
                return $this->updater->first_name . ' ' . $this->updater->last_name;
            } elseif ($this->organisation) {
                return $this->organisation->name;
            }
        }

        return '';
    }

    public function getUpdatedByUsernameAttribute()
    {
        if ($this->updater) {
            return $this->updater->username;
        }

        return '';
    }

    public function getCreatedByNameAttribute()
    {
        if ($this->creator) {
            if ($this->org_id == null) {
                return $this->creator->first_name . ' ' . $this->creator->last_name;
            } elseif ($this->organisation) {
                return $this->organisation->name;
            }
        }

        return '';
    }

    public function getCreatedByUsernameAttribute()
    {
        if ($this->creator) {
            return $this->creator->username;
        }

        return '';
    }

    public function getUserIdUsernameAttribute()
    {
        if ($this->actor) {
            return $this->actor->username;
        }

        return '';
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function actor()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
