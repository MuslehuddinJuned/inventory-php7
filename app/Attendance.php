<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['ac_no', 'name', 'department', 'date', 'time', 'in_time_1', 'in_time_2', 'out_time_1', 'out_time_2', 'ot', 'ot_extra'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
