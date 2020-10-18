<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Increment extends Model
{
    protected $fillable = ['effective_date', 'next_increment', 'amount', 'remarks'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
