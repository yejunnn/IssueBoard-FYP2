<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1e3a8a">
    <title>Login - Issue Board</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #1e40af 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .login-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .login-left {
            flex: 1;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        @media (min-width: 992px) {
            .login-left {
                display: flex;
            }
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            animation: rotate 30s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .login-left-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 450px;
        }

        .login-left-content h1 {
            font-size: 2.75rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .login-left-content p {
            font-size: 1.15rem;
            opacity: 0.9;
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .feature-list {
            text-align: left;
            margin-top: 2rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.75rem 1rem;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .feature-item:hover {
            transform: translateX(10px);
            background: rgba(255,255,255,0.15);
        }

        .feature-item i {
            font-size: 1.25rem;
            margin-right: 1rem;
            width: 24px;
            text-align: center;
        }

        .login-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: white;
        }

        @media (min-width: 992px) {
            .login-right {
                border-radius: 30px 0 0 30px;
                box-shadow: -10px 0 40px rgba(0,0,0,0.1);
            }
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 0.5rem;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            box-shadow: 0 10px 30px rgba(30, 58, 138, 0.3);
            transition: transform 0.3s ease;
        }

        .login-logo:hover {
            transform: scale(1.05) rotate(5deg);
        }

        .login-logo i {
            font-size: 2rem;
            color: white;
        }

        .login-header h2 {
            color: #1e293b;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #64748b;
            font-size: 0.95rem;
        }

        .form-floating {
            margin-bottom: 1.25rem;
        }

        .form-floating .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            height: 58px;
            font-size: 1rem;
            padding: 1rem 1rem 0.5rem 2.75rem;
            transition: all 0.3s ease;
            background-color: #f8fafc;
        }

        .form-floating .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
            background-color: white;
        }

        .form-floating label {
            padding-left: 2.75rem;
            color: #64748b;
        }

        .form-floating .form-control:focus ~ label,
        .form-floating .form-control:not(:placeholder-shown) ~ label {
            color: #3b82f6;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 5;
            transition: color 0.3s ease;
        }

        .form-floating:focus-within .input-icon {
            color: #3b82f6;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            z-index: 5;
            padding: 0.5rem;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #3b82f6;
        }

        .form-check {
            margin-bottom: 1.5rem;
        }

        .form-check-input {
            width: 1.15em;
            height: 1.15em;
            margin-top: 0.2em;
            border: 2px solid #cbd5e1;
            border-radius: 4px;
        }

        .form-check-input:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .form-check-label {
            color: #475569;
            font-size: 0.9rem;
        }

        .btn-login {
            width: 100%;
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(30, 58, 138, 0.35);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login .spinner-border {
            width: 1.25rem;
            height: 1.25rem;
            border-width: 2px;
            display: none;
        }

        .btn-login.loading .spinner-border {
            display: inline-block;
        }

        .btn-login.loading .btn-text {
            visibility: hidden;
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
        }

        .login-footer a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .login-footer a:hover {
            color: #1e3a8a;
            text-decoration: underline;
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(34, 197, 94, 0.05) 100%);
            color: #166534;
            border-left: 4px solid #22c55e;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.05) 100%);
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .invalid-feedback {
            font-size: 0.85rem;
            margin-top: 0.5rem;
            color: #dc2626;
        }

        .form-control.is-invalid {
            border-color: #dc2626;
            background-image: none;
        }

        /* Mobile adjustments */
        @media (max-width: 576px) {
            .login-right {
                padding: 1.5rem;
            }

            .login-card {
                padding: 0;
            }

            .login-header h2 {
                font-size: 1.5rem;
            }

            .login-logo {
                width: 60px;
                height: 60px;
            }

            .login-logo i {
                font-size: 1.75rem;
            }
        }

        /* Floating shapes decoration */
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .shape-1 {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 10%;
            animation: float 6s ease-in-out infinite;
        }

        .shape-2 {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 15%;
            animation: float 8s ease-in-out infinite reverse;
        }

        .shape-3 {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation: float 7s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(10deg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Branding -->
        <div class="login-left">
            <div class="floating-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
            </div>
            <div class="login-left-content">
                <h1><i class="fas fa-ticket-alt me-3"></i>Issue Board</h1>
                <p>Your centralized platform for tracking, managing, and resolving campus issues efficiently.</p>
                <div class="feature-list">
                    <div class="feature-item">
                        <i class="fas fa-bolt"></i>
                        <span>Real-time ticket updates</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-building"></i>
                        <span>Department-based organization</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-bell"></i>
                        <span>Instant notifications</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Track progress & analytics</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-right">
            <div class="login-card">
                <div class="login-header">
                    <div class="login-logo">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <h2>Welcome Back</h2>
                    <p>Sign in to your account to continue</p>
                </div>

                @if(session('status'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        @foreach($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    <div class="form-floating position-relative">
                        <i class="fas fa-envelope input-icon"></i>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email address" required autofocus>
                        <label for="email">Email address</label>
                    </div>

                    <div class="form-floating position-relative">
                        <i class="fas fa-lock input-icon"></i>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-decoration-none" style="font-size: 0.9rem;">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-login" id="loginBtn">
                        <span class="btn-text"><i class="fas fa-sign-in-alt me-2"></i>Sign In</span>
                        <span class="spinner-border spinner-border-sm position-absolute" role="status"></span>
                    </button>
                </form>

                <div class="login-footer">
                    <p class="text-muted mb-0">Need help? <a href="{{ route('welcome') }}">Go to Home</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Form submission loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.disabled = true;
        });
    </script>
</body>
</html>
