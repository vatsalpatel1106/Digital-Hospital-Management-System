<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-3">Welcome, {{ $patient->name }}</h2>

    <div class="mb-4">
        <p><strong>Email:</strong> {{ $patient->email }}</p>
        <p><strong>Contact:</strong> {{ $patient->phone }}</p>
        <p><strong>Gender:</strong> {{ ucfirst($patient->gender) }}</p>
        <p><strong>DOB:</strong> {{ \Carbon\Carbon::parse($patient->dob)->format('d M Y') }}</p>
        <p><strong>Address:</strong> {{ $patient->address }}</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
<h3 class="mb-3 d-flex justify-content-between align-items-center">
    
    <a href="{{ route('appointment.create') }}" class="btn btn-primary btn-sm">
        + Add Appointment
    </a>
</h3>

    <h3 class="mb-3">Your Appointments</h3>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Doctor</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $appointment)
                <tr>
                   <td>{{ $appointment->doctor->name  }}</td>
                    <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') }}</td>
                    <td>{{ $appointment->appointment_time }}</td>
                    <td>
                        @php
                            $statusClass = match($appointment->status) {
                                'accepted' => 'success',
                                'rejected' => 'danger',
                                'pending' => 'warning',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge bg-{{ $statusClass }}">{{ ucfirst($appointment->status) }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">No appointments found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <a href="{{ route('logout') }}" class="btn btn-danger mt-3">Logout</a>
</div>
</body>
</html>