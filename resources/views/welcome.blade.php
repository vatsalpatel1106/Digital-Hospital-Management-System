<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5 text-center">
    <h1 class="mb-4">Welcome to Hospital Management System</h1>

    <div class="d-grid gap-3 col-6 mx-auto">
        <a href="{{ route('register.form') }}" class="btn btn-success">Patient Register</a>
        <a href="{{ route('login.form') }}" class="btn btn-primary">Patient Login</a>
        <a href="{{ route('doctor.login.form') }}" class="btn btn-dark">Doctor Login</a>
    </div>

    <footer class="mt-5 text-muted">
        <small>&copy; {{ date('Y') }} Hospital Management System</small>
    </footer>
</div>
</body>
</html>