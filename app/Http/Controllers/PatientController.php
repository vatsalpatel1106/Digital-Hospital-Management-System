<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Register;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{

    public function showSignupForm()
    {
        return view('patient.register');
    }

    
    public function signup(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:register,email',
            'password' => 'required|string|confirmed',
            'contact_number' => 'required|string',
            'gender' => 'required|string',
            'dob' => 'required|date',
            'address' => 'required|string',
        ]);

        $user = Register::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'patient',
        ]);

        $patient = Patient::create([
            'reid' => $user->reid,
            'gender' => $validated['gender'],
            'dob' => $validated['dob'],
            'address' => $validated['address'],
            'phone' => $validated['contact_number'],
        ]);

        session(['reid' => $user->reid, 'pid' => $patient->pid, 'role' => 'patient']);

        return redirect()->route('patient.dashboard')->with('success', 'Signup successful!');
    }


    public function showLoginForm()
    {
        return view('patient.login');
    }

    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = Register::where('email', $credentials['email'])
                        ->where('role', 'patient')
                        ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['login_error' => 'Invalid credentials']);
        }

        $patient = Patient::where('reid', $user->reid)->first();

        session(['reid' => $user->reid, 'pid' => $patient->pid, 'role' => 'patient']);

        return redirect()->route('patient.dashboard');
    }

    
   public function dashboard()
{
    if (!session()->has('pid') || session('role') !== 'patient') {
        return redirect()->route('patient.login.form')->withErrors(['login_error' => 'Unauthorized']);
    }

    $appointments = Appointment::where('pid', session('pid'))
        ->with('doctor')
        ->orderBy('appointment_date')
        ->get();

    $patient = Patient::where('pid', session('pid'))->first();
    $patient->name = Register::where('reid', $patient->reid)->value('name');
    $patient->email = Register::where('reid', $patient->reid)->value('email');

    return view('patient.dashboard', compact('appointments', 'patient'));
}


    
    public function createAppointment(Request $request)
    {
        $validated = $request->validate([
            'did' => 'required|exists:register,reid',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
        ]);

        $conflict = Appointment::where('did', $validated['did'])
            ->where('appointment_date', $validated['appointment_date'])
            ->where('appointment_time', $validated['appointment_time'])
            ->exists();

        if ($conflict) {
            return back()->withErrors(['conflict' => 'This time slot is already booked with the doctor.']);
        }

        Appointment::create([
            'did' => $validated['did'],
            'pid' => session('pid'),
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Appointment created and is pending approval.');
    }

    
    public function updateAppointment(Request $request, $id)
    {
        $appointment = Appointment::where('pid', session('pid'))->findOrFail($id);

        $validated = $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
        ]);

        $conflict = Appointment::where('did', $appointment->did)
            ->where('appointment_date', $validated['appointment_date'])
            ->where('appointment_time', $validated['appointment_time'])
            ->where('apid', '!=', $appointment->apid)
            ->exists();

        if ($conflict) {
            return back()->withErrors(['conflict' => 'This time slot is already booked with the doctor.']);
        }

        $appointment->update([
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Appointment updated and is pending approval.');
    }

    public function cancelAppointment($id)
    {
        $appointment = Appointment::where('pid', session('pid'))->findOrFail($id);
        $appointment->delete();

        return back()->with('success', 'Appointment canceled.');
    }


    public function logout()
    {
        session()->flush();
        return redirect()->route('patient.login.form')->with('info', 'Logged out successfully.');
    }
}
