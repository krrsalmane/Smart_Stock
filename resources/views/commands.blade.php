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

        <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="bg-brand-primary hover:bg-cyan-400 text-black font-semibold px-4 py-2 rounded-xl shadow-[0_0_15px_rgba(0,212,255,0.4)] transition-all hover:-translate-y-0.5 flex items-center">
            <i class="ph ph-plus-circle text-lg mr-2"></i> Create Order
        </button>
        
        <button onclick="exportCommandsReport()" class="bg-brand-secondary hover:bg-purple-600 text-white font-semibold px-4 py-2 rounded-xl shadow-[0_0_15px_rgba(123,47,247,0.4)] transition-all hover:-translate-y-0.5 flex items-center">
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
                        <label class="block text-sm font-medium text-gray-400 mb-1">Client ID *</label>
                        <input type="number" id="c_client_id" required class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary" placeholder="User ID">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Command Type</label>
                        <select id="c_command_type" class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary appearance-none">
                            <option value="purchase">Purchase</option>
                            <option value="return">Return</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Order Date *</label>
                        <input type="datetime-local" id="c_ordered_at" required class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary">
                    </div>
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
                                <select required class="product-select w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-primary appearance-none">
                                    <option value="">Select Product...</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <input type="number" min="1" required class="quantity-input w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-primary" placeholder="Qty">
                            </div>
                            <div class="col-span-2">
                                <input type="number" step="0.01" min="0" required class="price-input w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-primary" placeholder="Price">
                            </div>
                            <div class="col-span-1 flex items-center justify-center">
                                <button type="button" onclick="removeProductRow(this)" class="text-gray-500 hover:text-brand-danger transition-colors" title="Remove">
                                    <i class="ph ph-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <p class="text-xs text-gray-500 mt-2">Select product, enter quantity and unit price</p>
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
@endsection

@push('scripts')
<script>
    // Store products data globally
    let productsData = [];

    document.addEventListener('DOMContentLoaded', async () => {
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
                                <button onclick="updateStatus(${item.id})" title="Update Status" class="text-gray-500 hover:text-brand-primary transition-colors"><i class="ph ph-pencil"></i></button>
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
            const res = await apiCall('/products', 'GET');
            if(res.status === 200 && res.data) {
                productsData = res.data;
                updateAllProductDropdowns();
            }
        } catch (error) {
            console.error("Failed to load products", error);
        }
    }

    function updateAllProductDropdowns() {
        document.querySelectorAll('.product-select').forEach(select => {
            const currentValue = select.value;
            select.innerHTML = '<option value="">Select Product...</option>';
            productsData.forEach(p => {
                select.innerHTML += `<option value="${p.id}" class="bg-[#16162a] text-white">${p.name} (${p.sku}) - Stock: ${p.quantity}</option>`;
            });
            select.value = currentValue;
        });
    }

    // Add a new product row
    function addProductRow() {
        const container = document.getElementById('products-container');
        const newRow = document.createElement('div');
        newRow.className = 'product-row grid grid-cols-12 gap-2';
        newRow.innerHTML = `
            <div class="col-span-7">
                <select required class="product-select w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-primary appearance-none">
                    <option value="">Select Product...</option>
                </select>
            </div>
            <div class="col-span-2">
                <input type="number" min="1" required class="quantity-input w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-primary" placeholder="Qty">
            </div>
            <div class="col-span-2">
                <input type="number" step="0.01" min="0" required class="price-input w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-primary" placeholder="Price">
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
            client_id: parseInt(document.getElementById('c_client_id').value),
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
                            <select required class="product-select w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-primary appearance-none">
                                <option value="">Select Product...</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <input type="number" min="1" required class="quantity-input w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-primary" placeholder="Qty">
                        </div>
                        <div class="col-span-2">
                            <input type="number" step="0.01" min="0" required class="price-input w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-primary" placeholder="Price">
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
</script>
@endpush
