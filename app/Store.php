<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = ['name', 'remarks', 'account_code'];
    public function user(){
        return $this->belongsToMany(User::class);
    }
}
