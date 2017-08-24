<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    public function email()
    {
        return $this->belongsToMany('App\Email');
    }
}
