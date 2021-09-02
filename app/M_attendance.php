<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_attendance extends Model
{
    protected $fillable = ['in_time', 'out_time', 'others', 'employee_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
