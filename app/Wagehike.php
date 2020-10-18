<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wagehike extends Model
{
    protected $fillable = ['effective_date', 'next_increment', 'amount', 'remarks', 'file_link', 'employee_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
