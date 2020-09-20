<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['event', 'yearly_holiday'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
