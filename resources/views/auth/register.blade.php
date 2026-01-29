<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Issue Board</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8fafc; }
        .register-card { max-width: 400px; margin: 60px auto; }
        .brand-icon { font-size: 2.5rem; color: #0d6efd; }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-card card shadow-sm">
            <div class="card-body">
                <div class="text-center mb-4">
                    <span class="brand-icon"><i class="fas fa-ticket-alt"></i></span>
                    <h2 class="mt-2 mb-0">Issue Board</h2>
                    <p class="text-muted">Create your account</p>
                </div>
    <form method="POST" action="{{ route('register') }}">
        @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                    </div>
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus me-1"></i> Register
                        </button>
        </div>
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-decoration-none">Already have an account? Login</a>
        </div>
                </form>
        </div>
        </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
