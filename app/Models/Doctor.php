<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;
     protected $table = 'doctor';
    protected $primaryKey = 'did';

    protected $fillable = ['reid', 'specialization', 'phone'];

    public function register()
    {
        return $this->belongsTo(Register::class, 'reid');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'did', 'did');
    }


    
}
