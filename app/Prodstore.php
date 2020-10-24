<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prodstore extends Model
{
    protected $fillable = ['name', 'remarks'];
    public function user(){
        return $this->belongsToMany(User::class);
    }
}
