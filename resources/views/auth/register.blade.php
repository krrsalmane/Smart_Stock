<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStock | Create Account</title>
    
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
                            success: '#10b981',
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

    <div class="glass-panel w-full max-w-md rounded-2xl p-8 relative overflow-hidden my-8">
        
        <!-- Decorative subtle glow inside the card -->
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-brand-primary rounded-full mix-blend-screen filter blur-[50px] opacity-20"></div>

        <div class="text-center mb-8 relative z-10">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-brand-secondary to-brand-primary flex items-center justify-center shadow-[0_0_20px_rgba(123,47,247,0.3)] mb-4">
                <i class="ph ph-user-plus text-white text-3xl"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Create Your Account</h1>
            <p class="text-gray-400 text-sm mt-2">Join SmartStock inventory management system</p>
        </div>

        <!-- Error Message Container (Hidden by default) -->
        <div id="error-message" class="hidden bg-brand-danger/20 border border-brand-danger/50 text-brand-danger px-4 py-3 rounded-lg mb-6 text-sm text-center font-medium relative z-10">
        </div>

        <!-- Success Message Container (Hidden by default) -->
        <div id="success-message" class="hidden bg-brand-success/20 border border-brand-success/50 text-brand-success px-4 py-3 rounded-lg mb-6 text-sm text-center font-medium relative z-10">
        </div>

        <form id="registerForm" class="space-y-5 relative z-10">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Full Name</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-user text-gray-400 text-lg"></i>
                    </div>
                    <input type="text" id="name" name="name" required 
                           class="glass-input block w-full pl-10 pr-3 py-3 rounded-xl text-white placeholder-gray-500" 
                           placeholder="John Doe">
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-envelope-simple text-gray-400 text-lg"></i>
                    </div>
                    <input type="email" id="email" name="email" required 
                           class="glass-input block w-full pl-10 pr-3 py-3 rounded-xl text-white placeholder-gray-500" 
                           placeholder="john@example.com">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-lock-key text-gray-400 text-lg"></i>
                    </div>
                    <input type="password" id="password" name="password" required 
                           class="glass-input block w-full pl-10 pr-3 py-3 rounded-xl text-white placeholder-gray-500" 
                           placeholder="••••••••" minlength="6">
                </div>
                <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">Confirm Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-lock-key text-gray-400 text-lg"></i>
                    </div>
                    <input type="password" id="password_confirmation" name="password_confirmation" required 
                           class="glass-input block w-full pl-10 pr-3 py-3 rounded-xl text-white placeholder-gray-500" 
                           placeholder="••••••••" minlength="6">
                </div>
            </div>

            <button type="submit" id="submitBtn" 
                    class="w-full py-3 px-4 bg-gradient-to-r from-brand-secondary to-brand-primary hover:from-purple-500 hover:to-cyan-400 text-white font-semibold rounded-xl shadow-[0_0_15px_rgba(123,47,247,0.4)] hover:shadow-[0_0_25px_rgba(123,47,247,0.6)] transition-all duration-300 transform hover:-translate-y-0.5 flex justify-center items-center">
                <span id="btnText">Create Account</span>
                <i id="btnSpinner" class="ph ph-spinner-gap animate-spin text-xl ml-2 hidden"></i>
            </button>
        </form>

        <!-- Login Link -->
        <div class="mt-6 text-center relative z-10">
            <p class="text-gray-400 text-sm">
                Already have an account? 
                <a href="/login" class="text-brand-primary hover:text-white font-medium transition-colors">
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
                    btnSpinner.classList.remove('animate-spin');
                    btnSpinner.className = 'ph ph-check-circle text-xl ml-2 text-white';
                    
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
