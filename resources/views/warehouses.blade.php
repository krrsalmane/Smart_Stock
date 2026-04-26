@extends('layouts.app')

@section('page_title', 'Warehouse Locations')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-white flex items-center">
                Storage Facilities
                <span class="ml-3 px-2 py-0.5 rounded-md text-xs font-semibold bg-brand-primary/20 text-brand-primary border border-brand-primary/30" id="total_count">0 Warehouses</span>
            </h2>
            <p class="text-gray-400 mt-1">Manage physical locations where inventory is stored.</p>
        </div>

        <button id="btnRegisterWarehouse" onclick="document.getElementById('createModal').classList.remove('hidden')" class="hidden bg-brand-primary hover:bg-cyan-400 text-black font-semibold px-4 py-2 rounded-xl shadow-[0_0_15px_rgba(0,212,255,0.4)] transition-all hover:-translate-y-0.5 flex items-center">
            <i class="ph ph-plus-circle text-lg mr-2"></i> Register Warehouse
        </button>
    </div>

    <!-- Data Table -->
    <div class="glass-panel overflow-hidden w-full relative z-10">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-400 uppercase bg-black/30 border-b border-white/5">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider w-16">ID</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Facility Name</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Physical Address</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Capacity & Usage</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body" class="divide-y divide-white/5 text-gray-300">
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="ph ph-spinner-gap animate-spin text-3xl mx-auto mb-2 text-brand-primary"></i>
                            <p>Loading facilities...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- CREATE MODAL -->
    <div id="createModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('createModal').classList.add('hidden')"></div>
        <div class="glass-panel w-full max-w-md relative z-10 p-6 rounded-2xl border-t border-white/20">
            <div class="flex justify-between items-center mb-6 border-b border-white/10 pb-4">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="ph ph-buildings text-brand-primary mr-2"></i> New Warehouse
                </h3>
                <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="text-gray-400 hover:text-white transition-colors">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>

            <form id="createForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Facility Name *</label>
                    <input type="text" id="w_name" required class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary" placeholder="e.g. Alpha Hub">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Physical Address *</label>
                    <textarea id="w_address" required rows="2" class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary" placeholder="Full physical address"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Max Capacity (Quantity of Items)</label>
                    <input type="number" min="1" id="w_capacity" class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary" placeholder="Leave empty for unlimited">
                </div>

                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-white/10">
                    <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-white/10 text-gray-300 hover:bg-white/5 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="btnSubmit" class="bg-brand-primary hover:bg-cyan-400 text-black font-semibold px-6 py-2 rounded-lg shadow-[0_0_15px_rgba(0,212,255,0.4)] transition-all">
                        Register
                    </button>
                </div>
            </form>
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

        if (userRole === 'admin' || userRole === 'magasinier') {
            document.getElementById('btnRegisterWarehouse')?.classList.remove('hidden');
        }

        await loadTable();
    });

    async function loadTable() {
        const tBody = document.getElementById('table-body');
        try {
            const response = await apiCall('/warehouses', 'GET');
            if(response.status === 200) {
                const items = response.data;
                document.getElementById('total_count').innerText = items.length + ' Warehouses';
                
                tBody.innerHTML = ''; 
                if(items.length === 0) {
                    tBody.innerHTML = `<tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No warehouses configured.</td></tr>`;
                    return;
                }

                items.forEach(item => {
                    const row = `
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4 font-mono text-gray-500">#${item.id}</td>
                            <td class="px-6 py-4 font-medium text-white group-hover:text-brand-primary transition-colors">
                                <i class="ph ph-buildings text-gray-500 mr-2"></i> ${item.name}
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-sm">
                                <i class="ph ph-map-pin mr-1"></i> ${item.address}
                            </td>
                            <td class="px-6 py-4 w-48">
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="text-gray-400">Usage</span>
                                    <span class="font-bold ${item.is_full ? 'text-brand-danger' : 'text-brand-primary'}">${item.usage_percent}%</span>
                                </div>
                                <div class="w-full bg-black/50 rounded-full h-1.5 overflow-hidden border border-white/5">
                                    <div class="${item.is_full ? 'bg-brand-danger shadow-[0_0_8px_rgba(239,68,68,0.6)]' : 'bg-brand-primary shadow-[0_0_8px_rgba(0,212,255,0.6)]'} h-1.5 rounded-full transition-all" style="width: ${item.usage_percent}%"></div>
                                </div>
                                <p class="text-[10px] text-gray-500 mt-1">${item.current_stock} / ${item.capacity || '∞'} Items ${item.is_full ? '<span class="text-brand-danger font-bold uppercase ml-1">FULL</span>' : ''}</p>
                            </td>
                            <td class="px-6 py-4 text-right text-lg space-x-2">
                                ${(userRole === 'admin' || userRole === 'magasinier') ? `
                                    <button onclick="deleteItem(${item.id})" class="text-gray-500 hover:text-brand-danger transition-colors"><i class="ph ph-trash"></i></button>
                                ` : '<span class="text-xs text-gray-500">-</span>'}
                            </td>
                        </tr>
                    `;
                    tBody.insertAdjacentHTML('beforeend', row);
                });
            }
        } catch (error) {
            tBody.innerHTML = `<tr><td colspan="4" class="px-6 py-8 text-center text-brand-danger">API Error</td></tr>`;
        }
    }

    document.getElementById('createForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const capacityVal = document.getElementById('w_capacity').value;
        const payload = { 
            name: document.getElementById('w_name').value,
            address: document.getElementById('w_address').value,
            capacity: capacityVal ? parseInt(capacityVal) : null
        };
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = 'Saving...'; btn.disabled = true;

        try {
            const res = await apiCall('/warehouses', 'POST', payload);
            if(res.status === 201) {
                showToast("Warehouse registered!");
                document.getElementById('createModal').classList.add('hidden');
                document.getElementById('createForm').reset();
                loadTable();
            } else {
                showToast("Failed to create", "error");
            }
        } catch (error) {
            showToast("Server error", "error");
        } finally {
            btn.innerHTML = 'Register'; btn.disabled = false;
        }
    });

    async function deleteItem(id) {
        if(!confirm("Are you sure? Will fail if products are stored here.")) return;
        try {
            const res = await apiCall(`/warehouses/${id}`, 'DELETE');
            if(res.status === 200 || res.status === 204) {
                showToast("Warehouse deleted!");
                loadTable();
            } else {
                showToast("Could not delete. Empty the warehouse first.", "error");
            }
        } catch (error) {
            showToast("Error", "error");
        }
    }
</script>
@endpush
