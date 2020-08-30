<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // protected $fillable = ['requisition_no', 'remarks', 'accept'];
    public function user(){
        return $this->belongsToMany(User::class);
    }
}
