@extends('layouts.app')

@section('page_title', 'Supplier Portal')

@section('content')
    <!-- Welcome Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold tracking-tight text-white">Supplier Dashboard</h2>
        <p class="text-gray-400 mt-1">Manage your deliveries and view assigned orders.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Pending Orders -->
        <div class="glass-panel p-6 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-brand-warning/20 rounded-full blur-2xl group-hover:bg-brand-warning/30 transition-colors"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <h3 class="text-sm font-medium text-gray-400">Pending Orders</h3>
                <div class="w-10 h-10 rounded-lg bg-black/30 border border-white/5 flex items-center justify-center text-brand-warning shadow-inner">
                    <i class="ph ph-clock text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p id="stat-pending" class="text-3xl font-bold tracking-tight text-white">...</p>
                <p class="text-xs text-brand-warning mt-2">Awaiting your response</p>
            </div>
        </div>

        <!-- Confirmed Deliveries -->
        <div class="glass-panel p-6 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-brand-primary/20 rounded-full blur-2xl group-hover:bg-brand-primary/30 transition-colors"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <h3 class="text-sm font-medium text-gray-400">Confirmed Deliveries</h3>
                <div class="w-10 h-10 rounded-lg bg-black/30 border border-white/5 flex items-center justify-center text-brand-primary shadow-inner">
                    <i class="ph ph-check-circle text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p id="stat-confirmed" class="text-3xl font-bold tracking-tight text-white">...</p>
                <p class="text-xs text-brand-primary mt-2">Successfully delivered</p>
            </div>
        </div>

        <!-- Total Products Supplied -->
        <div class="glass-panel p-6 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-brand-success/20 rounded-full blur-2xl group-hover:bg-brand-success/30 transition-colors"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <h3 class="text-sm font-medium text-gray-400">Products Supplied</h3>
                <div class="w-10 h-10 rounded-lg bg-black/30 border border-white/5 flex items-center justify-center text-brand-success shadow-inner">
                    <i class="ph ph-package text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p id="stat-products" class="text-3xl font-bold tracking-tight text-white">...</p>
                <p class="text-xs text-brand-success mt-2">In your catalog</p>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="glass-panel rounded-xl overflow-hidden shadow-2xl mb-8">
        <div class="px-6 py-5 border-b border-white/10 flex items-center justify-between bg-black/20">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="ph ph-shopping-cart ml-2 mr-3 text-brand-primary"></i> Purchase Orders
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-400">
                <thead class="text-xs uppercase bg-black/30 text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-medium tracking-wider">Order ID</th>
                        <th scope="col" class="px-6 py-4 font-medium tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 font-medium tracking-wider">Products</th>
                        <th scope="col" class="px-6 py-4 font-medium tracking-wider">Order Date</th>
                        <th scope="col" class="px-6 py-4 font-medium tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="orders-table-body" class="divide-y divide-white/5">
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                            <i class="ph ph-spinner-gap animate-spin text-3xl mx-auto mb-2 text-brand-primary"></i>
                            <p>Loading orders...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Confirm Delivery Modal -->
    <div id="deliveryModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('deliveryModal').classList.add('hidden')"></div>
        <div class="glass-panel w-full max-w-md relative z-10 p-6 rounded-2xl border-t border-white/20">
            <div class="flex justify-between items-center mb-6 border-b border-white/10 pb-4">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="ph ph-truck text-brand-primary mr-2"></i> Confirm Delivery
                </h3>
                <button onclick="document.getElementById('deliveryModal').classList.add('hidden')" class="text-gray-400 hover:text-white transition-colors">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>

            <form id="deliveryForm" class="space-y-4">
                <input type="hidden" id="d_command_id">
                <input type="hidden" id="d_supplier_id">
                
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Delivery Status *</label>
                    <select id="d_status" required class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary">
                        <option value="confirmed">Confirmed - Shipped</option>
                        <option value="delivered">Delivered - Received</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Delivery Date *</label>
                    <input type="date" id="d_delivery_date" required class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Tracking Number / Notes</label>
                    <textarea id="d_notes" rows="3" class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary" placeholder="Optional tracking info or notes"></textarea>
                </div>

                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-white/10">
                    <button type="button" onclick="document.getElementById('deliveryModal').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-white/10 text-gray-300 hover:bg-white/5 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="btnSubmitDelivery" class="bg-brand-primary hover:bg-cyan-400 text-black font-semibold px-6 py-2 rounded-lg shadow-[0_0_15px_rgba(0,212,255,0.4)] transition-all">
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let supplierId = null;

    document.addEventListener('DOMContentLoaded', async () => {
        await loadSupplierData();
    });

    async function loadSupplierData() {
        try {
            // Get current user info
            const userRes = await apiCall('/user', 'GET');
            if (userRes.status === 200) {
                // For now, we'll use the first supplier - in production, link user to supplier
                const suppliersRes = await apiCall('/suppliers', 'GET');
                if (suppliersRes.status === 200 && suppliersRes.data.length > 0) {
                    supplierId = suppliersRes.data[0].id;
                    await loadOrders();
                    await loadStats(suppliersRes.data[0]);
                }
            }
        } catch (error) {
            console.error("Failed to load supplier data", error);
        }
    }

    async function loadStats(supplier) {
        document.getElementById('stat-products').innerText = supplier.products ? supplier.products.length : 0;
        
        // Count orders from pivot
        let pending = 0;
        let confirmed = 0;
        
        if (supplier.commands) {
            supplier.commands.forEach(cmd => {
                const pivotStatus = cmd.pivot.status || 'pending';
                if (pivotStatus === 'pending' || pivotStatus === 'approved') pending++;
                else if (pivotStatus === 'delivered') confirmed++;
            });
        }
        
        document.getElementById('stat-pending').innerText = pending;
        document.getElementById('stat-confirmed').innerText = confirmed;
    }

    async function loadOrders() {
        const tBody = document.getElementById('orders-table-body');
        try {
            const res = await apiCall(`/suppliers/${supplierId}`, 'GET');
            if(res.status === 200) {
                const supplier = res.data;
                const commands = supplier.commands || [];
                
                tBody.innerHTML = '';
                
                if(commands.length === 0) {
                    tBody.innerHTML = `<tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No orders assigned yet.</td></tr>`;
                    return;
                }

                commands.forEach(cmd => {
                    const pivotStatus = cmd.pivot.status || 'pending';
                    const statusColors = {
                        'pending': 'bg-brand-warning/20 text-brand-warning border-brand-warning/30',
                        'confirmed': 'bg-brand-primary/20 text-brand-primary border-brand-primary/30',
                        'delivered': 'bg-brand-success/20 text-brand-success border-brand-success/30',
                        'cancelled': 'bg-brand-danger/20 text-brand-danger border-brand-danger/30'
                    };
                    const badgeClass = statusColors[pivotStatus] || 'bg-gray-500/20 text-gray-400 border-gray-500/30';
                    const orderDate = new Date(cmd.pivot.order_date || cmd.created_at).toLocaleDateString();

                    const productCount = cmd.products ? cmd.products.length : 0;

                    const row = `
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-mono text-gray-500">#${cmd.id}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md border ${badgeClass}">
                                    ${pivotStatus}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-brand-primary">${productCount}</span> products
                            </td>
                            <td class="px-6 py-4 text-xs">${orderDate}</td>
                            <td class="px-6 py-4 text-center">
                                ${pivotStatus === 'pending' || pivotStatus === 'approved' ? `
                                    <button onclick="openDeliveryModal(${cmd.id})" class="bg-brand-primary/20 text-brand-primary px-3 py-1 rounded-lg hover:bg-brand-primary/30 transition-colors text-xs font-medium">
                                        <i class="ph ph-check mr-1"></i> Update Status
                                    </button>
                                ` : '<span class="text-gray-500 text-xs">Completed</span>'}
                            </td>
                        </tr>
                    `;
                    tBody.insertAdjacentHTML('beforeend', row);
                });
            }
        } catch (error) {
            tBody.innerHTML = `<tr><td colspan="5" class="px-6 py-8 text-center text-brand-danger">API Error</td></tr>`;
        }
    }

    function openDeliveryModal(commandId) {
        document.getElementById('d_command_id').value = commandId;
        document.getElementById('d_supplier_id').value = supplierId;
        document.getElementById('d_delivery_date').value = new Date().toISOString().split('T')[0];
        document.getElementById('deliveryModal').classList.remove('hidden');
    }

    document.getElementById('deliveryForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const commandId = document.getElementById('d_command_id').value;
        const status = document.getElementById('d_status').value;
        const deliveryDate = document.getElementById('d_delivery_date').value;
        const notes = document.getElementById('d_notes').value;

        const btn = document.getElementById('btnSubmitDelivery');
        btn.innerHTML = 'Processing...'; btn.disabled = true;

        try {
            // Use the update pivot endpoint
            const res = await apiCall(`/suppliers/${supplierId}/commands/${commandId}`, 'PUT', {
                status: status,
                delivered_at: status === 'delivered' ? deliveryDate : null,
                shipped_at: status === 'confirmed' ? deliveryDate : null,
                notes: notes
            });

            if(res.status === 200) {
                showToast("Delivery status updated successfully!");
                document.getElementById('deliveryModal').classList.add('hidden');
                document.getElementById('deliveryForm').reset();
                loadOrders();
                loadSupplierData(); // Refresh stats
            } else {
                showToast("Failed to update delivery status", "error");
            }
        } catch (error) {
            showToast("Server error: " + error.message, "error");
        } finally {
            btn.innerHTML = 'Confirm'; btn.disabled = false;
        }
    });
</script>
@endpush
