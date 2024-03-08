<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'payment_id','payment_code', 'amount', 'pending', 'sucess'];

    public function student()
    {
        return $this->belongsTo(Student::class,'user_id','id');
    }
}
