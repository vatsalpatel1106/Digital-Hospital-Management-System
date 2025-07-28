<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patient';
    protected $primaryKey = 'pid';

    protected $fillable = ['reid', 'gender', 'dob', 'address', 'phone'];

    
    public function register()
    {
        return $this->belongsTo(Register::class, 'reid', 'reid');
    }

    
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'pid', 'pid');
    }
}