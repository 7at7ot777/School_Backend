<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    //Get Subjects taught By this Employee(Teachers)
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function attendance()
    {
        return $this->hasMany(EmployeesAttendance::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

}
