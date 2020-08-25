<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Polist extends Model
{
    protected $fillable = ['quantity', 'remarks', 'po_date', 'po_no', 'user_id', 'producthead_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
