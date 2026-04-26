@extends('layouts.app')

@section('page_title', 'Order Management')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-white flex items-center">
                Purchase Orders
                <span class="ml-3 px-2 py-0.5 rounded-md text-xs font-semibold bg-brand-primary/20 text-brand-primary border border-brand-primary/30" id="total_count">0 Orders</span>
            </h2>
            <p class="text-gray-400 mt-1">Track and manage all incoming purchase orders.</p>
        </div>

        <button id="btnCreateOrder" onclick="document.getElementById('createModal').classList.remove('hidden')" class="hidden bg-brand-primary hover:bg-cyan-400 text-black font-semibold px-4 py-2 rounded-xl shadow-[0_0_15px_rgba(0,212,255,0.4)] transition-all hover:-translate-y-0.5 flex items-center">
            <i class="ph ph-plus-circle text-lg mr-2"></i> Create Order
        </button>
        
        <button id="btnExport" onclick="exportCommandsReport()" class="hidden bg-brand-secondary hover:bg-purple-600 text-white font-semibold px-4 py-2 rounded-xl shadow-[0_0_15px_rgba(123,47,247,0.4)] transition-all hover:-translate-y-0.5 flex items-center">
            <i class="ph ph-download-simple text-lg mr-2"></i> Export Report
        </button>
    </div>

    <!-- Data Table -->
    <div class="glass-panel overflow-hidden w-full relative z-10">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="text-xs text-gray-400 uppercase bg-black/30 border-b border-white/5">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider w-16">ID</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Client</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-right">Total Cost</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Order Date</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Expected Delivery</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body" class="divide-y divide-white/5 text-gray-300">
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="ph ph-spinner-gap animate-spin text-3xl mx-auto mb-2 text-brand-primary"></i>
                            <p>Loading orders...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- CREATE MODAL -->
    <div id="createModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('createModal').classList.add('hidden')"></div>
        <div class="glass-panel w-full max-w-2xl relative z-10 p-6 rounded-2xl border-t border-white/20">
            <div class="flex justify-between items-center mb-6 border-b border-white/10 pb-4">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="ph ph-shopping-cart text-brand-primary mr-2"></i> New Purchase Order
                </h3>
                <button onclick="document.getElementById('createModal').classList.add('hidden')" class="text-gray-400 hover:text-white transition-colors">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>

            <form id="createForm" class="space-y-4">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Command Type</label>
                        <select id="c_command_type" class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary appearance-none">
                            <option value="purchase">Purchase</option>
                            <option value="return">Return</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Order Date *</label>
                        <input type="datetime-local" id="c_ordered_at" required class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Expected Delivery</label>
                        <input type="date" id="c_expected_at" class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary">
                    </div>
                </div>

                <div class="border-t border-white/10 pt-4">
                    <div class="flex justify-between items-center mb-3">
                        <label class="block text-sm font-medium text-gray-400">Products *</label>
                        <button type="button" onclick="addProductRow()" class="text-xs bg-brand-primary/20 text-brand-primary px-3 py-1 rounded-lg hover:bg-brand-primary/30 transition-colors">
                            <i class="ph ph-plus mr-1"></i> Add Product
                        </button>
                    </div>
                    
                    <div id="products-container" class="space-y-2">
                        <!-- Product rows will be added here dynamically -->
                        <div class="product-row grid grid-cols-12 gap-2">
                            <div class="col-span-7">
                                <select required onchange="onProductSelect(this)" class="product-select w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-primary appearance-none">
                                    <option value="">Select Product...</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <input type="number" min="1" required class="quantity-input w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-primary" placeholder="Qty">
                            </div>
                            <div class="col-span-2">
                                <input type="number" step="0.01" min="0" required readonly class="price-input w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-gray-400 text-sm focus:outline-none cursor-not-allowed" placeholder="Auto">
                            </div>
                            <div class="col-span-1 flex items-center justify-center">
                                <button type="button" onclick="removeProductRow(this)" class="text-gray-500 hover:text-brand-danger transition-colors" title="Remove">
                                    <i class="ph ph-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <p class="text-xs text-gray-500 mt-2">Select a product and enter quantity — price is filled automatically</p>
                </div>

                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-white/10">
                    <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-white/10 text-gray-300 hover:bg-white/5 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="btnSubmit" class="bg-brand-primary hover:bg-cyan-400 text-black font-semibold px-6 py-2 rounded-lg shadow-[0_0_15px_rgba(0,212,255,0.4)] transition-all">
                        Create Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ASSIGN DELIVERY AGENT MODAL -->
    <div id="assignModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('assignModal').classList.add('hidden')"></div>
        <div class="glass-panel w-full max-w-md relative z-10 p-6 rounded-2xl border-t border-white/20">
            <div class="flex justify-between items-center mb-6 border-b border-white/10 pb-4">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="ph ph-truck text-brand-warning mr-2"></i> Assign Delivery Agent
                </h3>
                <button onclick="document.getElementById('assignModal').classList.add('hidden')" class="text-gray-400 hover:text-white transition-colors">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>

            <form id="assignForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Select Delivery Agent *</label>
                    <select id="delivery_agent_select" required class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-brand-warning appearance-none">
                        <option value="">Loading agents...</option>
                    </select>
                </div>

                <div class="border-t border-white/10 pt-4 flex gap-3">
                    <button type="button" onclick="document.getElementById('assignModal').classList.add('hidden')" class="flex-1 bg-white/5 hover:bg-white/10 text-gray-300 font-semibold px-4 py-3 rounded-xl transition-all">
                        Cancel
                    </button>
                    <button type="submit" id="assignBtn" class="flex-1 bg-brand-warning hover:bg-yellow-500 text-black font-semibold px-4 py-3 rounded-xl shadow-[0_0_15px_rgba(245,158,11,0.4)] transition-all hover:-translate-y-0.5">
                        Assign Agent
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Store products data globally
    let productsData = [];
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

        if (userRole === 'admin' || userRole === 'magasinier' || userRole === 'client') {
            document.getElementById('btnCreateOrder')?.classList.remove('hidden');
        }
        
        if (userRole === 'admin' || userRole === 'magasinier') {
            document.getElementById('btnExport')?.classList.remove('hidden');
        }

        await loadTable();
        await loadProductsDropdown();
    });

    async function loadTable() {
        const tBody = document.getElementById('table-body');
        try {
            const response = await apiCall('/commands', 'GET');
            if(response.status === 200) {
                const items = response.data;
                document.getElementById('total_count').innerText = items.length + ' Orders';
                
                tBody.innerHTML = ''; 
                if(items.length === 0) {
                    tBody.innerHTML = `<tr><td colspan="8" class="px-6 py-8 text-center text-gray-500">No orders found.</td></tr>`;
                    return;
                }

                items.forEach(item => {
                    const statusColors = {
                        'pending': 'bg-brand-warning/20 text-brand-warning border-brand-warning/30',
                        'approved': 'bg-brand-primary/20 text-brand-primary border-brand-primary/30',
                        'received': 'bg-brand-success/20 text-brand-success border-brand-success/30',
                        'cancelled': 'bg-brand-danger/20 text-brand-danger border-brand-danger/30'
                    };
                    const badgeClass = statusColors[item.status] || 'bg-gray-500/20 text-gray-400 border-gray-500/30';
                    const dateObj = new Date(item.ordered_at);
                    const expectedObj = item.expected_at ? new Date(item.expected_at) : null;

                    const row = `
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4 font-mono text-gray-500">#${item.id}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded bg-brand-secondary/20 text-brand-secondary border border-brand-secondary/30">
                                    ${item.command_type || 'purchase'}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md border ${badgeClass}">
                                    ${item.status}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-white">
                                <i class="ph ph-user text-gray-500 mr-2"></i> ${item.client ? item.client.name : 'N/A'}
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-brand-primary font-medium">
                                $${parseFloat(item.total_cost).toFixed(2)}
                            </td>
                            <td class="px-6 py-4 text-center text-xs text-gray-400">
                                ${dateObj.toLocaleDateString()}
                            </td>
                            <td class="px-6 py-4 text-center text-xs text-gray-400">
                                ${expectedObj ? expectedObj.toLocaleDateString() : '-'}
                            </td>
                            <td class="px-6 py-4 text-center text-lg space-x-2">
                                ${(userRole === 'admin' || userRole === 'magasinier') ? `
                                    <button onclick="updateStatus(${item.id})" title="Update Status" class="text-gray-500 hover:text-brand-primary transition-colors"><i class="ph ph-pencil"></i></button>
                                    ${item.delivery_agent_id ? 
                                        `<span class="text-xs text-brand-success" title="Assigned to ${item.delivery_agent ? item.delivery_agent.name : 'Agent'}"><i class="ph ph-check-circle"></i></span>` :
                                        `<button onclick="openAssignModal(${item.id})" title="Assign Delivery Agent" class="text-gray-500 hover:text-brand-warning transition-colors"><i class="ph ph-truck"></i></button>`
                                    }
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

    // Load products into all dropdowns
    async function loadProductsDropdown() {
        try {
            console.log('Loading products...');
            const res = await apiCall('/products', 'GET');
            console.log('Products response:', res);
            if(res.status === 200 && res.data) {
                productsData = res.data;
                console.log('Products loaded:', productsData.length);
                updateAllProductDropdowns();
            } else {
                console.error('Failed to load products:', res);
            }
        } catch (error) {
            console.error("Failed to load products", error);
            showToast("Failed to load products - check console", "error");
        }
    }

    function updateAllProductDropdowns() {
        document.querySelectorAll('.product-select').forEach(select => {
            const currentValue = select.value;
            select.innerHTML = '<option value="">Select Product...</option>';
            productsData.forEach(p => {
                select.innerHTML += `<option value="${p.id}" data-price="${p.price}" class="bg-[#16162a] text-white">${p.name} (${p.sku}) - $${parseFloat(p.price).toFixed(2)}</option>`;
            });
            select.value = currentValue;
        });
    }

    // Auto-fill price when a product is selected
    function onProductSelect(selectEl) {
        const row = selectEl.closest('.product-row');
        const priceInput = row.querySelector('.price-input');
        const selectedOption = selectEl.options[selectEl.selectedIndex];
        
        if (selectEl.value && selectedOption.dataset.price) {
            priceInput.value = parseFloat(selectedOption.dataset.price).toFixed(2);
        } else {
            priceInput.value = '';
        }
    }

    // Add a new product row
    function addProductRow() {
        const container = document.getElementById('products-container');
        const newRow = document.createElement('div');
        newRow.className = 'product-row grid grid-cols-12 gap-2';
        newRow.innerHTML = `
            <div class="col-span-7">
                <select required onchange="onProductSelect(this)" class="product-select w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-primary appearance-none">
                    <option value="">Select Product...</option>
                </select>
            </div>
            <div class="col-span-2">
                <input type="number" min="1" required class="quantity-input w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-primary" placeholder="Qty">
            </div>
            <div class="col-span-2">
                <input type="number" step="0.01" min="0" required readonly class="price-input w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-gray-400 text-sm focus:outline-none cursor-not-allowed" placeholder="Auto">
            </div>
            <div class="col-span-1 flex items-center justify-center">
                <button type="button" onclick="removeProductRow(this)" class="text-gray-500 hover:text-brand-danger transition-colors" title="Remove">
                    <i class="ph ph-x"></i>
                </button>
            </div>
        `;
        container.appendChild(newRow);
        updateAllProductDropdowns();
    }

    // Remove a product row
    function removeProductRow(button) {
        const container = document.getElementById('products-container');
        if (container.children.length > 1) {
            button.closest('.product-row').remove();
        } else {
            showToast("You need at least one product", "error");
        }
    }

    async function updateStatus(id) {
        const newStatus = prompt("Enter new status (pending, approved, received, cancelled):");
        if (!newStatus) return;

        try {
            const res = await apiCall(`/commands/${id}`, 'PUT', { status: newStatus });
            if(res.status === 200) {
                showToast("Order updated!");
                loadTable();
            } else {
                showToast("Failed to update order", "error");
            }
        } catch (error) {
            showToast("Server error", "error");
        }
    }

    // Handle form submission
    document.getElementById('createForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Collect all product rows
        const productRows = document.querySelectorAll('.product-row');
        const products = [];
        
        for (const row of productRows) {
            const productId = row.querySelector('.product-select').value;
            const quantity = row.querySelector('.quantity-input').value;
            const unitPrice = row.querySelector('.price-input').value;
            
            if (productId && quantity && unitPrice) {
                products.push({
                    product_id: parseInt(productId),
                    quantity: parseInt(quantity),
                    unit_price: parseFloat(unitPrice)
                });
            }
        }

        if (products.length === 0) {
            showToast("Please add at least one product", "error");
            return;
        }

        const payload = { 
            ordered_at: document.getElementById('c_ordered_at').value,
            command_type: document.getElementById('c_command_type').value,
            expected_at: document.getElementById('c_expected_at').value || null,
            products: products
        };
        
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = 'Creating...'; btn.disabled = true;

        try {
            const res = await apiCall('/commands', 'POST', payload);
            if(res.status === 201) {
                showToast("Order created successfully!");
                document.getElementById('createModal').classList.add('hidden');
                document.getElementById('createForm').reset();
                // Reset to single product row
                document.getElementById('products-container').innerHTML = `
                    <div class="product-row grid grid-cols-12 gap-2">
                        <div class="col-span-7">
                            <select required onchange="onProductSelect(this)" class="product-select w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-primary appearance-none">
                                <option value="">Select Product...</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <input type="number" min="1" required class="quantity-input w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-primary" placeholder="Qty">
                        </div>
                        <div class="col-span-2">
                            <input type="number" step="0.01" min="0" required readonly class="price-input w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-gray-400 text-sm focus:outline-none cursor-not-allowed" placeholder="Auto">
                        </div>
                        <div class="col-span-1 flex items-center justify-center">
                            <button type="button" onclick="removeProductRow(this)" class="text-gray-500 hover:text-brand-danger transition-colors" title="Remove">
                                <i class="ph ph-x"></i>
                            </button>
                        </div>
                    </div>
                `;
                updateAllProductDropdowns();
                loadTable();
            } else {
                showToast(res.data.message || "Failed to create order", "error");
            }
        } catch (error) {
            showToast("Server error: " + error.message, "error");
        } finally {
            btn.innerHTML = 'Create Order'; btn.disabled = false;
        }
    });

    // Export Report Function
    function exportCommandsReport() {
        const token = localStorage.getItem('smartstock_token');
        window.open(`/api/reports/export/commands?token=${token}`, '_blank');
        showToast('Commands report exporting...', 'success');
    }

    // Assign Delivery Agent Modal
    let currentCommandId = null;

    function openAssignModal(commandId) {
        currentCommandId = commandId;
        document.getElementById('assignModal').classList.remove('hidden');
        loadDeliveryAgents();
    }

    async function loadDeliveryAgents() {
        const select = document.getElementById('delivery_agent_select');
        select.innerHTML = '<option value="">Loading agents...</option>';
        
        try {
            const res = await apiCall('/delivery-agents', 'GET');
            if(res.status === 200 && res.data.delivery_agents) {
                select.innerHTML = '<option value="">Select Delivery Agent...</option>';
                res.data.delivery_agents.forEach(agent => {
                    select.innerHTML += `<option value="${agent.id}">${agent.name} (${agent.email})</option>`;
                });
            }
        } catch (error) {
            select.innerHTML = '<option value="">Failed to load agents</option>';
            showToast('Failed to load delivery agents', 'error');
        }
    }

    document.getElementById('assignForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('assignBtn');
        const agentId = document.getElementById('delivery_agent_select').value;

        if (!agentId) {
            showToast('Please select a delivery agent', 'error');
            return;
        }

        btn.innerHTML = '<i class="ph ph-spinner-gap animate-spin mr-2"></i> Assigning...';
        btn.disabled = true;

        try {
            const res = await apiCall(`/commands/${currentCommandId}/assign-delivery-agent`, 'POST', {
                delivery_agent_id: agentId
            });

            if(res.status === 200 || res.status === 201) {
                showToast('Delivery agent assigned successfully!', 'success');
                document.getElementById('assignModal').classList.add('hidden');
                loadTable();
            } else {
                showToast(res.data.error || 'Failed to assign agent', 'error');
            }
        } catch (error) {
            showToast('Server error: ' + error.message, 'error');
        } finally {
            btn.innerHTML = 'Assign Agent';
            btn.disabled = false;
        }
    });
</script>
@endpush
