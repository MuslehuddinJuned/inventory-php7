<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_employee extends Model
{
    protected $fillable = ['name', 'id_no', 'designation'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
