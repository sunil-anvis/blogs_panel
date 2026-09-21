<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | BlogServices</title>
    <link rel="icon" type="image/png" href="/anvis-favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: #f4f7fe;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            -webkit-font-smoothing: antialiased;
        }

        .login-wrapper {
            display: flex;
            width: 100%;
            max-width: 900px;
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(79, 70, 229, 0.12);
        }

        /* Left Panel */
        .login-brand {
            flex: 1;
            background: linear-gradient(150deg, #0f172a 0%, #1e293b 60%, #312e81 100%);
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 500px;
        }
        .brand-logo {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .brand-logo img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 8px;
            background: rgba(255,255,255,0.1);
            padding: 4px;
        }
        .brand-logo span {
            font-size: 20px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.5px;
        }
        .brand-tagline {
            color: rgba(255,255,255,0.85);
        }
        .brand-tagline h2 {
            font-size: 28px;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 14px;
            letter-spacing: -0.5px;
        }
        .brand-tagline p {
            font-size: 14px;
            color: rgba(255,255,255,0.5);
            line-height: 1.7;
        }
        .brand-dots {
            display: flex;
            gap: 8px;
        }
        .brand-dots span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
        }
        .brand-dots span:first-child { background: #4f46e5; }

        /* Right Panel */
        .login-form-panel {
            width: 380px;
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-title {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }
        .login-subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 32px;
        }

        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            color: #1e293b;
            background: #fff;
            outline: none;
            transition: all .2s ease;
        }
        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }
        .form-control::placeholder { color: #94a3b8; }
        .form-control.is-invalid { border-color: #ef4444; }

        .invalid-feedback {
            font-size: 12px;
            color: #ef4444;
            margin-top: 5px;
            display: block;
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
        }
        .remember-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #4f46e5;
            cursor: pointer;
        }
        .remember-row label {
            font-size: 13px;
            color: #64748b;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all .2s ease;
            letter-spacing: 0.2px;
        }
        .btn-login:hover {
            background: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
        }
        .btn-login:active { transform: translateY(0); }

        .alert-danger {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        @media (max-width: 700px) {
            .login-brand { display: none; }
            .login-form-panel { width: 100%; padding: 40px 28px; }
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    {{-- Brand Panel --}}
    <div class="login-brand">
        <div class="brand-logo">
            <img src="/anvis-favicon.png" alt="Logo">
            <span>BlogServices</span>
        </div>
        <div class="brand-tagline">
            <h2>Manage your blog content with ease.</h2>
            <p>Sign in to access the admin panel and manage companies, blog posts, and more.</p>
        </div>
        <div class="brand-dots">
            <span></span><span></span><span></span>
        </div>
    </div>

    {{-- Form Panel --}}
    <div class="login-form-panel">
        <h1 class="login-title">Welcome back</h1>
        <p class="login-subtitle">Sign in to continue to the admin panel.</p>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input id="email" type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                       value="{{ old('email') }}" placeholder="you@example.com" required autofocus autocomplete="email">
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input id="password" type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                       placeholder="Enter your password" required autocomplete="current-password">
            </div>

            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember">Remember me for 30 days</label>
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        </form>
    </div>
</div>
</body>
</html>
