<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Login </title>
    <link rel="stylesheet" href="{{ asset('css/fontsource.css') }}" />

    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('css/adminlte.css') }}" />

    <style>
        body {
            transition: background-color 0.3s ease;
            background: linear-gradient(to right, #6a11cb, #2575fc);
        }

        .dark-mode {
            background: #121212;
        }

        .login-box {
            width: 420px;
            max-width: 95%;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
            border: none;
            overflow: hidden;
        }

        .card-outline.card-primary {
            border-top: 5px solid #007bff;
        }

        .login-card-body .input-group .form-control {
            border-right: 0;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .login-card-body .input-group .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
            border-color: #80bdff;
        }

        .login-card-body .input-group .form-control.is-invalid {
            border-color: #dc3545;
        }

        .login-card-body .input-group .form-control.is-invalid:focus {
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, .25);
        }

        .login-card-body .input-group .input-group-text {
            background-color: #fff;
            border-left: 0;
            border-color: #ced4da;
        }

        .dark-mode .login-card-body .input-group .input-group-text {
            background-color: #343a40;
            border-color: #6c757d;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: all 0.2s;
            padding: 0.6rem;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
        }

        .theme-switch-wrapper {
            position: fixed;
            top: 1rem;
            right: 1rem;
            display: flex;
            align-items: center;
            z-index: 1000;
        }

        .theme-switch {
            display: inline-block;
            height: 24px;
            position: relative;
            width: 50px;
        }

        .theme-switch input {
            display: none;
        }

        .slider {
            background-color: #ccc;
            bottom: 0;
            cursor: pointer;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            background-color: #fff;
            bottom: 4px;
            content: "";
            height: 16px;
            left: 4px;
            position: absolute;
            transition: .4s;
            width: 16px;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #007bff;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        .theme-switch-wrapper .bi {
            margin: 0 8px;
            font-size: 1.2rem;
            color: white;
        }
    </style>
</head>

<body class="hold-transition login-page">

    <div class="theme-switch-wrapper">
        <i class="bi bi-sun-fill"></i>
        <label class="theme-switch" for="checkbox">
            <input type="checkbox" id="checkbox" />
            <div class="slider round"></div>
        </label>
        <i class="bi bi-moon-fill"></i>
    </div>

    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="#" class="h1"><b>Admin</b></a>
            </div>
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>

                <form action="{{ route('login') }}" method="post" id="loginForm">
                    @csrf
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="email" name="email"
                                class="form-control @error('email') is-invalid @enderror" placeholder="Email"
                                value="{{ old('email') }}" required autocomplete="email" autofocus>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <i class="bi bi-envelope-fill"></i>
                                </div>
                            </div>
                        </div>
                        @error('email')
                            <span class="text-danger" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <div class="input-group">
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" id="password"
                                placeholder="Password" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <i id="togglePassword" class="bi bi-eye-fill" style="cursor: pointer;"></i>
                                </div>
                            </div>
                        </div>
                        @error('password')
                            <span class="text-danger" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-7">
                            <div class="icheck-primary">
                                <input type="checkbox" name="remember" id="remember"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="col-5">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/overlayscrollbars.min.js') }}"></script>
    <script src="{{ asset('js/adminlte.js') }}"></script>

    <script>
        $(function() {
            'use strict';

            $('#togglePassword').on('click', function() {
                const passwordField = $('#password');
                const fieldType = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', fieldType);

                $(this).toggleClass('bi-eye-fill bi-eye-slash-fill');
            });

            const $themeSwitch = $('#checkbox');
            const $body = $('body');
            const currentTheme = localStorage.getItem('theme');

            function setTheme(theme) {
                if (theme === 'dark') {
                    $body.addClass('dark-mode');
                    $themeSwitch.prop('checked', true);
                } else {
                    $body.removeClass('dark-mode');
                    $themeSwitch.prop('checked', false);
                }
            }

            setTheme(currentTheme || 'light');

            $themeSwitch.on('change', function() {
                if ($(this).is(':checked')) {
                    $body.addClass('dark-mode');
                    localStorage.setItem('theme', 'dark');
                } else {
                    $body.removeClass('dark-mode');
                    localStorage.setItem('theme', 'light');
                }
            });
        });
    </script>
</body>

</html>
