<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4 text-center">Doctor Dashboard</h2>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filter Section --}}
    <form method="GET" action="{{ route('doctor.dashboard') }}" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="patient_name" class="form-control" placeholder="Search by patient name" value="{{ request('patient_name') }}">
        </div>
        <div class="col-md-3">
            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
        </div>
        <div class="col-md-3">
            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    {{-- Appointments Table --}}
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Patient Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Actions</th>
                <th>Medicine File</th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $appointment)
            <tr>
                <td>{{ $appointment->patient->name }}</td>
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
                <td>
                    @if($appointment->status === 'pending')
                        <form method="GET" action="{{ route('appointment.accept', ['id' => $appointment->apid]) }}" class="d-inline">
    <button type="submit" class="btn btn-success btn-sm">Accept</button>
</form>

<form method="GET" action="{{ route('appointment.reject', ['id' => $appointment->apid]) }}" class="d-inline">
    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
</form>

                    @else
                        <span class="text-muted">No actions</span>
                    @endif
                </td>
                <td>
                    @if($appointment->status === 'accepted')
                        <form method="POST" action="{{ route('appointment.upload', $appointment->id) }}" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="medicine_file" accept=".pdf,.txt" class="form-control mb-2">
                            <button type="submit" class="btn btn-secondary btn-sm">Upload</button>
                        </form>
                    @elseif($appointment->medicine_file)
                        <a href="{{ Storage::url($appointment->medicine_file) }}" target="_blank" class="btn btn-outline-info btn-sm">View File</a>
                    @else
                        <span class="text-muted">Upload after acceptance</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">No appointments found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
</body>
</html>