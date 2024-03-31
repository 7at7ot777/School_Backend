<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentGrades extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'subject_id',
        'midterm',
        'final',
        'attendance',
        'behavior',
        'total',
        'level'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(){
        return $this->belongsTo(Subject::class);
    }
}
