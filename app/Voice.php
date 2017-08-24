<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voice extends Model
{
    protected $fillable = ['message_id', 'phone_number', 'direction', 'schedule_id'];
    public function schedule()
    {
        return $this->hasOne('App\Schedule', 'id', 'schedule_id');
    }

    public function message()
    {
        return $this->belongsTo('App\Message');
    }
}
