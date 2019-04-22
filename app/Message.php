<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\RecordSignature;

class Message extends Model
{
    use RecordSignature;
    
    const STATUS_ALL = 0;
    const STATUS_NOT_READ = 1;
    const STATUS_READ = 2;
   
    protected $perPage = 15;
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    protected $appends = ['sender_org_name', 'sender_user_name'];
    
    protected $hidden = ['senderOrganisation', 'senderUser'];

    public function file()
    {
        return $this->hasOne('App\File', 'message_id');
    }
    
    public function subMessages()
    {
        return $this->hasMany('App\Message', 'parent_id');
    }
    
    public function orderedMessages()
    {
        return $this->hasMany('App\Message', 'parent_id')->latest(); //->oldest();
    }
    
    public function isParent()
    {
        return !isset($this->parent_id);
    }
    
    public function parent()
    {
        return $this->belongsTo('App\Message', 'parent_id', 'id');
    }
    
    public function sender()
    {
        if ($this->sender_org_id) {
            return $this->belongsTo('App\Organisation', 'sender_org_id');
        }
        
        return $this->belongsTo('App\User', 'sender_user_id');
    }
    
    public function senderOrganisation()
    {
        return $this->belongsTo('App\Organisation', 'sender_org_id');
    }
    
    public function senderUser()
    {
        return $this->belongsTo('App\User', 'sender_user_id');
    }
    
    public function recipient()
    {
    }
    
    public function scopeSort($query, $field, $order)
    {
        if (isset($field)) {
            return $query->orderBy($field, $order);
        }
        
        return $query;
    }
    
    public function scopeSearch($query, $filters, $field = null, $order = 'ASC')
    {
        if (array_key_exists('parent_id', $filters)) {
            $query->where('parent_id',  $filters['parent_id']);
        }
        
        if ($date_from = ($filters['date_from'] ?? null)) {
            $date_from = date_format(date_create($date_from), 'Y-m-d');
            $query->where('created_at', '>=', $date_from);
        }
        if ($date_to = $filters['date_to'] ?? null) {
            $date_to = date_format(date_create($date_to), 'Y-m-d');
            $query->where('created_at', '<=', $date_to);
        }
        if ($subject = $filters['subject'] ?? null) {
            $query->where('subject', 'like', '%' . $subject . '%');
        }
        
        if ($name = $filters['org_name'] ?? null) {
            $query->whereHas('senderOganisation', function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            });
        }
        
        if ($status = $filters['status'] ?? null) {
            if ($status == self::STATUS_NOT_READ) {
                $query->where('read', null);
            } elseif ($status == self::STATUS_READ) {
                $query->where('read', '!=', null);
            }
        }
        
        return $query->sort($field, $order);
    }
    
    public static function getStatuses()
    {
        return [
            self::STATUS_ALL      => __('custom.status_all'),
            self::STATUS_NOT_READ => __('custom.status_not_read'),
            self::STATUS_READ     => __('custom.status_read'),
        ];
    }
    
    public function files()
    {
        return $this->hasMany('App\File', 'message_id');
    }
    
    public function isRead()
    {
        return $this->read != null;
    }
    
    public function getSenderOrgNameAttribute()
    {
        return isset($this->senderOrganisation) ? $this->senderOrganisation->name : '';
    }
    
    public function getSenderUserNameAttribute()
    {
        return isset($this->senderUser) ? $this->senderUser->fullName : '';
    }
}
