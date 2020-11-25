<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['ac_name', 'ac_no', 'service_name', 'mnth_of_payment', 'amount'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
