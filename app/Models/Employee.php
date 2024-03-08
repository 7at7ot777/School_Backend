<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['user_id','department_id','role','basic_salary','subject_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }


    public function subject()
    {
        //return $this->belongsTo(Subject::class);
        return $this->belongsToMany(Subject::class);

    }

    public function attendance()
    {
        return $this->hasMany(EmployeesAttendance::class);
    }

    public static function getRoles()
    {
        return ['admin','superAdmin','employee'];
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

}
