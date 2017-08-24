<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoiceScript extends Model
{
    public function parts()
    {
        return $this->hasMany('App\VoiceScriptPart');
    }
}
