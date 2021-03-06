<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\RecordSignature;
use Awobaz\Compoships\Compoships;
use App\Http\Controllers\Traits\CanResetPassword;
use Illuminate\Support\Str;
use App\Traits\MetaData;

class User extends Authenticatable
{
    use Notifiable;
    use Compoships;
    use RecordSignature;
    use CanResetPassword;
    use MetaData;

    const ACTIVE_FALSE = 0;
    const ACTIVE_TRUE = 1;

    const DEFAULT_ORDER_FIELD = 'id';
    const DEFAULT_ORDER_TYPE = 'ASC';

    const ALLOWED_ORDER_FIELDS = [
        'username',
        'name',
        'first_name',
        'last_name',
        'active',
        'email',
        'created_at',
        'updated_at',
    ];
    const ALLOWED_ORDER_TYPES = ['ASC', 'DESC'];

    const EDITABLE_FIELDS = ['first_name', 'last_name', 'active', 'email'];

    protected $guarded = ['id'];

    protected $rememberTokenName = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'updater', 'creator', 'org_id', 'voting_tour_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    public function organisation()
    {
        return $this->belongsTo('App\Organisation', 'org_id');
    }

    public function setUpdatedAtAttribute($value)
    {
        // to disable updated_at
    }

    public function scopeSort($query, $field, $order)
    {
        if (isset($field)) {
            if ($field == 'name') {
                return $query->orderBy('first_name', $order)->orderBy('last_name', $order);
            }
            return $query->orderBy($field, $order);
        }

        return $query;
    }

    public function isAdmin()
    {
        return !isset($this->org_id);
    }

    public function isSuperAdmin()
    {
        return !isset($this->org_id) && $this->username == config('auth.system.user');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name .' '. $this->last_name;
    }

    /**
     * Get the notification routing information for the given driver.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function routeNotificationFor($driver)
    {
        if (method_exists($this, $method = 'routeNotificationFor'. Str::studly($driver))) {
            return $this->{$method}();
        }

        switch ($driver) {
            case 'database':
                return $this->notifications();
            case 'mail':
                return $this->getEmailForPasswordReset();
            case 'nexmo':
                return $this->phone_number;
        }
    }
}
