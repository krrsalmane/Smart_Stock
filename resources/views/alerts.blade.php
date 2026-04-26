@extends('layouts.app')

@section('page_title', 'Alert Management')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-white flex items-center">
                System Alerts
                <span class="ml-3 px-2 py-0.5 rounded-md text-xs font-semibold bg-brand-danger/20 text-brand-danger border border-brand-danger/30" id="total_count">0 Alerts</span>
            </h2>
            <p class="text-gray-400 mt-1">Monitor and manage inventory alerts and notifications.</p>
        </div>

        <div class="flex gap-2">
            <select id="filter-status" onchange="loadTable()" class="bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-primary">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="dismissed">Dismissed</option>
                <option value="resolved">Resolved</option>
            </select>
        </div>
    </div>

    <!-- Data Table -->
    <div class="glass-panel overflow-hidden w-full relative z-10">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="text-xs text-gray-400 uppercase bg-black/30 border-b border-white/5">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider w-16">ID</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Product</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Triggered At</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body" class="divide-y divide-white/5 text-gray-300">
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="ph ph-spinner-gap animate-spin text-3xl mx-auto mb-2 text-brand-primary"></i>
                            <p>Loading alerts...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let userRole = '';

    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const userRes = await apiCall('/user', 'GET');
            if (userRes.status === 200) {
                userRole = userRes.data.role;
            }
        } catch (error) {
            console.error("Failed to fetch user role", error);
        }

        await loadTable();
    });

    async function loadTable() {
        const tBody = document.getElementById('table-body');
        const statusFilter = document.getElementById('filter-status').value;
        
        try {
            let endpoint = '/alerts';
            if (statusFilter) {
                endpoint += `?status=${statusFilter}`;
            }
            
            const response = await apiCall(endpoint, 'GET');
            if(response.status === 200) {
                const items = response.data;
                document.getElementById('total_count').innerText = items.length + ' Alerts';
                
                tBody.innerHTML = ''; 
                if(items.length === 0) {
                    tBody.innerHTML = `<tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No alerts found.</td></tr>`;
                    return;
                }

                items.forEach(item => {
                    const typeColors = {
                        'LOW_STOCK': 'bg-brand-warning/20 text-brand-warning border-brand-warning/30',
                        'OUT_OF_STOCK': 'bg-brand-danger/20 text-brand-danger border-brand-danger/30',
                        'OVERSTOCK': 'bg-brand-primary/20 text-brand-primary border-brand-primary/30'
                    };
                    const typeBadge = typeColors[item.type] || 'bg-gray-500/20 text-gray-400 border-gray-500/30';
                    
                    const statusColors = {
                        'active': 'bg-brand-danger/20 text-brand-danger border-brand-danger/30',
                        'dismissed': 'bg-gray-500/20 text-gray-400 border-gray-500/30',
                        'resolved': 'bg-brand-success/20 text-brand-success border-brand-success/30'
                    };
                    const statusBadge = statusColors[item.status] || 'bg-gray-500/20 text-gray-400 border-gray-500/30';
                    
                    const dateObj = new Date(item.triggered_at);
                    const productName = item.product ? item.product.name : 'Unknown Product';
                    const productSku = item.product ? item.product.sku : '';

                    const row = `
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4 font-mono text-gray-500">#${item.id}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md border ${typeBadge} flex items-center w-max">
                                    <i class="ph ph-warning mr-1 text-sm"></i> ${item.type.replace('_', ' ')}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-white">
                                ${productName}
                                <span class="text-xs font-mono text-brand-primary ml-2">${productSku}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md border ${statusBadge}">
                                    ${item.status}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-xs text-gray-400">
                                ${dateObj.toLocaleDateString()} ${dateObj.toLocaleTimeString()}
                            </td>
                            <td class="px-6 py-4 text-center text-lg space-x-2">
                                ${(userRole === 'admin' || userRole === 'magasinier') ? `
                                    ${item.status === 'active' ? `
                                        <button onclick="updateAlertStatus(${item.id}, 'dismissed')" title="Dismiss Alert" class="text-gray-500 hover:text-brand-warning transition-colors">
                                            <i class="ph ph-check-circle"></i>
                                        </button>
                                        <button onclick="updateAlertStatus(${item.id}, 'resolved')" title="Mark Resolved" class="text-gray-500 hover:text-brand-success transition-colors">
                                            <i class="ph ph-check-square"></i>
                                        </button>
                                    ` : ''}
                                    <button onclick="deleteAlert(${item.id})" title="Delete Alert" class="text-gray-500 hover:text-brand-danger transition-colors">
                                        <i class="ph ph-trash"></i>
                                    </button>
                                ` : '<span class="text-xs text-gray-500">View only</span>'}
                            </td>
                        </tr>
                    `;
                    tBody.insertAdjacentHTML('beforeend', row);
                });
            }
        } catch (error) {
            tBody.innerHTML = `<tr><td colspan="6" class="px-6 py-8 text-center text-brand-danger">API Error</td></tr>`;
        }
    }

    async function updateAlertStatus(id, newStatus) {
        try {
            const res = await apiCall(`/alerts/${id}`, 'PUT', { status: newStatus });
            if(res.status === 200) {
                showToast(`Alert ${newStatus}!`);
                loadTable();
            } else {
                showToast("Failed to update alert", "error");
            }
        } catch (error) {
            showToast("Server error", "error");
        }
    }

    async function deleteAlert(id) {
        if(!confirm("Are you sure you want to delete this alert?")) return;
        try {
            const res = await apiCall(`/alerts/${id}`, 'DELETE');
            if(res.status === 200 || res.status === 204) {
                showToast("Alert deleted!");
                loadTable();
            } else {
                showToast("Could not delete alert", "error");
            }
        } catch (error) {
            showToast("Error deleting alert", "error");
        }
    }
</script>
@endpush
