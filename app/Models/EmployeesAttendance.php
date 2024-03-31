<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeesAttendance extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'date', 'day', 'month', 'year', 'status'];


    public function employee(){
        return $this->belongsTo(Employee::class);
    }
}
