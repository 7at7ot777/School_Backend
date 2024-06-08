<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'status',
        'name',
        'email',
        'password',
        'user_type',
        'address',
        'phone',
        'avatar_url',
        'isFirstTimeLogin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function parent (){return  $this->hasOne(Parents::class);}
    public function employee (){return $this->hasOne(Employee::class);}
    public function student (){return $this->hasOne(Student::class);}
    public function role(){return $this->hasOne(Role::class);}

    public function payments()
    {
        return $this->hasMany(Payment::class,'user_id','id');
    }

    public function edit(array $data)
    {
        // Validate and update the user attributes using the update method
        return $this->update($data);
    }
}
