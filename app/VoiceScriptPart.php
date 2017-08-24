<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoiceScriptPart extends Model
{
    public function script()
    {
        return $this->belongsTo('App\VoiceScript');
    }
}
