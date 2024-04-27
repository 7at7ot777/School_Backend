<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory;//,SoftDeletes;
    protected $table = 'students';
    protected $fillable = [
        'user_id',
        'grade_level',
        'father_id',
        'mother_id',
        'class_id',
        'semester',
    ];

    public function classroom(){
        return $this->belongsTo(ClassRoom::class,'class_id','id');
    }

    public function studentExpenses(){
        return $this->hasMany(StudentExpenses::class);
    }
    public function studentAttendance(){
        return $this->hasMany(StudentAttendance::class);
    }

    //TODO: If you have any problem with student subjects plase return it as it was
    public function subjects()
    {
//        return $this->hasMany(Subject::class);
        return $this->belongsToMany(Subject::class,'student_subject','student_id','subject_id');

    }

    public function father()
    {
        return $this->belongsTo(User::class,'father_id','id');
    }

    public function mother()
    {
        return $this->belongsTo(User::class,'mother_id','id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

//    public function payments(){
//        return $this->hasMany(Payment::class,'user_id','user.id');
//    }
}
