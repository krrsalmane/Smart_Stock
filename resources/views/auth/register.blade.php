<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStock | Create Account</title>
    
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
            background: linear-gradient(135deg, #6800e4 0%, #00d4ff 100%);
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            box-shadow: 0 0 25px rgba(0, 212, 255, 0.4);
            transform: translateY(-1px);
        }
        
        .text-success {
            color: #10b981;
        }
        .bg-success-container {
            background-color: rgba(16, 185, 129, 0.2);
        }
        .border-success {
            border-color: rgba(16, 185, 129, 0.5);
        }
    </style>
</head>
<body class="antialiased">

    <div class="glass-card w-full max-w-md p-8 relative overflow-hidden my-8 mx-4">
        
        <!-- Decorative glow -->
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary-container rounded-full mix-blend-screen filter blur-[50px] opacity-20"></div>

        <div class="text-center mb-8 relative z-10">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-secondary-container to-primary-container flex items-center justify-center shadow-[0_0_20px_rgba(104,0,228,0.3)] mb-4">
                <span class="material-symbols-outlined text-white text-4xl">person_add</span>
            </div>
            <h1 class="font-display-lg text-2xl font-bold tracking-tight text-on-surface">Create Your Account</h1>
            <p class="text-on-surface-variant text-sm mt-2">Join SmartStock inventory management system</p>
        </div>

        <!-- Error Message Container -->
        <div id="error-message" class="hidden bg-error-container/30 border border-error/50 text-error px-4 py-3 rounded-lg mb-6 text-sm text-center font-medium relative z-10">
        </div>

        <!-- Success Message Container -->
        <div id="success-message" class="hidden bg-success-container border border-success text-success px-4 py-3 rounded-lg mb-6 text-sm text-center font-medium relative z-10">
        </div>

        <form id="registerForm" class="space-y-5 relative z-10">
            <div>
                <label for="name" class="block text-sm font-medium text-on-surface-variant mb-2">Full Name</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-on-surface-variant">person</span>
                    </div>
                    <input type="text" id="name" name="name" required 
                           class="glass-input block w-full pl-10 pr-3 py-3 rounded-lg text-on-surface placeholder-on-surface-variant/50" 
                           placeholder="John Doe">
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-on-surface-variant mb-2">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-on-surface-variant">mail</span>
                    </div>
                    <input type="email" id="email" name="email" required 
                           class="glass-input block w-full pl-10 pr-3 py-3 rounded-lg text-on-surface placeholder-on-surface-variant/50" 
                           placeholder="john@example.com">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-on-surface-variant mb-2">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-on-surface-variant">lock</span>
                    </div>
                    <input type="password" id="password" name="password" required 
                           class="glass-input block w-full pl-10 pr-3 py-3 rounded-lg text-on-surface placeholder-on-surface-variant/50" 
                           placeholder="••••••••" minlength="6">
                </div>
                <p class="text-xs text-on-surface-variant/50 mt-1">Minimum 6 characters</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-on-surface-variant mb-2">Confirm Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-on-surface-variant">lock_reset</span>
                    </div>
                    <input type="password" id="password_confirmation" name="password_confirmation" required 
                           class="glass-input block w-full pl-10 pr-3 py-3 rounded-lg text-on-surface placeholder-on-surface-variant/50" 
                           placeholder="••••••••" minlength="6">
                </div>
            </div>

            <button type="submit" id="submitBtn" 
                    class="w-full py-3 px-4 btn-gradient text-white font-semibold rounded-lg shadow-lg flex justify-center items-center gap-2">
                <span id="btnText">Create Account</span>
                <span id="btnSpinner" class="material-symbols-outlined hidden">progress_activity</span>
            </button>
        </form>

        <!-- Login Link -->
        <div class="mt-6 text-center relative z-10">
            <p class="text-on-surface-variant text-sm">
                Already have an account? 
                <a href="/login" class="text-primary hover:text-primary-container font-medium transition-colors">
                    Sign In
                </a>
            </p>
        </div>
    </div>

    <!-- The API Helper -->
    <script src="/js/api.js"></script>
    <script>
        // If already logged in, skip the register page
        if (isAuthenticated()) {
            window.location.href = '/dashboard';
        }

        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault(); // Stop normal form submission

            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            
            const errorBox = document.getElementById('error-message');
            const successBox = document.getElementById('success-message');
            
            // UI Loading state
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');
            btnText.innerText = 'Creating Account...';
            btnSpinner.classList.remove('hidden');
            errorBox.classList.add('hidden');
            successBox.classList.add('hidden');

            // Validate passwords match
            if (password !== passwordConfirmation) {
                errorBox.innerText = 'Passwords do not match.';
                errorBox.classList.remove('hidden');
                btnText.innerText = 'Create Account';
                btnSpinner.classList.add('hidden');
                return;
            }

            try {
                // Call the Laravel Backend API register endpoint
                const response = await fetch('/api/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        name, 
                        email, 
                        password, 
                        password_confirmation: passwordConfirmation 
                    })
                });

                const result = await response.json();

                if (response.ok && result.token) {
                    // Success! Show success message and redirect to login
                    successBox.innerText = 'Account created successfully! Redirecting to login...';
                    successBox.classList.remove('hidden');
                    btnText.innerText = 'Success!';
                    btnSpinner.classList.remove('hidden');
                    btnSpinner.classList.remove('animate-spin');
                    btnSpinner.textContent = 'check_circle';
                    
                    // Clear the form
                    document.getElementById('registerForm').reset();
                    
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 1500);

                } else {
                    // Failed registration - show validation errors
                    let errorMsg = 'Registration failed. ';
                    if (result.error) {
                        errorMsg += result.error;
                    } else if (result.message) {
                        errorMsg += result.message;
                    } else if (result.name) {
                        errorMsg += Array.isArray(result.name) ? result.name[0] : result.name;
                    } else if (result.email) {
                        errorMsg += Array.isArray(result.email) ? result.email[0] : result.email;
                    } else if (result.password) {
                        errorMsg += Array.isArray(result.password) ? result.password[0] : result.password;
                    }
                    
                    errorBox.innerText = errorMsg;
                    errorBox.classList.remove('hidden');
                }
            } catch (error) {
                console.error("Registration attempt failed:", error);
                errorBox.innerText = 'Network error. Could not reach the server.';
                errorBox.classList.remove('hidden');
            } finally {
                // Reset UI if it didn't redirect
                if(btnText.innerText !== 'Success!') {
                    btnText.innerText = 'Create Account';
                    btnSpinner.classList.add('hidden');
                }
            }
        });
    </script>
</body>
</html>
