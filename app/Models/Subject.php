<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function students(){
        return $this->belongsToMany(Student::class);
    }

    public function teachers()
    {
//        return $this->hasMany(Employee::class);
        return $this->belongsToMany(Employee::class, 'employee_subject', 'subject_id', 'employee_id');

    }

    public function grades(){
        return $this->hasMany(Grade::class);
    }

    public function lectures()
    {
        return $this->hasMany(Lecture::class);
    }

    public function classrooms(){
        return $this->belongsToMany(Classroom::class, 'subject_classroom', 'subject_id', 'classroom_id');
    }
}
