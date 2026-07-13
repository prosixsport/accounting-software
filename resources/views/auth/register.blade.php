<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - Accounts System</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background:
                radial-gradient(
                    circle at top left,
                    rgba(255, 255, 255, 0.08),
                    transparent 28%
                ),
                linear-gradient(
                    135deg,
                    #050505 0%,
                    #111111 48%,
                    #2e2d4d 100%
                );
        }

        .auth-page {
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .auth-card {
            width: 450px;
            max-width: 100%;
            padding: 34px 34px 28px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.97);
            box-shadow: 0 30px 90px rgba(0, 0, 0, 0.45);
        }

        .brand-wrap {
            min-height: 92px;
            margin-bottom: 26px;
            padding: 18px 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            border-radius: 18px;
            background: #000000;
        }

        .brand-p {
            width: auto;
            height: 48px;
            max-width: 80px;
            object-fit: contain;
        }

        .brand-line {
            width: 2px;
            height: 46px;
            flex-shrink: 0;
            border-radius: 10px;
            background: #ffffff;
            opacity: 0.9;
        }

        .brand-prosix {
            width: auto;
            height: 34px;
            max-width: 190px;
            object-fit: contain;
        }

        .auth-title {
            margin: 0;
            color: #050505;
            font-size: 26px;
            font-weight: 900;
            text-align: center;
            letter-spacing: -0.5px;
        }

        .auth-subtitle {
            margin: 7px 0 22px;
            color: #6b7280;
            font-size: 14px;
            text-align: center;
        }

        .alert-box {
            margin-bottom: 16px;
            padding: 11px 13px;
            border: 1px solid #ffb4b4;
            border-radius: 10px;
            background: #fff0f0;
            color: #b42318;
            font-size: 13px;
            font-weight: 600;
        }

        .field-group {
            margin-bottom: 16px;
        }

        .field-group label {
            display: block;
            margin-bottom: 7px;
            color: #111827;
            font-size: 13px;
            font-weight: 800;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            z-index: 2;
            width: 18px;
            height: 18px;
            color: #6b7280;
            pointer-events: none;
        }

        .input-wrap input {
            width: 100%;
            height: 48px;
            padding: 0 46px 0 43px;
            border: 1.5px solid #d1d5db;
            border-radius: 12px;
            outline: none;
            background: #ffffff;
            color: #111827;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s;
        }

        .input-wrap input:focus {
            border-color: #000000;
            box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.08);
        }

        .input-wrap input.is-invalid {
            border-color: #dc3545;
        }

        .password-toggle {
            position: absolute;
            right: 11px;
            z-index: 3;
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 8px;
            background: #f3f4f6;
            color: #111111;
            cursor: pointer;
            transition: 0.2s;
        }

        .password-toggle:hover {
            background: #000000;
            color: #ffffff;
        }

        .password-toggle svg {
            width: 17px;
            height: 17px;
        }

        .auth-button {
            width: 100%;
            height: 50px;
            margin-top: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            border-radius: 13px;
            background: #000000;
            color: #ffffff;
            font-size: 15px;
            font-weight: 900;
            cursor: pointer;
            transition: 0.2s;
        }

        .auth-button:hover {
            background: #2e2d4d;
            transform: translateY(-1px);
        }

        .auth-button svg {
            width: 17px;
            height: 17px;
        }

        .auth-footer {
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 13px;
            text-align: center;
        }

        .auth-footer a {
            color: #000000;
            font-weight: 800;
            text-decoration: none;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .auth-page {
                padding: 14px;
                align-items: flex-start;
            }

            .auth-card {
                margin: 15px 0;
                padding: 25px 20px 22px;
                border-radius: 18px;
            }

            .brand-wrap {
                min-height: 80px;
                gap: 16px;
                padding: 15px;
            }

            .brand-p {
                height: 40px;
            }

            .brand-line {
                height: 38px;
            }

            .brand-prosix {
                height: 28px;
                max-width: 155px;
            }
        }
    </style>
</head>

<body>

<div class="auth-page">
    <div class="auth-card">

        <div class="brand-wrap">
            <img
                src="{{ asset('assets/images/P LOGO WHITE.png') }}"
                class="brand-p"
                alt="Prosix P Logo"
            >

            <div class="brand-line"></div>

            <img
                src="{{ asset('assets/images/PROSIX SPORTS LOGO PNG WHITE.png') }}"
                class="brand-prosix"
                alt="Prosix Sports Logo"
            >
        </div>

        <h1 class="auth-title">Create Account</h1>

        <p class="auth-subtitle">
            Create your Accounts System account
        </p>

        @if($errors->any())
            <div class="alert-box">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ url('/register') }}">
            @csrf

            <div class="field-group">
                <label for="name">Full Name</label>

                <div class="input-wrap">
                    <svg
                        class="input-icon"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 21a8 8 0 0 1 16 0"></path>
                    </svg>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
                        placeholder="Enter your full name"
                        autocomplete="name"
                        required
                        autofocus
                    >
                </div>
            </div>

            <div class="field-group">
                <label for="email">Email Address</label>

                <div class="input-wrap">
                    <svg
                        class="input-icon"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                        <path d="m3 7 9 6 9-6"></path>
                    </svg>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                        placeholder="Enter your email"
                        autocomplete="email"
                        required
                    >
                </div>
            </div>

            <div class="field-group">
                <label for="password">Password</label>

                <div class="input-wrap">
                    <svg
                        class="input-icon"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <rect x="3" y="11" width="18" height="10" rx="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="Enter password"
                        autocomplete="new-password"
                        required
                    >

                    <button
                        type="button"
                        class="password-toggle"
                        onclick="togglePassword('password', this)"
                        aria-label="Show password"
                    >
                        <svg
                            class="eye-open"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>

                        <svg
                            class="eye-closed"
                            style="display:none;"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="m3 3 18 18"></path>
                            <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"></path>
                            <path d="M9.9 4.2A9.7 9.7 0 0 1 12 4c6.5 0 10 8 10 8a18.7 18.7 0 0 1-2.2 3.2"></path>
                            <path d="M6.6 6.6C3.7 8.5 2 12 2 12s3.5 8 10 8a9.8 9.8 0 0 0 5.4-1.6"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="field-group">
                <label for="password_confirmation">Confirm Password</label>

                <div class="input-wrap">
                    <svg
                        class="input-icon"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <rect x="3" y="11" width="18" height="10" rx="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>

                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        placeholder="Confirm your password"
                        autocomplete="new-password"
                        required
                    >

                    <button
                        type="button"
                        class="password-toggle"
                        onclick="togglePassword('password_confirmation', this)"
                        aria-label="Show confirm password"
                    >
                        <svg
                            class="eye-open"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>

                        <svg
                            class="eye-closed"
                            style="display:none;"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="m3 3 18 18"></path>
                            <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"></path>
                            <path d="M9.9 4.2A9.7 9.7 0 0 1 12 4c6.5 0 10 8 10 8a18.7 18.7 0 0 1-2.2 3.2"></path>
                            <path d="M6.6 6.6C3.7 8.5 2 12 2 12s3.5 8 10 8a9.8 9.8 0 0 0 5.4-1.6"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="auth-button">
                Create Account

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M5 12h14"></path>
                    <path d="m13 6 6 6-6 6"></path>
                </svg>
            </button>
        </form>

        <div class="auth-footer">
            Already have an account?
            <a href="{{ url('/login') }}">Log in</a>
        </div>

    </div>
</div>

<script>
    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const eyeOpen = button.querySelector('.eye-open');
        const eyeClosed = button.querySelector('.eye-closed');

        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.style.display = 'none';
            eyeClosed.style.display = 'block';
            button.setAttribute('aria-label', 'Hide password');
        } else {
            input.type = 'password';
            eyeOpen.style.display = 'block';
            eyeClosed.style.display = 'none';
            button.setAttribute('aria-label', 'Show password');
        }
    }
</script>

</body>
</html>
