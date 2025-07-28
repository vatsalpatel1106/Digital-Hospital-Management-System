<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointment';

    protected $fillable = [
        'pid',
        'did',
        'appointment_date',
        'appointment_time',
        'status',
        'medicine_file'
    ];

    
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'pid', 'pid');
    }

    
   public function doctor()
{
    return $this->belongsTo(Doctor::class, 'did', 'did');
}

}