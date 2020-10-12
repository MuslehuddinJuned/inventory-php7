<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['ac_no', 'name', 'department', 'date', 'time'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
