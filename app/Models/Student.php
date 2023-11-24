<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    public function classroom(){
        return $this->belongsTo(ClassRoom::class);
    }

    public function studentExpenses(){
        return $this->hasMany(StudentExpenses::class);
    }
    public function studentAttendance(){
        return $this->hasMany(StudentAttendance::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function parents()
    {
        return $this->hasMany(Parents::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
