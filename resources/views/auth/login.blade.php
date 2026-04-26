<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStock | Secure Login</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'body-base': ['Inter'],
                        'display-lg': ['Inter'],
                        'title-sm': ['Inter'],
                    },
                    colors: {
                        primary: '#a8e8ff',
                        'primary-container': '#00d4ff',
                        secondary: '#d2bbff',
                        'secondary-container': '#6800e4',
                        tertiary: '#ffd9a1',
                        'tertiary-container': '#feb528',
                        surface: '#0e1417',
                        'surface-container': '#1a2123',
                        'surface-container-high': '#242b2e',
                        'surface-container-highest': '#2f3639',
                        'on-surface': '#dde3e7',
                        'on-surface-variant': '#bbc9cf',
                        error: '#ffb4ab',
                        'error-container': '#93000a',
                        outline: '#859398',
                        'outline-variant': '#3c494e',
                    },
                },
            },
        }
    </script>
    
    <style>
        body {
            background-color: #0e1417;
            color: #dde3e7;
            font-family: 'Inter', sans-serif;
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(104, 0, 228, 0.15), transparent 40%),
                radial-gradient(circle at 85% 30%, rgba(0, 212, 255, 0.1), transparent 40%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .glass-input {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .glass-input:focus {
            background: rgba(0, 0, 0, 0.4);
            border-color: #00d4ff;
            box-shadow: 0 0 15px rgba(0, 212, 255, 0.2);
            outline: none;
        }
        
        .material-symbols-outlined {
            font-variation-settings:
            'FILL' 0,
            'wght' 400,
            'GRAD' 0,
            'opsz' 24;
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #00d4ff 0%, #6800e4 100%);
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            box-shadow: 0 0 25px rgba(0, 212, 255, 0.4);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="antialiased">

    <div class="glass-card w-full max-w-md p-8 relative overflow-hidden mx-4">
        
        <!-- Decorative glow -->
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-secondary-container rounded-full mix-blend-screen filter blur-[50px] opacity-20"></div>

        <div class="text-center mb-8 relative z-10">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-primary-container to-secondary-container flex items-center justify-center shadow-[0_0_20px_rgba(0,212,255,0.3)] mb-4">
                <span class="material-symbols-outlined text-white text-4xl">warehouse</span>
            </div>
            <h1 class="font-display-lg text-2xl font-bold tracking-tight text-on-surface">Welcome to SmartStock</h1>
            <p class="text-on-surface-variant text-sm mt-2">Enter your credentials to access the system</p>
        </div>

        <!-- Error Message Container -->
        <div id="error-message" class="hidden bg-error-container/30 border border-error/50 text-error px-4 py-3 rounded-lg mb-6 text-sm text-center font-medium relative z-10">
            Invalid email or password.
        </div>

        <form id="loginForm" class="space-y-6 relative z-10">
            <div>
                <label for="email" class="block text-sm font-medium text-on-surface-variant mb-2">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-on-surface-variant">mail</span>
                    </div>
                    <input type="email" id="email" name="email" required 
                           class="glass-input block w-full pl-10 pr-3 py-3 rounded-lg text-on-surface placeholder-on-surface-variant/50" 
                           placeholder="admin@test.com">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-sm font-medium text-on-surface-variant">Password</label>
                    <a href="#" class="text-xs text-primary hover:text-primary-container transition-colors">Forgot password?</a>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-on-surface-variant">lock</span>
                    </div>
                    <input type="password" id="password" name="password" required 
                           class="glass-input block w-full pl-10 pr-3 py-3 rounded-lg text-on-surface placeholder-on-surface-variant/50" 
                           placeholder="••••••••">
                </div>
            </div>

            <button type="submit" id="submitBtn" 
                    class="w-full py-3 px-4 btn-gradient text-white font-semibold rounded-lg shadow-lg flex justify-center items-center gap-2">
                <span id="btnText">Sign In</span>
                <span id="btnSpinner" class="material-symbols-outlined hidden">progress_activity</span>
            </button>
        </form>

        <!-- Register Link -->
        <div class="mt-6 text-center relative z-10">
            <p class="text-on-surface-variant text-sm">
                Don't have an account? 
                <a href="/register" class="text-primary hover:text-primary-container font-medium transition-colors">
                    Create Account
                </a>
            </p>
        </div>
    </div>

    <!-- The API Helper -->
    <script src="/js/api.js"></script>
    <script>
        // If already logged in, skip the login page entirely
        if (isAuthenticated()) {
            window.location.href = '/dashboard';
        }

        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault(); // Stop normal form submission

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorBox = document.getElementById('error-message');
            
            // UI Loading state
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');
            btnText.innerText = 'Authenticating...';
            btnSpinner.classList.remove('hidden');
            errorBox.classList.add('hidden');

            try {
                // Call the Laravel Backend API we built natively
                // Note: We don't use apiCall here because we don't have a token yet and we want to catch specific 401s
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const result = await response.json();

                if (response.ok && result.token) {
                    // Success! Save the token directly in the browser
                    localStorage.setItem('smartstock_token', result.token);
                    
                    // Show a quick success state before redirecting
                    btnText.innerText = 'Success! Redirecting...';
                    btnSpinner.classList.remove('hidden');
                    btnSpinner.classList.add('animate-spin');
                    
                    setTimeout(() => {
                        window.location.href = '/dashboard';
                    }, 500);

                } else {
                    // Failed login (wrong password, etc)
                    errorBox.innerText = result.error || 'Invalid credentials provided.';
                    errorBox.classList.remove('hidden');
                }
            } catch (error) {
                console.error("Login attempt failed:", error);
                errorBox.innerText = 'Network error. Could not reach the server.';
                errorBox.classList.remove('hidden');
            } finally {
                // Reset UI if it didn't redirect
                if(btnText.innerText !== 'Success! Redirecting...') {
                    btnText.innerText = 'Sign In';
                    btnSpinner.classList.add('hidden');
                }
            }
        });
    </script>
</body>
</html>
