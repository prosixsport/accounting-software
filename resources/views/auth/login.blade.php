<!DOCTYPE html>
<html>
<head>
    <title>Login - Accounts System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="card shadow border-0" style="width:420px;">
        <div class="card-body p-4">
            <h3 class="fw-bold text-center mb-1">Login</h3>
            <p class="text-muted text-center mb-4">Accounts Management System</p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">Email or password incorrect.</div>
            @endif

            <form method="POST" action="/login">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button class="btn btn-primary w-100">Login</button>
            </form>

            <div class="text-center mt-3">
                <a href="/register">Create account</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
