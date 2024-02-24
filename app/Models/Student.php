<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
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

    public function subjects()
    {
        return $this->hasMany(Subject::class);
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
}
