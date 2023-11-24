<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parents extends Model
{
    use HasFactory;

    public function student()
    {
        return $this->belongsToMany(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
