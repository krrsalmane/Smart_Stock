<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStock | Secure Login</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: {
                            bg: '#0f0f1a',
                            primary: '#00d4ff',
                            secondary: '#7b2ff7',
                            danger: '#ef4444',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            background-color: theme('colors.brand.bg');
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(123, 47, 247, 0.15), transparent 40%),
                radial-gradient(circle at 85% 30%, rgba(0, 212, 255, 0.1), transparent 40%);
            min-height: 100vh;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .glass-panel {
            background: linear-gradient(145deg, rgba(30, 30, 50, 0.4) 0%, rgba(20, 20, 35, 0.8) 100%);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            border-left: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        /* Custom Input styling */
        .glass-input {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .glass-input:focus {
            background: rgba(0, 0, 0, 0.4);
            border-color: theme('colors.brand.primary');
            box-shadow: 0 0 15px rgba(0, 212, 255, 0.2);
            outline: none;
        }
    </style>
</head>
<body class="antialiased">

    <div class="glass-panel w-full max-w-md rounded-2xl p-8 relative overflow-hidden">
        
        <!-- Decorative subtle glow inside the card -->
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-brand-secondary rounded-full mix-blend-screen filter blur-[50px] opacity-20"></div>

        <div class="text-center mb-8 relative z-10">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-brand-primary to-brand-secondary flex items-center justify-center shadow-[0_0_20px_rgba(0,212,255,0.3)] mb-4">
                <i class="ph ph-package text-white text-3xl"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Welcome to SmartStock</h1>
            <p class="text-gray-400 text-sm mt-2">Enter your credentials to access the system</p>
        </div>

        <!-- Error Message Container (Hidden by default) -->
        <div id="error-message" class="hidden bg-brand-danger/20 border border-brand-danger/50 text-brand-danger px-4 py-3 rounded-lg mb-6 text-sm text-center font-medium relative z-10">
            Invalid email or password.
        </div>

        <form id="loginForm" class="space-y-6 relative z-10">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-envelope-simple text-gray-400 text-lg"></i>
                    </div>
                    <input type="email" id="email" name="email" required 
                           class="glass-input block w-full pl-10 pr-3 py-3 rounded-xl text-white placeholder-gray-500" 
                           placeholder="admin@test.com">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                    <a href="#" class="text-xs text-brand-primary hover:text-white transition-colors">Forgot password?</a>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-lock-key text-gray-400 text-lg"></i>
                    </div>
                    <input type="password" id="password" name="password" required 
                           class="glass-input block w-full pl-10 pr-3 py-3 rounded-xl text-white placeholder-gray-500" 
                           placeholder="••••••••">
                </div>
            </div>

            <button type="submit" id="submitBtn" 
                    class="w-full py-3 px-4 bg-gradient-to-r from-brand-primary to-brand-secondary hover:from-cyan-400 hover:to-purple-500 text-white font-semibold rounded-xl shadow-[0_0_15px_rgba(123,47,247,0.4)] hover:shadow-[0_0_25px_rgba(123,47,247,0.6)] transition-all duration-300 transform hover:-translate-y-0.5 flex justify-center items-center">
                <span id="btnText">Sign In</span>
                <i id="btnSpinner" class="ph ph-spinner-gap animate-spin text-xl ml-2 hidden"></i>
            </button>
        </form>

        <!-- Register Link -->
        <div class="mt-6 text-center relative z-10">
            <p class="text-gray-400 text-sm">
                Don't have an account? 
                <a href="/register" class="text-brand-primary hover:text-white font-medium transition-colors">
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
                    btnSpinner.classList.remove('animate-spin');
                    btnSpinner.className = 'ph ph-check-circle text-xl ml-2 text-white';
                    
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
