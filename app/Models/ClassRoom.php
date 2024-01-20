<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;
    protected $fillable = ['class_number', 'grade'];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

}
