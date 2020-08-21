<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rechead extends Model
{
    // po_no
    protected $fillable = ['requisition_no', 'remarks', 'accept'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
