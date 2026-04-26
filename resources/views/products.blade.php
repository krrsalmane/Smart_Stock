@extends('layouts.app')

@section('page_title', 'Product Inventory')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-white flex items-center">
                Inventory Database
                <span class="ml-3 px-2 py-0.5 rounded-md text-xs font-semibold bg-brand-primary/20 text-brand-primary border border-brand-primary/30" id="total_products_count">0 Items</span>
            </h2>
            <p class="text-gray-400 mt-1">Manage and monitor all warehouse stock items.</p>
        </div>

        <button id="btnAddProduct" onclick="document.getElementById('productModal').classList.remove('hidden')" class="hidden bg-brand-primary hover:bg-cyan-400 text-black font-semibold px-4 py-2 rounded-xl shadow-[0_0_15px_rgba(0,212,255,0.4)] transition-all hover:-translate-y-0.5 flex items-center">
            <i class="ph ph-plus-circle text-lg mr-2"></i> Add Product
        </button>
        
        <button id="btnExportReport" onclick="exportProductsReport()" class="hidden bg-brand-secondary hover:bg-purple-600 text-white font-semibold px-4 py-2 rounded-xl shadow-[0_0_15px_rgba(123,47,247,0.4)] transition-all hover:-translate-y-0.5 flex items-center">
            <i class="ph ph-download-simple text-lg mr-2"></i> Export Report
        </button>
    </div>

    <!-- The Glassmorphic Data Table -->
    <div class="glass-panel overflow-hidden w-full relative z-10">
        <!-- Search and Filters Bar -->
        <div class="p-4 border-b border-white/10 flex items-center justify-between bg-black/10">
            <div class="relative w-72">
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                <input type="text" id="searchInput" placeholder="Search by SKU or Name..." class="w-full bg-black/40 border border-white/5 rounded-lg pl-10 pr-4 py-2 text-sm text-white focus:outline-none focus:border-brand-primary transition-colors">
            </div>
        </div>

        <!-- The Table -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="text-xs text-gray-400 uppercase bg-black/30 border-b border-white/5">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">SKU & Product</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Category</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-right">Unit Price</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider w-64">Stock Level</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Warehouse</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="products-table-body" class="divide-y divide-white/5 text-gray-300">
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="ph ph-spinner-gap animate-spin text-3xl mx-auto mb-2 text-brand-primary"></i>
                            <p>Loading inventory data...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- PRODUCT MODAL (Hidden by default)          -->
    <!-- ========================================== -->
    <div id="productModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Blurred background overlay -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('productModal').classList.add('hidden')"></div>
        
        <!-- Modal Content -->
        <div class="glass-panel w-full max-w-xl relative z-10 p-6 rounded-2xl border-t border-white/20">
            <div class="flex justify-between items-center mb-6 border-b border-white/10 pb-4">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="ph ph-archive text-brand-primary mr-2"></i> Register New Product
                </h3>
                <button onclick="document.getElementById('productModal').classList.add('hidden')" class="text-gray-400 hover:text-white transition-colors">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>

            <form id="createProductForm" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Name -->
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-400 mb-1">Product Name *</label>
                        <input type="text" id="p_name" required class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary">
                    </div>
                    <!-- SKU -->
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-400 mb-1">SKU *</label>
                        <input type="text" id="p_sku" required class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <!-- Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Price ($) *</label>
                        <input type="number" step="0.01" id="p_price" required class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary">
                    </div>
                    <!-- Quantity -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Init Quantity *</label>
                        <input type="number" id="p_quantity" required class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary">
                    </div>
                    <!-- Alert Threshold -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Alert Trigger *</label>
                        <input type="number" id="p_threshold" required class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary">
                    </div>
                </div>

                <!-- Category Dropdown (Javascript populates this) -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Category *</label>
                    <select id="p_category" required class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary appearance-none">
                        <option value="">Loading categories...</option>
                    </select>
                </div>

                <!-- Warehouse Dropdown (Javascript populates this) -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Warehouse Location *</label>
                    <select id="p_warehouse" required class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary appearance-none">
                        <option value="">Loading warehouses...</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-white/10">
                    <button type="button" onclick="document.getElementById('productModal').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-white/10 text-gray-300 hover:bg-white/5 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="btnSubmitProduct" class="bg-brand-primary hover:bg-cyan-400 text-black font-semibold px-6 py-2 rounded-lg shadow-[0_0_15px_rgba(0,212,255,0.4)] transition-all flex items-center">
                        Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Global variable to hold our products so we can search/delete easily
    let allProducts = [];
    let userRole = '';

    // --- 1. Load Data on Startup ---
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const userRes = await apiCall('/user', 'GET');
            if (userRes.status === 200) {
                userRole = userRes.data.role;
            }
        } catch (error) {
            console.error("Failed to fetch user role", error);
        }

        // Suppliers and delivery agents cannot access the products page
        if (userRole === 'supplier') {
            window.location.href = '/supplier-portal';
            return;
        }
        if (userRole === 'delivery_agent') {
            window.location.href = '/delivery-agent/dashboard';
            return;
        }

        if (userRole === 'admin' || userRole === 'magasinier') {
            document.getElementById('btnAddProduct')?.classList.remove('hidden');
            document.getElementById('btnExportReport')?.classList.remove('hidden');
        }

        await loadProductsTable();
        await loadDropdowns();
    });

    // --- 2. Fetch & Render Table ---
    async function loadProductsTable() {
        const tBody = document.getElementById('products-table-body');
        try {
            const response = await apiCall('/products', 'GET');
            if(response.status === 200) {
                allProducts = response.data;
                document.getElementById('total_products_count').innerText = allProducts.length + ' Items';
                renderTable(allProducts);
            }
        } catch (error) {
            tBody.innerHTML = `<tr><td colspan="6" class="px-6 py-8 text-center text-brand-danger">Failed to load API data.</td></tr>`;
        }
    }

    function renderTable(products) {
        const tBody = document.getElementById('products-table-body');
        tBody.innerHTML = ''; 

        if(!products || products.length === 0) {
            tBody.innerHTML = `<tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No products found. Add one above!</td></tr>`;
            return;
        }

        products.forEach(product => {
            let stockStatusColor = 'bg-brand-success';
            let badgeClass = 'text-green-400 border-green-400/20 bg-green-400/10';
            let statusLabel = 'Optimal';
            
            if (product.quantity <= product.alert_threshold) {
                stockStatusColor = 'bg-brand-danger shadow-[0_0_8px_rgba(239,68,68,0.8)]'; // Glowing Red
                badgeClass = 'text-brand-danger border-brand-danger/20 bg-brand-danger/10';
                statusLabel = 'Low Stock';
            } else if (product.quantity <= (product.alert_threshold * 1.5)) {
                stockStatusColor = 'bg-brand-warning'; // Yellow
                badgeClass = 'text-brand-warning border-brand-warning/20 bg-brand-warning/10';
                statusLabel = 'Low';
            }

            const maxCap = Math.max(200, product.quantity * 1.2); 
            const percent = Math.min(100, Math.round((product.quantity / maxCap) * 100));

            const row = `
                <tr class="hover:bg-white/5 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 rounded-lg bg-black/40 border border-white/10 flex items-center justify-center shadow-inner">
                                <i class="ph ph-package text-gray-400 text-xl group-hover:text-brand-primary transition-colors"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-white">${product.name}</div>
                                <div class="text-xs text-brand-primary font-mono mt-0.5">${product.sku}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-white/5 text-gray-300 border border-white/10">
                            ${product.category ? product.category.name : '-'}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right font-mono text-gray-300">
                        $${parseFloat(product.price).toFixed(2)}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-white">${product.quantity} Units</span>
                            <span class="${badgeClass} px-1.5 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider">${statusLabel}</span>
                        </div>
                        <div class="w-full bg-black/50 rounded-full h-1.5 border border-white/5 overflow-hidden">
                            <div class="${stockStatusColor} h-1.5 rounded-full transition-all duration-1000 ease-out" style="width: ${percent}%"></div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center text-xs text-gray-400">
                        <i class="ph ph-buildings mr-1"></i> ${product.warehouse ? product.warehouse.name : '-'}
                    </td>
                    <td class="px-6 py-4 text-center text-lg space-x-2">
                        ${(userRole === 'admin' || userRole === 'magasinier') ? 
                            `<button onclick="deleteProduct(${product.id})" title="Delete Product" class="text-gray-500 hover:text-brand-danger transition-colors"><i class="ph ph-trash"></i></button>` : 
                            '<span class="text-xs text-gray-500">-</span>'
                        }
                    </td>
                </tr>
            `;
            tBody.insertAdjacentHTML('beforeend', row);
        });
    }

    // --- 3. Fetch Data for Dropdowns ---
    async function loadDropdowns() {
        const catSelect = document.getElementById('p_category');
        const whSelect = document.getElementById('p_warehouse');

        try {
            // Run both API requests at the same time for speed
            const [catRes, whRes] = await Promise.all([
                apiCall('/categories', 'GET'),
                apiCall('/warehouses', 'GET')
            ]);

            // Populate Categories
            if(catRes.status === 200 && catRes.data) {
                if (catRes.data.length === 0) {
                    catSelect.innerHTML = '<option value="">No categories available</option>';
                } else {
                    catSelect.innerHTML = '<option value="" disabled selected>-- Select Category --</option>';
                    catRes.data.forEach(c => {
                        catSelect.innerHTML += `<option value="${c.id}" class="bg-[#16162a] text-white">${c.name}</option>`;
                    });
                }
            }

            // Populate Warehouses
            if(whRes.status === 200 && whRes.data) {
                if (whRes.data.length === 0) {
                    whSelect.innerHTML = '<option value="">No warehouses available for your account</option>';
                } else {
                    whSelect.innerHTML = '<option value="" disabled selected>-- Select Warehouse --</option>';
                    whRes.data.forEach(w => {
                        whSelect.innerHTML += `<option value="${w.id}" class="bg-[#16162a] text-white">${w.name}</option>`;
                    });
                }
            }
        } catch (error) {
            console.error("Failed to load dropdowns", error);
            showToast("Warning: Could not load categories/warehouses", "error");
        }
    }

    // --- 4. Handle Create Form Submit ---
    document.getElementById('createProductForm').addEventListener('submit', async function(e) {
        e.preventDefault(); // Stop page refresh

        const btn = document.getElementById('btnSubmitProduct');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="ph ph-spinner animate-spin"></i> Saving...';
        btn.disabled = true;

        // Gather all the inputs
        const payload = {
            name: document.getElementById('p_name').value,
            sku: document.getElementById('p_sku').value,
            price: parseFloat(document.getElementById('p_price').value),
            quantity: parseInt(document.getElementById('p_quantity').value),
            alert_threshold: parseInt(document.getElementById('p_threshold').value),
            category_id: parseInt(document.getElementById('p_category').value),
            warehouse_id: parseInt(document.getElementById('p_warehouse').value)
        };

        if (Number.isNaN(payload.category_id) || Number.isNaN(payload.warehouse_id)) {
            showToast("Please select both category and warehouse.", "error");
            btn.innerHTML = originalText;
            btn.disabled = false;
            return;
        }

        try {
            // Send payload to backend
            const response = await apiCall('/products', 'POST', payload);

            if(response.status === 201) {
                showToast("Product created successfully!");
                if (response.data && response.data.product) {
                    allProducts.unshift(response.data.product);
                    document.getElementById('total_products_count').innerText = allProducts.length + ' Items';
                    renderTable(allProducts);
                }
                // Close modal
                document.getElementById('productModal').classList.add('hidden');
                // Clear the form
                document.getElementById('createProductForm').reset();
                // Reload the table
                await loadProductsTable();
            } else {
                showToast("Failed to create product. Check inputs.", "error");
                console.error(response.data);
            }
        } catch (error) {
            showToast("Server error occurred.", "error");
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });

    // --- 5. Handle Delete Action ---
    async function deleteProduct(productId) {
        if(!confirm("Are you sure you want to permanently delete this product?")) return;

        try {
            const response = await apiCall(`/products/${productId}`, 'DELETE');
            
            if(response.status === 200 || response.status === 204) {
                showToast("Product deleted successfully!");
                loadProductsTable(); // Refresh the grid
            } else {
                showToast("Failed to delete product.", "error");
            }
        } catch (error) {
            showToast("Server error occurred.", "error");
        }
    }

    // --- 6. Search Function ---
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        const term = e.target.value.toLowerCase();
        const filtered = allProducts.filter(p => 
            p.name.toLowerCase().includes(term) || 
            p.sku.toLowerCase().includes(term)
        );
        renderTable(filtered);
    });

    // --- 7. Export Report Function ---
    function exportProductsReport() {
        const token = localStorage.getItem('smartstock_token');
        window.open(`/api/reports/export/products?token=${token}`, '_blank');
        showToast('Products report exporting...', 'success');
    }

</script>
@endpush
