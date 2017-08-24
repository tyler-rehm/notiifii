<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = ["phone_number","email_address","first_name","middle_name","last_name", "company_id"];

    public function messages()
    {
        return $this->hasMany('App\Message');
    }
}
