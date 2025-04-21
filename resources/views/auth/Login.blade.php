<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .card {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 360px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #111827;
        }

        label {
            font-weight: bold;
            color: #374151;
            font-size: 14px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            margin-top: 8px;
            margin-bottom: 20px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            color: #374151;
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            width: 24px;
            height: 24px;
            fill: #6b7280;
            transition: fill 0.2s ease;
        }

        .toggle-password:hover {
            fill: #374151;
        }

        .hidden {
            display: none;
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #4f46e5;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #4338ca;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Login</h2>

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="/login">
            @csrf

            <label for="email">Email</label>
            <input type="email" name="email" placeholder="Masukkan email" required>

            <label for="password">Password</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" placeholder="Masukkan password" required>

                <!-- Eye (show) -->
                <svg id="eye-open" class="toggle-password" onclick="togglePassword()" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                    <path d="M572.52 241.4C518.3 135.5 407.1 64 288 64S57.72 135.5 3.48 241.4a48.26 48.26 0 0 0 0 29.2C57.72 376.5 168.9 448 288 448s230.3-71.5 284.5-177.4a48.26 48.26 0 0 0 0-29.2zM288 400c-88.4 0-175.6-55.1-223.6-144C112.4 167.1 199.6 112 288 112s175.6 55.1 223.6 144C463.6 344.9 376.4 400 288 400zm0-256a112 112 0 1 0 112 112A112 112 0 0 0 288 144z"/>
                </svg>

                <!-- Eye Slash (hide) -->
                <svg id="eye-closed" class="toggle-password hidden" onclick="togglePassword()" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                    <path d="M633.82 458.1 87.17 6.18A16 16 0 0 0 64 16v32.4a16 16 0 0 0 5.17 11.67l46.4 38.35C47.36 142.18 11.12 199.39 0 256c23.58 104 135.2 192 288 192a319 319 0 0 0 147.9-35.9l101 83.4a16 16 0 0 0 22.6-2.6l20.3-25.3a16 16 0 0 0-2.58-22.6zM288 400c-88.4 0-175.6-55.1-223.6-144 14.18-28.1 38.1-56.8 68.1-79.6l36.6 29.8a112 112 0 0 0 153.4 153.4l55.6 45.9A276.17 276.17 0 0 1 288 400zM320 160a111.9 111.9 0 0 1 108.5 85.6 12.15 12.15 0 0 1-.6 9.6l36.6 29.8c1.5-1.5 3.1-3 4.6-4.6A278.56 278.56 0 0 0 576 256c-23.58-104-135.2-192-288-192a319 319 0 0 0-100.5 16.7l55.6 45.9A111.9 111.9 0 0 1 320 160z"/>
                </svg>
            </div>

            <button type="submit">Login</button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const eyeOpen = document.getElementById("eye-open");
            const eyeClosed = document.getElementById("eye-closed");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeOpen.classList.add("hidden");
                eyeClosed.classList.remove("hidden");
            } else {
                passwordInput.type = "password";
                eyeOpen.classList.remove("hidden");
                eyeClosed.classList.add("hidden");
            }
        }
    </script>
</body>
</html>
