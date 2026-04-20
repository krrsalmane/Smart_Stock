@extends('layouts.app')

@section('page_title', 'Supplier Management')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-white flex items-center">
                Supplier Directory
                <span class="ml-3 px-2 py-0.5 rounded-md text-xs font-semibold bg-brand-primary/20 text-brand-primary border border-brand-primary/30" id="total_count">0 Suppliers</span>
            </h2>
            <p class="text-gray-400 mt-1">Manage your vendor and supplier relationships.</p>
        </div>

        <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="bg-brand-primary hover:bg-cyan-400 text-black font-semibold px-4 py-2 rounded-xl shadow-[0_0_15px_rgba(0,212,255,0.4)] transition-all hover:-translate-y-0.5 flex items-center">
            <i class="ph ph-plus-circle text-lg mr-2"></i> Add Supplier
        </button>
    </div>

    <!-- Data Table -->
    <div class="glass-panel overflow-hidden w-full relative z-10">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="text-xs text-gray-400 uppercase bg-black/30 border-b border-white/5">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider w-16">ID</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Supplier Name</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Contact Email</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Phone</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Products</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body" class="divide-y divide-white/5 text-gray-300">
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="ph ph-spinner-gap animate-spin text-3xl mx-auto mb-2 text-brand-primary"></i>
                            <p>Loading suppliers...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- CREATE MODAL -->
    <div id="createModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('createModal').classList.add('hidden')"></div>
        <div class="glass-panel w-full max-w-xl relative z-10 p-6 rounded-2xl border-t border-white/20">
            <div class="flex justify-between items-center mb-6 border-b border-white/10 pb-4">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="ph ph-truck text-brand-primary mr-2"></i> New Supplier
                </h3>
                <button onclick="document.getElementById('createModal').classList.add('hidden')" class="text-gray-400 hover:text-white transition-colors">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>

            <form id="createForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Company Name *</label>
                    <input type="text" id="s_name" required class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary" placeholder="e.g. Acme Corp">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Email Address *</label>
                        <input type="email" id="s_email" required class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary" placeholder="contact@company.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Phone Number</label>
                        <input type="text" id="s_phone" class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary" placeholder="+1 234 567 8900">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Physical Address</label>
                    <textarea id="s_address" rows="2" class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary" placeholder="Full address"></textarea>
                </div>

                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-white/10">
                    <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-white/10 text-gray-300 hover:bg-white/5 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="btnSubmit" class="bg-brand-primary hover:bg-cyan-400 text-black font-semibold px-6 py-2 rounded-lg shadow-[0_0_15px_rgba(0,212,255,0.4)] transition-all">
                        Register Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        await loadTable();
    });

    async function loadTable() {
        const tBody = document.getElementById('table-body');
        try {
            const response = await apiCall('/suppliers', 'GET');
            if(response.status === 200) {
                const items = response.data;
                document.getElementById('total_count').innerText = items.length + ' Suppliers';
                
                tBody.innerHTML = ''; 
                if(items.length === 0) {
                    tBody.innerHTML = `<tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No suppliers configured.</td></tr>`;
                    return;
                }

                items.forEach(item => {
                    const productCount = item.products ? item.products.length : 0;
                    
                    const row = `
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4 font-mono text-gray-500">#${item.id}</td>
                            <td class="px-6 py-4 font-medium text-white group-hover:text-brand-primary transition-colors">
                                <i class="ph ph-buildings text-gray-500 mr-2"></i> ${item.name}
                            </td>
                            <td class="px-6 py-4 text-gray-400">
                                <i class="ph ph-envelope-simple mr-1"></i> ${item.email}
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-sm font-mono">
                                ${item.phone || '-'}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-brand-primary/10 text-brand-primary border border-brand-primary/20">
                                    ${productCount} ${productCount === 1 ? 'Product' : 'Products'}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-lg space-x-2">
                                <button onclick="deleteItem(${item.id})" title="Delete Supplier" class="text-gray-500 hover:text-brand-danger transition-colors"><i class="ph ph-trash"></i></button>
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

    document.getElementById('createForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const payload = { 
            name: document.getElementById('s_name').value,
            email: document.getElementById('s_email').value,
            phone: document.getElementById('s_phone').value,
            address: document.getElementById('s_address').value
        };
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = 'Saving...'; btn.disabled = true;

        try {
            const res = await apiCall('/suppliers', 'POST', payload);
            if(res.status === 201) {
                showToast("Supplier registered successfully!");
                document.getElementById('createModal').classList.add('hidden');
                document.getElementById('createForm').reset();
                loadTable();
            } else {
                showToast(res.data.message || "Failed to create supplier", "error");
            }
        } catch (error) {
            showToast("Server error", "error");
        } finally {
            btn.innerHTML = 'Register Supplier'; btn.disabled = false;
        }
    });

    async function deleteItem(id) {
        if(!confirm("Are you sure you want to delete this supplier?")) return;
        try {
            const res = await apiCall(`/suppliers/${id}`, 'DELETE');
            if(res.status === 200 || res.status === 204) {
                showToast("Supplier deleted!");
                loadTable();
            } else {
                showToast("Could not delete supplier", "error");
            }
        } catch (error) {
            showToast("Error deleting supplier", "error");
        }
    }
</script>
@endpush
