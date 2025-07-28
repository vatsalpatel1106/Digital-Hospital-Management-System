<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    
public function create()
{
    if (session('role') !== 'patient') {
        return redirect()->route('patient.login.form')->withErrors(['login_error' => 'Unauthorized']);
    }

    $doctors = Doctor::all(); 
    return view('appointment', compact('doctors'));
}


public function store(Request $request)
{
    if (session('role') !== 'patient') {
        return redirect()->route('patient.login.form')->withErrors(['login_error' => 'Unauthorized']);
    }

    $patient = Patient::where('reid', session('reid'))->first();
    if (!$patient) return back()->withErrors(['auth' => 'Patient not found.']);

    $validated = $request->validate([
        'did' => 'required|exists:doctor,did',
        'appointment_date' => 'required|date|after_or_equal:today',
        'appointment_time' => 'required',
    ]);

    $conflict = Appointment::where('did', $validated['did'])
        ->where('appointment_date', $validated['appointment_date'])
        ->where('appointment_time', $validated['appointment_time'])
        ->exists();

    if ($conflict) {
        return back()->withErrors(['conflict' => 'This time slot is already booked.']);
    }

    Appointment::create([
        'did' => $validated['did'],
        'pid' => $patient->pid,
        'appointment_date' => $validated['appointment_date'],
        'appointment_time' => $validated['appointment_time'],
        'status' => 'pending',
    ]);

    return redirect()->route('patient.dashboard')->with('success', 'Appointment requested successfully.');
}

    public function update(Request $request, $id)
    {
        if (session('role') !== 'patient') {
            return redirect()->route('patient.login.form')->withErrors(['login_error' => 'Unauthorized']);
        }

        $patient = Patient::where('reid', session('reid'))->first();
        $appointment = Appointment::where('pid', $patient->pid)->findOrFail($id);

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
            return back()->withErrors(['conflict' => 'This time slot is already booked.']);
        }

        $appointment->update([
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Appointment updated successfully.');
    }

    public function cancel($id)
    {
        if (session('role') !== 'patient') {
            return redirect()->route('patient.login.form')->withErrors(['login_error' => 'Unauthorized']);
        }

        $patient = Patient::where('reid', session('reid'))->first();
        $appointment = Appointment::where('pid', $patient->pid)->findOrFail($id);
        $appointment->delete();

        return back()->with('success', 'Appointment cancelled.');
    }


    public function accept($id)
    {
        if (session('role') !== 'doctor') {
            return redirect()->route('doctor.login.form')->withErrors(['login_error' => 'Unauthorized']);
        }

        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'accepted';
        $appointment->save();

        return back()->with('success', 'Appointment accepted.');
    }

    public function reject($id)
    {
        if (session('role') !== 'doctor') {
            return redirect()->route('doctor.login.form')->withErrors(['login_error' => 'Unauthorized']);
        }

        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'rejected';
        $appointment->save();

        return back()->with('success', 'Appointment rejected.');
    }


    public function uploadMedicine(Request $request, $id)
    {
        if (session('role') !== 'doctor') {
            return redirect()->route('doctor.login.form')->withErrors(['login_error' => 'Unauthorized']);
        }

        $validator = Validator::make($request->all(), [
            'medicine_file' => 'required|file|mimes:pdf,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $appointment = Appointment::findOrFail($id);
        $path = $request->file('medicine_file')->store('medicine_lists', 'public');

        $appointment->medicine_file = $path;
        $appointment->save();

        return back()->with('success', 'Medicine file uploaded.');
    }


    public function patientAppointments()
    {
        $patient = Patient::where('reid', session('reid'))->first();
       $appointments = Appointment::where('pid', $patient->pid)
    ->with('doctor') 
    ->orderBy('appointment_date')
    ->get();


        return view('patient.appointments.index', compact('appointments'));
    }

    
    public function doctorAppointments(Request $request)
    {
        $doctor = Doctor::where('reid', session('reid'))->first();
        $query = Appointment::where('did', $doctor->did);

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('appointment_date', [$request->date_from, $request->date_to]);
        }

        if ($request->filled('patient_name')) {
            $query->whereHas('patient.register', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->patient_name . '%');
            });
        }

        $appointments = $query->orderBy('appointment_date')->get();

        return view('doctor.appointments.index', compact('appointments'));
    }
}
