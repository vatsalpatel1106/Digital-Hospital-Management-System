<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Register;
use App\Models\Appointment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    
    public function loginForm()
    {
        return view('doctor.login');
    }

    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $doctor = Register::where('email', $credentials['email'])
                          ->where('role', 'doctor')
                          ->first();

        if (!$doctor || !Hash::check($credentials['password'], $doctor->password)) {
            return back()->withErrors(['login_error' => 'Invalid credentials']);
        }

        session(['reid' => $doctor->reid, 'role' => 'doctor']);

        return redirect()->route('doctor.dashboard')->with('success', 'Welcome Doctor!');
    }


    public function dashboard(Request $request)
    {
        if (session('role') !== 'doctor') {
            return redirect()->route('doctor.login.form')->withErrors(['login_error' => 'Unauthorized']);
        }

        $doctorId = session('reid');

        $query = Appointment::where('did', $doctorId)->with('patient');

    
        if ($request->filled('patient_name')) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->patient_name . '%');
            });
        }

    
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('appointment_date', [$request->from_date, $request->to_date]);
        }

        $appointments = $query->orderBy('appointment_date')->get();

        return view('doctor.doctordashboard', compact('appointments'));
    }

    
    public function acceptAppointment($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'accepted';
        $appointment->save();

        return back()->with('success', 'Appointment accepted.');
    }

    public function rejectAppointment($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'rejected';
        $appointment->save();

        return back()->with('success', 'Appointment rejected.');
    }


    public function uploadMedicine(Request $request, $id)
    {
        $request->validate([
            'medicine_file' => 'required|file|mimes:pdf,txt|max:2048',
        ]);

        $appointment = Appointment::findOrFail($id);

        
        $path = $request->file('medicine_file')->store('medicine_lists', 'public');

        $appointment->medicine_file = $path;
        $appointment->save();

        return back()->with('success', 'Medicine list uploaded.');
    }


    public function logout()
    {
        session()->flush();
        return redirect()->route('doctor.login.form')->with('info', 'Logged out successfully.');
    }
}