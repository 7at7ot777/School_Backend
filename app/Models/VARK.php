<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VARK extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'vark_results';
    protected $fillable = ['v','a','r','k','user_id','result'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
