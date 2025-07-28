
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-header text-center">
            <h4>Book a New Appointment</h4>
        </div>
        <div class="card-body">
            
            {{-- Show validation errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Appointment Form --}}
            <form action="{{ route('appointment.create') }}" method="POST">
                @csrf

                {{-- Doctor selection --}}
                <div class="mb-3">
                    <label for="did" class="form-label">Select Doctor</label>
                    <select name="did" id="did" class="form-select" required>
                        <option value="">-- Choose Doctor --</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->did }}" {{ old('did') == $doctor->did ? 'selected' : '' }}>
                                Dr. {{ $doctor->register->name ?? 'Unknown' }} - {{ $doctor->specialist }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Appointment date --}}
                <div class="mb-3">
                    <label for="appointment_date" class="form-label">Appointment Date</label>
                    <input type="date" name="appointment_date" id="appointment_date" class="form-control" value="{{ old('appointment_date') }}" required>
                </div>

                {{-- Appointment time --}}
                <div class="mb-4">
                    <label for="appointment_time" class="form-label">Appointment Time</label>
                    <input type="time" name="appointment_time" id="appointment_time" class="form-control" value="{{ old('appointment_time') }}" required>
                </div>

                {{-- Submit button --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Book Appointment</button>
                    <a href="{{ route('patient.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            </form>

        </div>
    </div>
</div>

</body>
</html>
