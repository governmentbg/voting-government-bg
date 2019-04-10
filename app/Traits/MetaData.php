<?php

namespace App\Traits;

trait MetaData
{
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['updated_by_name', 'created_by_name']));

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
            return $this->creator->first_name . ' ' . $this->creator->last_name;
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
}
