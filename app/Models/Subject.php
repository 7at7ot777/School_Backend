<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function student(){
        return $this->hasMany(Employee::class);
    }

    public function teachers()
    {
//        return $this->hasMany(Employee::class);
        return $this->belongsToMany(Employee::class, 'employee_subject', 'subject_id', 'employee_id');

    }
    public function grades(){
        return $this->hasMany(Grade::class);
    }
}
