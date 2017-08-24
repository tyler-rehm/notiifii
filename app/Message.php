<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    public function type()
    {
        return $this->belongsTo('App\Type');
    }

    public function status()
    {
        return $this->belongsTo('App\Status');
    }

    public function contact()
    {
        return $this->belongsTo('App\Contact');
    }
}
