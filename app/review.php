<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class review extends Model
{
    const CREATE_AT = 'date';
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
