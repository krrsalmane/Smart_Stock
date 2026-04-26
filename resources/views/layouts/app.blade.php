<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStock | Management</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- Tailwind CSS (via CDN) -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

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
                        'body-base': ['Inter'],
                        'display-lg': ['Inter'],
                        'title-sm': ['Inter'],
                        'label-caps': ['Inter'],
                    },
                    colors: {
                        'brand-primary': '#00d4ff',
                        'brand-secondary': '#7b2ff7',
                        'brand-success': '#10b981',
                        'brand-warning': '#f59e0b',
                        'brand-danger': '#ef4444',
                        primary: '#a8e8ff',
                        'primary-container': '#00d4ff',
                        'primary-fixed': '#b4ebff',
                        'primary-fixed-dim': '#3cd7ff',
                        secondary: '#d2bbff',
                        'secondary-container': '#6800e4',
                        'secondary-fixed': '#eaddff',
                        'secondary-fixed-dim': '#d2bbff',
                        tertiary: '#ffd9a1',
                        'tertiary-container': '#feb528',
                        'tertiary-fixed': '#ffdeae',
                        'tertiary-fixed-dim': '#ffba3d',
                        surface: '#0e1417',
                        'surface-bright': '#333a3d',
                        'surface-container': '#1a2123',
                        'surface-container-low': '#161d1f',
                        'surface-container-lowest': '#080f12',
                        'surface-container-high': '#242b2e',
                        'surface-container-highest': '#2f3639',
                        'surface-dim': '#0e1417',
                        'surface-variant': '#2f3639',
                        background: '#0e1417',
                        error: '#ffb4ab',
                        'error-container': '#93000a',
                        outline: '#859398',
                        'outline-variant': '#3c494e',
                        'on-surface': '#dde3e7',
                        'on-surface-variant': '#bbc9cf',
                        'on-primary': '#003642',
                        'on-primary-container': '#00586b',
                        'on-secondary': '#3e008e',
                        'on-secondary-container': '#d2bbff',
                        'on-error': '#690005',
                        'on-error-container': '#ffdad6',
                        'on-background': '#dde3e7',
                    },
                    spacing: {
                        'sidebar_width': '260px',
                        'topbar_height': '64px',
                        'sidebar_collapsed': '72px',
                    },
                    fontSize: {
                        'body-sm': ['14px', { lineHeight: '1.5', fontWeight: '400' }],
                        'body-base': ['16px', { lineHeight: '1.6', fontWeight: '400' }],
                        'display-lg': ['32px', { lineHeight: '1.2', letterSpacing: '-0.02em', fontWeight: '700' }],
                        'title-sm': ['18px', { lineHeight: '1.4', fontWeight: '600' }],
                        'headline-md': ['24px', { lineHeight: '1.3', letterSpacing: '-0.01em', fontWeight: '600' }],
                        'label-caps': ['12px', { lineHeight: '1', letterSpacing: '0.05em', fontWeight: '700' }],
                        'mono-data': ['14px', { lineHeight: '1', fontWeight: '500' }],
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
        }
        
        /* Glass Card Component */
        .glass-card {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
        }
        
        /* Neon Shadow Effect */
        .neon-shadow:hover {
            box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        }
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #3c494e;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #859398;
        }
        
        /* Material Symbols Base */
        .material-symbols-outlined {
            font-variation-settings:
            'FILL' 0,
            'wght' 400,
            'GRAD' 0,
            'opsz' 24;
        }
        
        .material-symbols-outlined.filled {
            font-variation-settings:
            'FILL' 1,
            'wght' 400,
            'GRAD' 0,
            'opsz' 24;
        }
        
        /* KPI Card Hover Effects */
        .kpi-card {
            transition: all 0.3s ease;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            border-color: rgba(0, 212, 255, 0.3);
        }
        
        /* Table Styles */
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .data-table thead th {
            background: rgba(30, 41, 59, 0.6);
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #bbc9cf;
        }
        .data-table tbody tr {
            transition: background 0.2s ease;
        }
        .data-table tbody tr:hover {
            background: rgba(0, 212, 255, 0.05);
        }
        .data-table tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.025em;
        }
        .status-pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
        .status-approved { background: rgba(0, 212, 255, 0.2); color: #00d4ff; border: 1px solid rgba(0, 212, 255, 0.3); }
        .status-in_transit { background: rgba(123, 47, 247, 0.2); color: #7b2ff7; border: 1px solid rgba(123, 47, 247, 0.3); }
        .status-delivered { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
        .status-cancelled { background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
        
        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, #00d4ff 0%, #7b2ff7 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .btn-primary:hover {
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.4);
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: rgba(30, 41, 59, 0.6);
            color: #dde3e7;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .btn-secondary:hover {
            background: rgba(30, 41, 59, 0.8);
            border-color: rgba(0, 212, 255, 0.3);
        }
        
        /* Role Badge Styles */
        .role-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .role-admin { background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
        .role-magasinier { background: rgba(0, 212, 255, 0.2); color: #00d4ff; border: 1px solid rgba(0, 212, 255, 0.3); }
        .role-client { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
        .role-supplier { background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
        .role-delivery_agent { background: rgba(123, 47, 247, 0.2); color: #7b2ff7; border: 1px solid rgba(123, 47, 247, 0.3); }
        
        /* Smooth fade-in for sidebar content */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .sidebar-content-loaded {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</head>
<body class="font-body-base antialiased selection:bg-primary/30 selection:text-white flex overflow-hidden">

    <!-- SIDEBAR -->
    <aside class="fixed left-0 top-0 h-full w-[260px] border-r border-white/10 bg-surface-container/80 backdrop-blur-xl shadow-2xl flex flex-col z-50">
        <!-- Logo Area -->
        <div class="px-6 py-6 border-b border-white/10">
            <span class="text-2xl font-black tracking-tighter text-primary drop-shadow-[0_0_8px_rgba(0,212,255,0.5)]">SmartStock</span>
        </div>
        
        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto custom-scrollbar" id="sidebar-nav">
            
            {{-- Loading State --}}
            <div id="sidebar-loading" class="flex flex-col items-center justify-center py-12">
                <span class="material-symbols-outlined text-primary text-4xl animate-spin">progress_activity</span>
                <p class="text-on-surface-variant text-sm mt-3">Loading navigation...</p>
            </div>
            
            {{-- Navigation will be populated by JavaScript based on user role --}}
            <div id="sidebar-content" class="hidden">
                {{-- Content injected by JS --}}
            </div>
            
        </nav>

        <!-- User Area -->
        <div class="p-4 border-t border-white/10">
            <div class="flex items-center justify-between bg-surface-container-high/50 p-3 rounded-lg border border-white/5 hover:bg-surface-container-highest transition-colors cursor-pointer group">
                <div class="flex items-center truncate">
                    <div class="w-8 h-8 rounded-full overflow-hidden border border-primary/30">
                        <img alt="User" class="w-full h-full object-cover" src="https://ui-avatars.com/api/?name=User&background=00d4ff&color=fff" id="user-avatar"/>
                    </div>
                    <div class="ml-3 truncate">
                        <p id="current-user-name" class="text-sm font-medium text-on-surface truncate">Loading...</p>
                        <p id="current-user-role" class="text-xs font-semibold uppercase tracking-wider truncate">Loading...</p>
                    </div>
                </div>
                <button onclick="logoutUser()" title="Logout" class="text-on-surface-variant hover:text-error transition-colors">
                    <span class="material-symbols-outlined">logout</span>
                </button>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="flex flex-col flex-1 ml-[260px] min-h-screen">
        
        <!-- TOP APP BAR -->
        <header class="fixed top-0 right-0 w-[calc(100%-16rem)] border-b border-white/10 bg-surface/70 backdrop-blur-md shadow-lg shadow-black/20 flex justify-between items-center h-16 px-4 md:px-8 z-40">
            <div class="flex items-center gap-4">
                <h1 class="font-title-sm text-sm md:text-lg font-semibold uppercase tracking-widest text-on-surface">@yield('page_title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-4 md:gap-6">
                {{-- Notifications --}}
                <button class="relative flex items-center">
                    <span class="material-symbols-outlined text-on-surface-variant hover:text-primary cursor-pointer">notifications</span>
                    <span id="top-alert-dot" class="hidden absolute -top-1 -right-1 w-2 h-2 bg-secondary-container rounded-full"></span>
                </button>
                
                {{-- User Profile --}}
                <div class="flex items-center gap-3 cursor-pointer group">
                    <div class="w-8 h-8 rounded-full overflow-hidden border border-primary/30">
                        <img alt="User" class="w-full h-full object-cover" src="https://ui-avatars.com/api/?name=User&background=00d4ff&color=fff" id="user-avatar-top"/>
                    </div>
                    <span class="text-on-surface text-sm font-medium hidden md:block group-hover:text-primary" id="user-name-top">Loading...</span>
                </div>
            </div>
        </header>

        <!-- MAIN SCROLLABLE CONTENT -->
        <main class="pt-24 pb-20 md:pb-8 px-4 md:px-8 min-h-screen">
            @yield('content')
        </main>
    </div>

    <!-- API Client & Base Logic -->
    <script src="/js/api.js"></script>
    <script>
        // Sidebar navigation templates for each role
        const sidebarTemplates = {
            admin: `
                <a href="/dashboard" class="nav-link flex items-center px-4 py-3 rounded-l-md text-on-surface-variant hover:text-on-surface hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <span class="material-symbols-outlined mr-3">dashboard</span>
                    Dashboard
                </a>
                
                <p class="px-4 pt-4 pb-2 text-xs font-semibold text-on-surface-variant/60 uppercase tracking-wider">Inventory</p>
                <a href="/products" class="nav-link flex items-center px-4 py-3 rounded-l-md text-on-surface-variant hover:text-on-surface hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <span class="material-symbols-outlined mr-3">inventory_2</span>
                    Products
                </a>
                <a href="/warehouses" class="nav-link flex items-center px-4 py-3 rounded-l-md text-on-surface-variant hover:text-on-surface hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <span class="material-symbols-outlined mr-3">warehouse</span>
                    Warehouses
                </a>
                <a href="/categories" class="nav-link flex items-center px-4 py-3 rounded-l-md text-on-surface-variant hover:text-on-surface hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <span class="material-symbols-outlined mr-3">category</span>
                    Categories
                </a>

                <p class="px-4 pt-4 pb-2 text-xs font-semibold text-on-surface-variant/60 uppercase tracking-wider">Operations</p>
                <a href="/mouvements" class="nav-link flex items-center px-4 py-3 rounded-l-md text-on-surface-variant hover:text-on-surface hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <span class="material-symbols-outlined mr-3">swap_horiz</span>
                    Movements
                </a>

                <a href="/commands" class="nav-link flex items-center px-4 py-3 rounded-l-md text-on-surface-variant hover:text-on-surface hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <span class="material-symbols-outlined mr-3">shopping_cart</span>
                    Orders
                </a>

                <p class="px-4 pt-4 pb-2 text-xs font-semibold text-on-surface-variant/60 uppercase tracking-wider">Management</p>
                <a href="/suppliers" class="nav-link flex items-center px-4 py-3 rounded-l-md text-on-surface-variant hover:text-on-surface hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <span class="material-symbols-outlined mr-3">local_shipping</span>
                    Suppliers
                </a>
                <a href="/alerts" class="nav-link flex items-center justify-between px-4 py-3 rounded-l-md text-on-surface-variant hover:text-tertiary hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <div class="flex items-center">
                        <span class="material-symbols-outlined mr-3">warning</span>
                        Alerts
                    </div>
                    <span id="nav-alert-count" class="hidden bg-secondary-container text-[10px] font-bold text-white px-2 py-0.5 rounded-full">0</span>
                </a>
                <a href="/archives" class="nav-link flex items-center px-4 py-3 rounded-l-md text-on-surface-variant hover:text-on-surface hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <span class="material-symbols-outlined mr-3">history</span>
                    Archives
                </a>
            `,
            magasinier: `
                <a href="/dashboard" class="nav-link flex items-center px-4 py-3 rounded-l-md text-on-surface-variant hover:text-on-surface hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <span class="material-symbols-outlined mr-3">dashboard</span>
                    Dashboard
                </a>
                
                <p class="px-4 pt-4 pb-2 text-xs font-semibold text-on-surface-variant/60 uppercase tracking-wider">Inventory</p>
                <a href="/products" class="nav-link flex items-center px-4 py-3 rounded-l-md text-on-surface-variant hover:text-on-surface hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <span class="material-symbols-outlined mr-3">inventory_2</span>
                    Products
                </a>
                <a href="/warehouses" class="nav-link flex items-center px-4 py-3 rounded-l-md text-on-surface-variant hover:text-on-surface hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <span class="material-symbols-outlined mr-3">warehouse</span>
                    Warehouses
                </a>
                <a href="/categories" class="nav-link flex items-center px-4 py-3 rounded-l-md text-on-surface-variant hover:text-on-surface hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <span class="material-symbols-outlined mr-3">category</span>
                    Categories
                </a>

                <p class="px-4 pt-4 pb-2 text-xs font-semibold text-on-surface-variant/60 uppercase tracking-wider">Operations</p>
                <a href="/mouvements" class="nav-link flex items-center px-4 py-3 rounded-l-md text-on-surface-variant hover:text-on-surface hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <span class="material-symbols-outlined mr-3">swap_horiz</span>
                    Movements
                </a>

                <a href="/commands" class="nav-link flex items-center px-4 py-3 rounded-l-md text-on-surface-variant hover:text-on-surface hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <span class="material-symbols-outlined mr-3">shopping_cart</span>
                    Orders
                </a>

                <p class="px-4 pt-4 pb-2 text-xs font-semibold text-on-surface-variant/60 uppercase tracking-wider">Management</p>
                <a href="/suppliers" class="nav-link flex items-center px-4 py-3 rounded-l-md text-on-surface-variant hover:text-on-surface hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <span class="material-symbols-outlined mr-3">local_shipping</span>
                    Suppliers
                </a>
                <a href="/alerts" class="nav-link flex items-center justify-between px-4 py-3 rounded-l-md text-on-surface-variant hover:text-tertiary hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <div class="flex items-center">
                        <span class="material-symbols-outlined mr-3">warning</span>
                        Alerts
                    </div>
                    <span id="nav-alert-count" class="hidden bg-secondary-container text-[10px] font-bold text-white px-2 py-0.5 rounded-full">0</span>
                </a>
                <a href="/archives" class="nav-link flex items-center px-4 py-3 rounded-l-md text-on-surface-variant hover:text-on-surface hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <span class="material-symbols-outlined mr-3">history</span>
                    Archives
                </a>
            `,
            client: `
                <a href="/commands" class="nav-link flex items-center px-4 py-3 rounded-l-md text-on-surface-variant hover:text-on-surface hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <span class="material-symbols-outlined mr-3">shopping_cart</span>
                    Orders
                </a>
            `,
            delivery_agent: `
                <p class="px-4 pt-4 pb-2 text-xs font-semibold text-on-surface-variant/60 uppercase tracking-wider">Deliveries</p>
                <a href="/delivery-agent/dashboard" class="nav-link flex items-center px-4 py-3 rounded-l-md text-on-surface-variant hover:text-on-surface hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <span class="material-symbols-outlined mr-3">local_shipping</span>
                    My Deliveries
                </a>
            `,
            supplier: `
                <p class="px-4 pt-4 pb-2 text-xs font-semibold text-on-surface-variant/60 uppercase tracking-wider">Supplier</p>
                <a href="/supplier-portal" class="nav-link flex items-center px-4 py-3 rounded-l-md text-on-surface-variant hover:text-on-surface hover:bg-white/5 font-body-base text-body-sm font-medium tracking-wide transition-all duration-200">
                    <span class="material-symbols-outlined mr-3">receipt_long</span>
                    My Commands
                </a>
            `
        };

        // Initialize sidebar
        document.addEventListener('DOMContentLoaded', async () => {
            // Check if user is logged in
            if (!isAuthenticated()) {
                window.location.href = '/login';
                return;
            }

            try {
                // Fetch current user data
                const userResponse = await apiCall('/user', 'GET');
                if (userResponse.status === 200) {
                    const user = userResponse.data;
                    
                    // Update user info
                    document.getElementById('current-user-name').innerText = user.name;
                    document.getElementById('user-name-top').innerText = user.name;
                    
                    // Update avatars
                    const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=00d4ff&color=fff`;
                    document.getElementById('user-avatar').src = avatarUrl;
                    document.getElementById('user-avatar-top').src = avatarUrl;
                    
                    // Set role badge
                    const roleElement = document.getElementById('current-user-role');
                    roleElement.innerText = user.role;
                    roleElement.className = `text-xs font-semibold uppercase tracking-wider truncate role-badge role-${user.role}`;
                    
                    // Populate sidebar based on role
                    const sidebarContent = document.getElementById('sidebar-content');
                    const sidebarLoading = document.getElementById('sidebar-loading');
                    
                    if (sidebarTemplates[user.role]) {
                        sidebarContent.innerHTML = sidebarTemplates[user.role];
                        sidebarLoading.classList.add('hidden');
                        sidebarContent.classList.remove('hidden');
                        sidebarContent.classList.add('sidebar-content-loaded');
                    }
                    
                    // Highlight current active nav link
                    const currentPath = window.location.pathname;
                    document.querySelectorAll('.nav-link').forEach(link => {
                        if(link.getAttribute('href') === currentPath) {
                            link.classList.add('bg-primary/10', 'text-primary', 'border-r-2', 'border-primary');
                        }
                    });
                    
                    // Fetch active alerts count
                    const alertResponse = await apiCall('/alerts/active/count', 'GET');
                    if (alertResponse.status === 200 && alertResponse.data.active_alerts > 0) {
                        const count = alertResponse.data.active_alerts;
                        const badge = document.getElementById('nav-alert-count');
                        const dot = document.getElementById('top-alert-dot');
                        
                        if (badge) {
                            badge.innerText = count;
                            badge.classList.remove('hidden');
                        }
                        if (dot) {
                            dot.classList.remove('hidden');
                        }
                    }
                }
            } catch (error) {
                console.error("Failed to load sidebar", error);
                // Redirect to login on error
                window.location.href = '/login';
            }
        });

        // Logout function
        function logoutUser() {
            localStorage.removeItem('smartstock_token');
            window.location.href = '/login';
        }
    </script>
    
    <!-- Page Specific Scripts -->
    @stack('scripts')

</body>
</html>
