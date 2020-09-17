<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['weekly_holiday', 'yearly_holiday'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
