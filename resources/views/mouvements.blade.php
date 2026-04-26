@extends('layouts.app')

@section('page_title', 'Stock Movements')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-white flex items-center">
                Movement Log
                <span class="ml-3 px-2 py-0.5 rounded-md text-xs font-semibold bg-brand-primary/20 text-brand-primary border border-brand-primary/30" id="total_count">0 Logs</span>
            </h2>
            <p class="text-gray-400 mt-1">Audit trail for all incoming and outgoing stock.</p>
        </div>

        <button id="btnRegisterMovement" onclick="document.getElementById('createModal').classList.remove('hidden')" class="hidden bg-brand-primary hover:bg-cyan-400 text-black font-semibold px-4 py-2 rounded-xl shadow-[0_0_15px_rgba(0,212,255,0.4)] transition-all hover:-translate-y-0.5 flex items-center">
            <i class="ph ph-arrows-left-right text-lg mr-2"></i> Register Movement
        </button>
        
        <button id="btnExport" onclick="exportMovementsReport()" class="hidden bg-brand-secondary hover:bg-purple-600 text-white font-semibold px-4 py-2 rounded-xl shadow-[0_0_15px_rgba(123,47,247,0.4)] transition-all hover:-translate-y-0.5 flex items-center">
            <i class="ph ph-download-simple text-lg mr-2"></i> Export Report
        </button>
    </div>

    <!-- Data Table -->
    <div class="glass-panel overflow-hidden w-full relative z-10">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="text-xs text-gray-400 uppercase bg-black/30 border-b border-white/5">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider w-24">Type</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Product (ID)</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Quantity</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Operator</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Notes</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-right">Timestamp</th>
                    </tr>
                </thead>
                <tbody id="table-body" class="divide-y divide-white/5 text-gray-300">
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="ph ph-spinner-gap animate-spin text-3xl mx-auto mb-2 text-brand-primary"></i>
                            <p>Loading audit trail...</p>
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
                    <i class="ph ph-arrows-left-right text-brand-primary mr-2"></i> Register Movement
                </h3>
                <button onclick="document.getElementById('createModal').classList.add('hidden')" class="text-gray-400 hover:text-white transition-colors">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>

            <form id="createForm" class="space-y-4">
                
                <div class="grid grid-cols-2 gap-4">
                    <!-- Type Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Direction *</label>
                        <select id="m_type" required class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary appearance-none">
                            <option value="IN" class="bg-[#16162a] text-brand-success">IN (Restock)</option>
                            <option value="OUT" class="bg-[#16162a] text-brand-secondary">OUT (Dispatch)</option>
                            <option value="ADJ" class="bg-[#16162a] text-brand-warning">ADJ (Adjustment)</option>
                        </select>
                    </div>
                    
                    <!-- Quantity -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Quantity *</label>
                        <input type="number" id="m_quantity" required min="1" class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary">
                    </div>
                </div>

                <!-- New Quantity (Only for ADJ type) -->
                <div id="adj-field" class="hidden">
                    <label class="block text-sm font-medium text-gray-400 mb-1">New Stock Quantity *</label>
                    <input type="number" id="m_new_quantity" min="0" class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary" placeholder="Enter the correct stock quantity">
                    <p class="text-xs text-gray-500 mt-1">For adjustments: set the actual stock count</p>
                </div>

                <!-- Product Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Target Product *</label>
                    <select id="m_product_id" required class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary appearance-none">
                        <option value="">Loading products...</option>
                    </select>
                </div>

                <!-- Note -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Operation Note</label>
                    <textarea id="m_note" rows="2" class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary" placeholder="Reason for movement..."></textarea>
                </div>

                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-white/10">
                    <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-white/10 text-gray-300 hover:bg-white/5 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="btnSubmit" class="bg-brand-primary hover:bg-cyan-400 text-black font-semibold px-6 py-2 rounded-lg shadow-[0_0_15px_rgba(0,212,255,0.4)] transition-all">
                        Execute
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
            document.getElementById('btnRegisterMovement')?.classList.remove('hidden');
            document.getElementById('btnExport')?.classList.remove('hidden');
        }

        await loadTable();
        await loadProductsDropdown();
        
        // Show/hide ADJ field based on movement type
        document.getElementById('m_type').addEventListener('change', function() {
            const adjField = document.getElementById('adj-field');
            if (this.value === 'ADJ') {
                adjField.classList.remove('hidden');
                document.getElementById('m_new_quantity').required = true;
            } else {
                adjField.classList.add('hidden');
                document.getElementById('m_new_quantity').required = false;
            }
        });
    });

    async function loadTable() {
        const tBody = document.getElementById('table-body');
        try {
            const response = await apiCall('/mouvements', 'GET');
            if(response.status === 200) {
                const items = response.data;
                document.getElementById('total_count').innerText = items.length + ' Logs';
                
                tBody.innerHTML = ''; 
                if(items.length === 0) {
                    tBody.innerHTML = `<tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No movements recorded yet.</td></tr>`;
                    return;
                }

                items.forEach(item => {
                    const isIN = item.type === 'IN';
                    const isADJ = item.type === 'ADJ';
                    const badgeColor = isIN ? 'bg-brand-success/20 text-brand-success border-brand-success/30' : 
                                      (isADJ ? 'bg-brand-warning/20 text-brand-warning border-brand-warning/30' : 'bg-brand-secondary/20 text-brand-secondary border-brand-secondary/30');
                    const icon = isIN ? 'ph-arrow-down-left' : (isADJ ? 'ph-sliders-horizontal' : 'ph-arrow-up-right');
                    const sign = isIN ? '+' : (isADJ ? '→' : '-');
                    const dateObj = new Date(item.created_at);

                    const row = `
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md border ${badgeColor} flex items-center w-max">
                                    <i class="ph ${icon} mr-1 text-sm"></i> ${item.type}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-white">
                                ${item.product ? item.product.name : 'Unknown Product'} 
                                <span class="text-xs font-mono text-gray-500 ml-1">(ID: ${item.product_id})</span>
                            </td>
                            <td class="px-6 py-4 font-mono font-medium ${isIN ? 'text-brand-success' : 'text-brand-secondary'}">
                                ${sign}${item.quantity}
                            </td>
                            <td class="px-6 py-4 flex items-center">
                                <div class="w-6 h-6 rounded-full bg-gray-700 flex items-center justify-center mr-2 text-[10px] text-white">
                                    ${item.user ? item.user.name.charAt(0) : '?'}
                                </div>
                                ${item.user ? item.user.name : 'System'}
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-sm truncate max-w-[200px]" title="${item.note || '-'}">
                                ${item.note || '-'}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500 text-right">
                                ${dateObj.toLocaleDateString()} ${dateObj.toLocaleTimeString()}
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

    async function loadProductsDropdown() {
        const prodSelect = document.getElementById('m_product_id');
        try {
            const res = await apiCall('/products', 'GET');
            if(res.status === 200 && res.data) {
                prodSelect.innerHTML = '<option value="" disabled selected>-- Choose Product --</option>';
                res.data.forEach(p => {
                    prodSelect.innerHTML += `<option value="${p.id}" class="bg-[#16162a] text-white">${p.name} (${p.sku} | In Stock: ${p.quantity})</option>`;
                });
            }
        } catch (error) {
            console.error("Failed to load dropdowns");
        }
    }

    document.getElementById('createForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const payload = { 
            type: document.getElementById('m_type').value,
            quantity: parseInt(document.getElementById('m_quantity').value),
            product_id: parseInt(document.getElementById('m_product_id').value),
            note: document.getElementById('m_note').value,
            user_id: parseInt(localStorage.getItem('user_id') || 1) // Will be set from auth
        };
        
        // Add new_quantity for ADJ type
        if (payload.type === 'ADJ') {
            const newQty = document.getElementById('m_new_quantity').value;
            if (!newQty) {
                showToast("Please enter the new stock quantity for adjustment", "error");
                return;
            }
            payload.new_quantity = parseInt(newQty);
        }
        
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = 'Executing...'; btn.disabled = true;

        try {
            const res = await apiCall('/mouvements', 'POST', payload);
            if(res.status === 201) {
                showToast("Movement executed successfully!");
                document.getElementById('createModal').classList.add('hidden');
                document.getElementById('createForm').reset();
                document.getElementById('adj-field').classList.add('hidden');
                loadTable();
                loadProductsDropdown(); // Refresh stock counts in dropdown
            } else {
                // E.g., not enough stock for OUT
                showToast(res.data.message || res.data.error || "Failed to register movement.", "error");
            }
        } catch (error) {
            showToast("Server error: " + error.message, "error");
        } finally {
            btn.innerHTML = 'Execute'; btn.disabled = false;
        }
    });

    // Export Report Function
    function exportMovementsReport() {
        const token = localStorage.getItem('smartstock_token');
        window.open(`/api/reports/export/movements?token=${token}`, '_blank');
        showToast('Movements report exporting...', 'success');
    }
</script>
@endpush
