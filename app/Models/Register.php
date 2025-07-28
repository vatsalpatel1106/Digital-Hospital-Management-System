<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Register extends Model
{
    use HasFactory;
    protected $table = 'register';
    protected $primaryKey = 'reid';

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password'];

    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'reid');
    }

    public function patient()
    {
        return $this->hasOne(Patient::class, 'reid');
    }

}
