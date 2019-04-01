<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\RecordSignature;

class Message extends Model
{
    use RecordSignature;
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    
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
        //
    }
    
    public function recipient()
    {
        //
    }
}
