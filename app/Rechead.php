<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rechead extends Model
{
    protected $fillable = ['requisition_no', 'remarks'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
