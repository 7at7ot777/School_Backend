<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['subject_name'];

    public function student(){
        return $this->belongsToMany(Student::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(Employee::class, 'subject_teacher', 'subject_id', 'teacher_id');
    }
    public function grades(){
        return $this->hasMany(Grade::class);
    }
}
