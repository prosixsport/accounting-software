<!DOCTYPE html>
<html>
<head>
    <title>Register - Accounts System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="card shadow border-0" style="width:450px;">
        <div class="card-body p-4">
            <h3 class="fw-bold text-center mb-1">Register</h3>
            <p class="text-muted text-center mb-4">Create your account</p>

            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="/register">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <button class="btn btn-primary w-100">Register</button>
            </form>

            <div class="text-center mt-3">
                <a href="/login">Already have account?</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
