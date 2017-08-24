<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Sms extends Model
{
    public function schedule()
    {
        return $this->hasOne('App\Schedule', 'id', 'schedule_id');
    }

    public function message()
    {
        return $this->belongsTo('App\Message');
    }
}
