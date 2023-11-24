<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;


    public function student(){
        return $this->belongsToMany(Student::class);
    }

//GetTeachers that teaches this subject
    public function taughtBy(){
        return $this->belongsTo(Employee::class);
    }

    public function grades(){
        return $this->hasMany(Grade::class);
    }
}
