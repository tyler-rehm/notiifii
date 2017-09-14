<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public function company()
    {
        return $this->belongsTo('App\Company');
    }
}
