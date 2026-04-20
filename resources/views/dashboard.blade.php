@extends('layouts.app')

@section('page_title', 'Strategic Dashboard')

@section('content')
    <!-- Welcome Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold tracking-tight text-white">Overview</h2>
        <p class="text-gray-400 mt-1">Real-time alerts and inventory statistics.</p>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Total Value -->
        <div class="glass-panel p-6 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-brand-success/20 rounded-full blur-2xl group-hover:bg-brand-success/30 transition-colors"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <h3 class="text-sm font-medium text-gray-400">Total Inventory Value</h3>
                <div class="w-10 h-10 rounded-lg bg-black/30 border border-white/5 flex items-center justify-center text-brand-success shadow-inner">
                    <i class="ph ph-currency-circle-dollar text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p class="text-3xl font-bold tracking-tight text-white flex items-baseline">
                    <span class="text-xl text-gray-500 mr-1">$</span>
                    <span id="metric-inventory-value">...</span>
                </p>
                <p class="text-xs text-brand-success mt-2 flex items-center">
                    <i class="ph ph-trend-up mr-1 text-sm"></i> Calculated dynamically
                </p>
            </div>
        </div>

        <!-- Pending Commands -->
        <div class="glass-panel p-6 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-cyan-500/20 rounded-full blur-2xl group-hover:bg-cyan-500/30 transition-colors"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <h3 class="text-sm font-medium text-gray-400">Pending Orders</h3>
                <div class="w-10 h-10 rounded-lg bg-black/30 border border-white/5 flex items-center justify-center text-cyan-400 shadow-inner">
                    <i class="ph ph-shopping-cart text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p id="metric-pending-commands" class="text-3xl font-bold tracking-tight text-white">...</p>
                <p class="text-xs text-cyan-400 mt-2 flex items-center">
                    Requires action
                </p>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="glass-panel p-6 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-brand-warning/20 rounded-full blur-2xl group-hover:bg-brand-warning/30 transition-colors"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <h3 class="text-sm font-medium text-gray-400">Low Stock Alerts</h3>
                <div class="w-10 h-10 rounded-lg bg-black/30 border border-white/5 flex items-center justify-center text-brand-warning shadow-inner">
                    <i class="ph ph-warning text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p id="metric-low-stock" class="text-3xl font-bold tracking-tight text-white">...</p>
                <p class="text-xs text-brand-warning mt-2 flex items-center">
                    Below threshold limits
                </p>
            </div>
        </div>

        <!-- Active Alerts (Discrepancy etc) -->
        <div class="glass-panel p-6 relative overflow-hidden group border-brand-danger/30">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-brand-danger/20 rounded-full blur-2xl group-hover:bg-brand-danger/30 transition-colors"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <h3 class="text-sm font-medium text-gray-400">Total Active Alerts</h3>
                <div class="w-10 h-10 rounded-lg bg-black/30 border border-white/5 flex items-center justify-center text-brand-danger shadow-inner">
                    <i class="ph ph-bell-ringing text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p id="metric-total-alerts" class="text-3xl font-bold tracking-tight text-white text-brand-danger drop-shadow-[0_0_8px_rgba(239,68,68,0.5)]">...</p>
                <p class="text-xs text-brand-danger mt-2 flex items-center opacity-80">
                    System-wide issues
                </p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Stock Movement Chart -->
        <div class="glass-panel p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="ph ph-chart-line-up ml-2 mr-3 text-brand-primary"></i> Stock Movements (Last 7 Days)
                </h3>
                <button onclick="exportMovementChart()" class="text-sm text-brand-primary hover:text-white transition-colors flex items-center">
                    <i class="ph ph-download-simple mr-1"></i> Export
                </button>
            </div>
            <div class="relative" style="height: 300px;">
                <canvas id="movementChart"></canvas>
            </div>
        </div>

        <!-- Inventory by Category Chart -->
        <div class="glass-panel p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="ph ph-chart-pie-slice ml-2 mr-3 text-brand-secondary"></i> Inventory by Category
                </h3>
                <button onclick="exportCategoryChart()" class="text-sm text-brand-primary hover:text-white transition-colors flex items-center">
                    <i class="ph ph-download-simple mr-1"></i> Export
                </button>
            </div>
            <div class="relative" style="height: 300px;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Alerts Trend & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Alerts Trend Chart -->
        <div class="glass-panel p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="ph ph-chart-bar ml-2 mr-3 text-brand-warning"></i> Alerts Trend
                </h3>
            </div>
            <div class="relative" style="height: 250px;">
                <canvas id="alertsChart"></canvas>
            </div>
        </div>

        <!-- Recent Activity Table -->
        <div class="glass-panel rounded-xl overflow-hidden shadow-2xl lg:col-span-2">
        <div class="px-6 py-5 border-b border-white/10 flex items-center justify-between bg-black/20">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="ph ph-activity ml-2 mr-3 text-brand-primary"></i> Recent Stock Movements
            </h3>
            <button class="text-sm text-brand-primary hover:text-white transition-colors flex items-center">
                View All <i class="ph ph-arrow-right ml-1"></i>
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-400">
                <thead class="text-xs uppercase bg-black/30 text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-medium tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-4 font-medium tracking-wider">Product</th>
                        <th scope="col" class="px-6 py-4 font-medium tracking-wider">Qty change</th>
                        <th scope="col" class="px-6 py-4 font-medium tracking-wider">Operator</th>
                        <th scope="col" class="px-6 py-4 font-medium tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody id="movements-table-body" class="divide-y divide-white/5">
                    <!-- Javascript will inject rows here -->
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                            <i class="ph ph-spinner-gap animate-spin text-3xl mx-auto mb-2 text-brand-primary"></i>
                            <p>Loading activity data...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Chart instances
    let movementChartInstance = null;
    let categoryChartInstance = null;
    let alertsChartInstance = null;

    document.addEventListener('DOMContentLoaded', async () => {
        // Get current user role from the sidebar
        const roleElement = document.getElementById('current-user-role');
        const userRole = roleElement ? roleElement.innerText.toLowerCase() : '';
        
        // Only admin can access dashboard
        if (userRole !== 'admin') {
            // Redirect non-admin users to products page
            window.location.href = '/products';
            return;
        }
        
        try {
            // Because dashboards are for admins, this uses the admin endpoint
            const response = await apiCall('/admin/dashboard', 'GET');
            
            if(response.status === 200) {
                const data = response.data.data; // Laravel returns wrapped in 'data'
                
                // Animate Numbers in
                document.getElementById('metric-inventory-value').innerText = data.total_inventory_value.toLocaleString(undefined, {minimumFractionDigits: 2});
                document.getElementById('metric-pending-commands').innerText = data.pending_commands_count;
                document.getElementById('metric-low-stock').innerText = data.low_stock_alerts_count;
                document.getElementById('metric-total-alerts').innerText = data.total_active_alerts;

                // Build the table dynamically
                const tBody = document.getElementById('movements-table-body');
                tBody.innerHTML = ''; // clear loading state

                if(data.recent_mouvements.length === 0) {
                    tBody.innerHTML = `<tr><td colspan="5" class="px-6 py-8 text-center">No recent movements found.</td></tr>`;
                } else {
                    data.recent_mouvements.forEach(movement => {
                        const isIN = movement.type === 'IN';
                        const badgeColor = isIN ? 'bg-brand-success/20 text-brand-success border-brand-success/30' : 'bg-brand-secondary/20 text-brand-secondary border-brand-secondary/30';
                        const icon = isIN ? 'ph-arrow-down-left' : 'ph-arrow-up-right';
                        const sign = isIN ? '+' : '-';
                        
                        const dateObj = new Date(movement.created_at);

                        const row = `
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md border ${badgeColor} flex items-center w-max">
                                        <i class="ph ${icon} mr-1 text-sm"></i> ${movement.type}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium text-white group-hover:text-brand-primary transition-colors">
                                    ${movement.product ? movement.product.name : 'Unknown Product'}
                                </td>
                                <td class="px-6 py-4 font-mono font-medium ${isIN ? 'text-brand-success' : 'text-brand-secondary'}">
                                    ${sign}${movement.quantity}
                                </td>
                                <td class="px-6 py-4 flex items-center">
                                    <div class="w-6 h-6 rounded-full bg-gray-700 flex items-center justify-center mr-2 text-[10px] text-white">
                                        ${movement.user ? movement.user.name.charAt(0) : '?'}
                                    </div>
                                    ${movement.user ? movement.user.name : 'System'}
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 border-l border-white/5">
                                    ${dateObj.toLocaleDateString()} <br> 
                                    <span class="text-[10px]">${dateObj.toLocaleTimeString()}</span>
                                </td>
                            </tr>
                        `;
                        tBody.insertAdjacentHTML('beforeend', row);
                    });
                }

                // Load charts
                await loadCharts();

            } else {
                throw new Error("Failed");
            }
        } catch (error) {
            console.error("Dashboard population failed", error);
            // Only show error for admin users
            document.getElementById('movements-table-body').innerHTML = `<tr><td colspan="5" class="px-6 py-8 text-center text-brand-danger">Failed to load dashboard data.</td></tr>`;
        }
    });

    // Load all charts
    async function loadCharts() {
        await Promise.all([
            loadMovementChart(),
            loadCategoryChart(),
            loadAlertsChart()
        ]);
    }

    // Load Stock Movement Chart
    async function loadMovementChart() {
        try {
            const response = await apiCall('/reports/movement-chart', 'GET');
            if (response.status === 200) {
                const data = response.data;
                const ctx = document.getElementById('movementChart').getContext('2d');
                
                movementChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Stock IN',
                                data: data.in_data,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'Stock OUT',
                                data: data.out_data,
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.4,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: '#a0a0b0' }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: '#707080' },
                                grid: { color: 'rgba(255, 255, 255, 0.05)' }
                            },
                            y: {
                                ticks: { color: '#707080' },
                                grid: { color: 'rgba(255, 255, 255, 0.05)' }
                            }
                        }
                    }
                });
            }
        } catch (error) {
            console.error('Failed to load movement chart:', error);
        }
    }

    // Load Category Chart
    async function loadCategoryChart() {
        try {
            const response = await apiCall('/reports/category-chart', 'GET');
            if (response.status === 200) {
                const data = response.data;
                const ctx = document.getElementById('categoryChart').getContext('2d');
                
                categoryChartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.values,
                            backgroundColor: [
                                '#00d4ff',
                                '#7b2ff7',
                                '#10b981',
                                '#f59e0b',
                                '#ef4444',
                                '#8b5cf6',
                                '#ec4899'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: { color: '#a0a0b0' }
                            }
                        }
                    }
                });
            }
        } catch (error) {
            console.error('Failed to load category chart:', error);
        }
    }

    // Load Alerts Chart
    async function loadAlertsChart() {
        try {
            const response = await apiCall('/reports/alerts-chart', 'GET');
            if (response.status === 200) {
                const data = response.data;
                const ctx = document.getElementById('alertsChart').getContext('2d');
                
                alertsChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Active Alerts',
                            data: data.values,
                            backgroundColor: 'rgba(245, 158, 11, 0.6)',
                            borderColor: '#f59e0b',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: '#a0a0b0' }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: '#707080' },
                                grid: { color: 'rgba(255, 255, 255, 0.05)' }
                            },
                            y: {
                                ticks: { color: '#707080' },
                                grid: { color: 'rgba(255, 255, 255, 0.05)' }
                            }
                        }
                    }
                });
            }
        } catch (error) {
            console.error('Failed to load alerts chart:', error);
        }
    }

    // Export chart as image
    function exportMovementChart() {
        if (movementChartInstance) {
            const link = document.createElement('a');
            link.download = 'stock-movements-chart.png';
            link.href = movementChartInstance.toBase64Image();
            link.click();
        }
    }

    function exportCategoryChart() {
        if (categoryChartInstance) {
            const link = document.createElement('a');
            link.download = 'inventory-by-category.png';
            link.href = categoryChartInstance.toBase64Image();
            link.click();
        }
    }
</script>
@endpush
