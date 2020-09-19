<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usedleave extends Model
{
    protected $fillable = ['leave_type', 'reason', 'replacing_person', 'leave_start', 'leave_end', 'day_count', 'employee_id'];
    public function user(){
        return $this->belongsToMany(User::class);
    }

}
