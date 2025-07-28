<?php

namespace App\Http\Controllers;

use App\Models\Register;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    
    public function openregister()
    {
        return view('patient.register');
    }

    
    public function store(Request $request)
    {
        if ($request->role === 'doctor') {
            return redirect()->route('doctor.login.form')->with('info', 'Doctors must login directly.');
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:register,email',
            'password' => 'required|string|confirmed',
            'contact_number' => 'required|string',
            'gender' => 'required|string',
            'dob' => 'required|date',
            'address' => 'required|string',
            'role' => 'required|in:patient,doctor',
        ]);

        
        $validated['password'] = Hash::make($validated['password']);

        
        $user = Register::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'patient',
        ]);

        
        $patient = Patient::create([
            'reid' => $user->reid,
            'gender' => $validated['gender'],
            'dob' => $validated['dob'],
            'address' => $validated['address'],
            'phone' => $validated['contact_number'],
        ]);

        
        session(['reid' => $user->reid, 'role' => 'patient']);

        return redirect()->route('patient.dashboard')->with('success', 'Registration successful!');
    }


    public function openlogin()
    {
        return view('patient.login');
    }

    
    public function storelogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = Register::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['login_error' => 'Invalid email or password']);
        }

        session(['reid' => $user->reid, 'role' => $user->role]);

        return $user->role === 'doctor'
            ? redirect()->route('doctor.dashboard')->with('success', 'Welcome Doctor!')
            : redirect()->route('patient.dashboard')->with('success', 'Welcome Patient!');
    }


    public function dashboard()
    {
        if (!session()->has('reid') || session('role') !== 'patient') {
            return redirect()->route('login.form')->withErrors(['login_error' => 'Unauthorized access']);
        }

        $register = Register::find(session('reid'));
        $patient = $register->patient;

        $appointments = Appointment::where('pid', $patient->pid)
            ->with('doctor')
            ->orderBy('appointment_date')
            ->get();

        return view('patient.patientdashboard', compact('patient', 'appointments'));
    }
    public function logout()
{
    session()->flush(); 
    return redirect()->route('home')->with('success', 'Logged out successfully.');
}

}