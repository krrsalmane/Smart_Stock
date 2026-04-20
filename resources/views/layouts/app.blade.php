<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStock | Management</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Simple Icons (Phosphor Icons - clean and premium) -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- Tailwind CSS (via CDN for simplicity) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js for data visualization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Custom Theme Configuration -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        // Premium Dark Theme Palette
                        brand: {
                            bg: '#0f0f1a',        // Deep navy/black background
                            panel: '#16162a',     // Slightly lighter panel
                            glass: 'rgba(26, 26, 46, 0.7)', // Transparent glass effect
                            border: 'rgba(0, 212, 255, 0.1)',
                            primary: '#00d4ff',   // Vibrant Cyan
                            secondary: '#7b2ff7', // Royal Purple
                            success: '#10b981',   // Emerald
                            danger: '#ef4444',    // Red
                            warning: '#f59e0b',   // Amber
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Base styles */
        body {
            background-color: theme('colors.brand.bg');
            color: #ffffff;
            /* Subtly animated background gradient */
            background-image: 
                radial-gradient(at 0% 0%, rgba(123, 47, 247, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(0, 212, 255, 0.05) 0px, transparent 50%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        /* Glassmorphism utility classes */
        .glass-panel {
            background: linear-gradient(145deg, rgba(30, 30, 50, 0.4) 0%, rgba(20, 20, 35, 0.8) 100%);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid theme('colors.brand.border');
            border-radius: 1rem;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }

        /* Smooth scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }

        /* Better text contrast */
        h1, h2, h3, .text-white {
            color: #ffffff !important;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .text-gray-400 {
            color: #a0a0b0 !important;
        }
        
        .text-gray-300 {
            color: #d0d0e0 !important;
        }
        
        .text-gray-500 {
            color: #707080 !important;
        }

        /* Role badge styles */
        .role-badge {
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .role-admin {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .role-magasinier {
            background: rgba(0, 212, 255, 0.2);
            color: #00d4ff;
            border: 1px solid rgba(0, 212, 255, 0.3);
        }
        
        .role-client {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        
        .role-supplier {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
    </style>
</head>
<body class="antialiased selection:bg-brand-secondary selection:text-white flex overflow-hidden">

    <!-- SIDEBAR -->
    <aside class="w-64 h-screen glass-panel rounded-none border-t-0 border-b-0 border-l-0 flex flex-col z-20">
        <!-- Logo Area -->
        <div class="h-20 flex items-center px-6 border-b border-brand-border">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-primary to-brand-secondary flex items-center justify-center shadow-lg shadow-brand-primary/20">
                <i class="ph ph-package text-white text-xl"></i>
            </div>
            <span class="ml-3 font-bold text-xl tracking-wide bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">
                SmartStock
            </span>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto" id="sidebar-nav">
            <!-- Dynamic Navigation Links will be injected or highlighted here -->
            <a href="/dashboard" class="nav-link flex items-center px-4 py-3 rounded-lg text-gray-400 font-medium transition-all duration-200 hover:bg-white/5 hover:text-brand-primary group" data-role="admin,magasinier">
                <i class="ph ph-squares-four text-xl mr-3 group-hover:drop-shadow-[0_0_8px_rgba(0,212,255,0.8)] transition-all"></i>
                Dashboard
            </a>
            
            <p class="px-4 pt-4 pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider nav-section" data-role="admin,magasinier">Inventory</p>
            
            <a href="/products" class="nav-link flex items-center px-4 py-3 rounded-lg text-gray-400 font-medium transition-all duration-200 hover:bg-white/5 hover:text-brand-primary group" data-role="admin,magasinier">
                <i class="ph ph-box-box text-xl mr-3 group-hover:drop-shadow-[0_0_8px_rgba(0,212,255,0.8)] transition-all"></i>
                Products
            </a>
            <a href="/warehouses" class="nav-link flex items-center px-4 py-3 rounded-lg text-gray-400 font-medium transition-all duration-200 hover:bg-white/5 hover:text-brand-primary group" data-role="admin,magasinier">
                <i class="ph ph-buildings text-xl mr-3 group-hover:drop-shadow-[0_0_8px_rgba(0,212,255,0.8)] transition-all"></i>
                Warehouses
            </a>
            <a href="/categories" class="nav-link flex items-center px-4 py-3 rounded-lg text-gray-400 font-medium transition-all duration-200 hover:bg-white/5 hover:text-brand-primary group" data-role="admin,magasinier">
                <i class="ph ph-tag text-xl mr-3 group-hover:drop-shadow-[0_0_8px_rgba(0,212,255,0.8)] transition-all"></i>
                Categories
            </a>

            <p class="px-4 pt-4 pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider nav-section" data-role="admin,magasinier">Operations</p>

            <a href="/mouvements" class="nav-link flex items-center px-4 py-3 rounded-lg text-gray-400 font-medium transition-all duration-200 hover:bg-white/5 hover:text-brand-primary group" data-role="admin,magasinier">
                <i class="ph ph-arrows-left-right text-xl mr-3 group-hover:drop-shadow-[0_0_8px_rgba(0,212,255,0.8)] transition-all"></i>
                Movements
            </a>
            <a href="/commands" class="nav-link flex items-center px-4 py-3 rounded-lg text-gray-400 font-medium transition-all duration-200 hover:bg-white/5 hover:text-brand-primary group" data-role="admin,magasinier,client">
                <i class="ph ph-shopping-cart text-xl mr-3 group-hover:drop-shadow-[0_0_8px_rgba(0,212,255,0.8)] transition-all"></i>
                Commands
            </a>
            <a href="/supplier-dashboard" class="nav-link flex items-center px-4 py-3 rounded-lg text-gray-400 font-medium transition-all duration-200 hover:bg-white/5 hover:text-brand-primary group" data-role="supplier">
                <i class="ph ph-truck text-xl mr-3 group-hover:drop-shadow-[0_0_8px_rgba(0,212,255,0.8)] transition-all"></i>
                My Deliveries
            </a>

            <p class="px-4 pt-4 pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider nav-section" data-role="admin,magasinier">Management</p>

            <a href="/suppliers" class="nav-link flex items-center px-4 py-3 rounded-lg text-gray-400 font-medium transition-all duration-200 hover:bg-white/5 hover:text-brand-primary group" data-role="admin,magasinier">
                <i class="ph ph-truck text-xl mr-3 group-hover:drop-shadow-[0_0_8px_rgba(0,212,255,0.8)] transition-all"></i>
                Suppliers
            </a>
            <a href="/alerts" class="nav-link flex items-center justify-between px-4 py-3 rounded-lg text-gray-400 font-medium transition-all duration-200 hover:bg-white/5 hover:text-warning group" data-role="admin,magasinier">
                <div class="flex items-center">
                    <i class="ph ph-warning-circle text-xl mr-3 group-hover:drop-shadow-[0_0_8px_rgba(245,158,11,0.8)] transition-all"></i>
                    Alerts
                </div>
                <!-- Dynamic badge -->
                <span id="nav-alert-count" class="hidden bg-brand-warning text-[10px] font-bold text-white px-2 py-0.5 rounded-full drop-shadow-md">0</span>
            </a>

            <p class="px-4 pt-4 pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider nav-section" data-role="supplier">Supplier</p>

            <a href="/supplier-portal" class="nav-link flex items-center px-4 py-3 rounded-lg text-gray-400 font-medium transition-all duration-200 hover:bg-white/5 hover:text-brand-primary group" data-role="supplier">
                <i class="ph ph-package text-xl mr-3 group-hover:drop-shadow-[0_0_8px_rgba(0,212,255,0.8)] transition-all"></i>
                My Commands
            </a>
        </nav>

        <!-- User Area -->
        <div class="p-4 border-t border-brand-border">
            <div class="flex items-center justify-between bg-black/20 p-3 rounded-xl border border-white/5 hover:bg-black/40 transition-colors cursor-pointer group">
                <div class="flex items-center truncate">
                    <div class="w-8 h-8 rounded-full bg-brand-secondary/30 border border-brand-secondary/50 flex items-center justify-center">
                        <i class="ph ph-user text-brand-secondary"></i>
                    </div>
                    <div class="ml-3 truncate">
                        <p id="current-user-name" class="text-sm font-medium text-white truncate">Loading...</p>
                        <p id="current-user-role" class="text-xs font-semibold uppercase tracking-wider truncate">Role</p>
                    </div>
                </div>
                <button onclick="logoutUser()" title="Logout" class="text-gray-400 hover:text-brand-danger transition-colors">
                    <i class="ph ph-sign-out text-lg"></i>
                </button>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT FLEX CONTAINER -->
    <div class="flex flex-col flex-1 h-screen relative z-10">
        
        <!-- TOPBAR -->
        <header class="h-20 glass-panel rounded-none border-t-0 border-r-0 border-l-0 flex items-center justify-between px-8 z-20">
            <!-- Page Title injected by children -->
            <h1 class="text-2xl font-semibold tracking-tight">@yield('page_title', 'Dashboard')</h1>
            
            <div class="flex items-center space-x-6">
                <!-- Search Box (Visual only for now) -->
                <div class="relative hidden md:block">
                    <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" placeholder="Search inventory..." class="bg-black/30 border border-white/10 text-sm rounded-full pl-10 pr-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-primary/50 focus:border-brand-primary w-64 transition-all">
                </div>
                
                <!-- Quick notifications -->
                <button class="relative text-gray-400 hover:text-white transition-colors">
                    <i class="ph ph-bell text-2xl"></i>
                    <span id="top-alert-dot" class="hidden absolute top-0 right-0 w-2.5 h-2.5 bg-brand-danger rounded-full shadow-[0_0_6px_rgba(239,68,68,0.8)]"></span>
                </button>
            </div>
        </header>

        <!-- MAIN SCROLLABLE AREA -->
        <main class="flex-1 overflow-y-auto p-8">
            <!-- Content injected here -->
            @yield('content')
        </main>
    </div>

    <!-- API Client & Base Logic -->
    <script src="/js/api.js"></script>
    <script>
        // Universal protection & startup logic
        document.addEventListener('DOMContentLoaded', async () => {
            // 1. Check if user is logged in
            if (!isAuthenticated()) {
                window.location.href = '/login';
                return;
            }

            // 2. Fetch current user data to populate the sidebar and handle role-based access
            try {
                const userResponse = await apiCall('/user', 'GET');
                if (userResponse.status === 200) {
                    const user = userResponse.data;
                    document.getElementById('current-user-name').innerText = user.name;
                    
                    // Set role badge with appropriate color
                    const roleElement = document.getElementById('current-user-role');
                    roleElement.innerText = user.role;
                    roleElement.className = `text-xs font-semibold uppercase tracking-wider truncate role-badge role-${user.role}`;
                    
                    // 3. Apply role-based navigation visibility
                    applyRoleBasedNavigation(user.role);
                    
                    // 4. Highlight current specific nav link
                    const currentPath = window.location.pathname;
                    document.querySelectorAll('.nav-link').forEach(link => {
                        if(link.getAttribute('href') === currentPath) {
                            link.classList.add('bg-white/10', 'text-brand-primary', 'shadow-inner');
                            link.querySelector('i').classList.add('drop-shadow-[0_0_8px_rgba(0,212,255,0.8)]');
                        }
                    });
                }

                // 5. Also fetch active alerts count for the red badge
                const alertResponse = await apiCall('/alerts/active/count', 'GET');
                if (alertResponse.status === 200 && alertResponse.data.active_alerts > 0) {
                    const count = alertResponse.data.active_alerts;
                    const badge = document.getElementById('nav-alert-count');
                    const dot = document.getElementById('top-alert-dot');
                    
                    badge.innerText = count;
                    badge.classList.remove('hidden');
                    dot.classList.remove('hidden');
                }
            } catch (error) {
                console.error("Failed to fetch initial startup data", error);
            }
        });

        // Role-based navigation control
        function applyRoleBasedNavigation(role) {
            const allNavItems = document.querySelectorAll('[data-role]');
            
            allNavItems.forEach(item => {
                const allowedRoles = item.getAttribute('data-role').split(',');
                if (allowedRoles.includes(role)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });

            // If non-admin user lands on dashboard, redirect them
            if (window.location.pathname === '/dashboard' && role !== 'admin') {
                if (role === 'supplier') {
                    window.location.href = '/supplier-portal';
                } else {
                    window.location.href = '/products';
                }
            }

            // If supplier lands on a non-supplier page, redirect them
            if (role === 'supplier' && window.location.pathname !== '/supplier-portal') {
                window.location.href = '/supplier-portal';
            }
        }
    </script>
    
    <!-- Page Specific Scripts -->
    @stack('scripts')

</body>
</html>
