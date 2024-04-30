<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;
    protected $table = 'class_rooms';
    protected $fillable = ['class_number', 'grade'];

    public function students()
    {
        return $this->hasMany(Student::class,'class_id','id');
    }

    public function subjects(){
        return $this->belongsToMany(Subject::class, 'subject_classroom', 'classroom_id', 'subject_id');
    }

}
