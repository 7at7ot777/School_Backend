<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherVideo extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', // اسم الفيديو
        'description', // وصف الفيديو
        'file_path', // مسار الملف
    ];
}
