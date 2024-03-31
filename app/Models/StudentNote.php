<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentNote extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['subject_id', 'teacher_id', 'father_id', 'mother_id', 'student_id', 'note'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Employee::class,'teacher_id');
    }

    public function father()
    {
        return $this->belongsTo(Employee::class, 'father_id');
    }

    public function mother()
    {
        return $this->belongsTo(Employee::class, 'mother_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

}
