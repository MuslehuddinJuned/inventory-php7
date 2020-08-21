<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wipissue extends Model
{
    protected $fillable = ['issue_qty', 'remarks', 'user_id', 'product_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
