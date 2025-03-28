<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="login-container" style="position: relative; text-align: center;">
        <img src="{{ asset('images/image1.png') }}" alt="login" class="login-card-img"
            style="width: 100%; height: 100vh; object-fit: cover; position: absolute; z-index: -1;">

        <div class="login-options">
            <button onclick="showLogin()" class="btn btn-primary">Login to Account</button>
            <button onclick="showRegister()" class="btn btn-secondary">Create New Account</button>
        </div>

        <!-- Login Form -->
        <div id="loginForm" class="auth-form" style="display: none;">
            <form action="{{ route('login.submit') }}" method="POST">
                @csrf
                <input type="text" name="login_id" placeholder="Username" required value="{{ old('login_id') }}">
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>

            <!-- Display Validation Errors -->
            @if ($errors->any())
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        html: `{!! implode('<br>', $errors->all()) !!}`
                    });
                });
            </script>
        @endif
        </div>

    </div>

    <script>
        function showLogin() {
            document.getElementById("loginForm").style.display = "block";
        }

        function showRegister() {
            Swal.fire({
                title: 'Create Account',
                html: '<form action="{{ route('register') }}" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                '<input type="text" name="first_name" placeholder="First Name" required><br>' +
                '<input type="text" name="last_name" placeholder="Last Name"><br>' +
                '<input type="text" name="login_id" placeholder="Username" required><br>' +
                '<input type="email" name="email" placeholder="Email" required><br>' +
                '<input type="password" name="password" placeholder="Password" required><br>' +
                '<input type="password" name="password_confirmation" placeholder="Confirm Password" required><br>' +

                '<label for="currency">Select Currency:</label>' +
                '<select name="currency" required>' +
                '<option value="USD">USD</option>' +
                '<option value="EUR">EUR</option>' +
                '<option value="GBP">GBP</option>' +
                '</select><br>' +

                '<label for="account_type">Select Account Type:</label>' +
                '<select name="account_type" required>' +
                '<option value="Personal">Personal</option>' +
                '<option value="Business">Business</option>' +
                '</select><br>' +

                '<button type="submit">Register</button>' +
                '</form>',
                showConfirmButton: false
            });
        }
    </script>
</body>
</html>