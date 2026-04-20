@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
        <p class="text-gray-600 mt-2">Track and manage your orders in real-time</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Orders</p>
                    <p class="text-3xl font-bold text-gray-900" x-text="stats.total">0</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Pending</p>
                    <p class="text-3xl font-bold text-yellow-600" x-text="stats.pending">0</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">In Transit</p>
                    <p class="text-3xl font-bold text-blue-600" x-text="stats.in_transit">0</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Delivered</p>
                    <p class="text-3xl font-bold text-green-600" x-text="stats.delivered">0</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Sort -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Order</label>
                <input type="text" x-model="search" placeholder="Search by Order ID or Product..."
                       @input="applyFilters()"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
                <select x-model="statusFilter" @change="applyFilters()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="in_transit">In Transit</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                <select x-model="sortBy" @change="applyFilters()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="recent">Most Recent</option>
                    <option value="oldest">Oldest First</option>
                    <option value="amount">Highest Amount</option>
                    <option value="expected">Expected Soon</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="space-y-4">
        <template x-for="order in paginatedOrders" :key="order.id">
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Order #<span x-text="order.id"></span></h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Placed on <span x-text="formatDate(order.ordered_at)"></span>
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span @click="showDetails(order)"
                                  class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium cursor-pointer transition"
                                  :class="getStatusClass(order.status)">
                                <span x-text="formatStatus(order.status)"></span>
                            </span>
                            <template x-if="order.status === 'pending' || order.status === 'approved'">
                                <button @click="cancelOrder(order)"
                                        class="px-3 py-1 text-sm text-red-600 hover:bg-red-50 rounded border border-red-300 font-medium">
                                    Cancel
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Items</p>
                            <p class="text-lg font-semibold text-gray-900" x-text="order.products?.length || 0"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Amount</p>
                            <p class="text-lg font-semibold text-gray-900">$<span x-text="order.total_cost?.toFixed(2)"></span></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Expected Delivery</p>
                            <p class="text-lg font-semibold text-gray-900">
                                <span x-text="order.expected_at ? formatDate(order.expected_at) : 'N/A'"></span>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Suppliers</p>
                            <p class="text-lg font-semibold text-gray-900" x-text="order.suppliers?.length || 0"></p>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="mt-4 border-t border-gray-100 pt-4">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Items Ordered:</p>
                        <div class="space-y-2">
                            <template x-for="product in order.products?.slice(0, 2)" :key="product.id">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-700"><span x-text="product.name"></span> × <span x-text="product.pivot?.quantity"></span></span>
                                    <span class="text-gray-900 font-medium">$<span x-text="(product.pivot?.unit_price * product.pivot?.quantity).toFixed(2)"></span></span>
                                </div>
                            </template>
                            <template x-if="order.products?.length > 2">
                                <p class="text-sm text-gray-500 italic">+ <span x-text="order.products.length - 2"></span> more items</p>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Order Status Timeline -->
                <div class="p-6 bg-gray-50">
                    <p class="text-sm font-semibold text-gray-700 mb-3">Delivery Status:</p>
                    <div class="space-y-2">
                        <template x-for="(supplier, idx) in order.suppliers" :key="supplier.id">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                                     :class="getSupplierStatusColor(supplier.status)">
                                    <svg v-if="supplier.status === 'delivered'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <svg v-else-if="supplier.status === 'shipped'" class="w-4 h-4 animate-bounce" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 11a1 1 0 011-1h2.101a7 7 0 00.05-1.992A.75.75 0 005.75 7H1.75A.75.75 0 001 7.75v1.5a.75.75 0 001 .75h.001zm7.1-7a.75.75 0 00.298-1.443 9.103 9.103 0 00-7.754 6.6.75.75 0 001.422.503 7.614 7.614 0 016.34-5.66z"/>
                                    </svg>
                                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900" x-text="supplier.name"></p>
                                    <p class="text-xs text-gray-500">
                                        <span x-text="formatStatus(supplier.status)"></span>
                                        <template x-if="supplier.tracking_number">
                                            - Tracking: <span x-text="supplier.tracking_number" class="font-mono"></span>
                                        </template>
                                    </p>
                                </div>
                                <button @click="viewSupplierTracking(order.id, supplier.id)"
                                        class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                                    Track
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-200 flex justify-end gap-2">
                    <button @click="showDetails(order)"
                            class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium text-gray-700">
                        View Details
                    </button>
                    <template x-if="order.status === 'delivered'">
                        <button @click="reorderItems(order)"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            Reorder
                        </button>
                    </template>
                </div>
            </div>
        </template>

        <template x-if="filteredOrders.length === 0">
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m0 10v10l8 4"/>
                </svg>
                <p class="text-gray-500 text-lg">No orders found</p>
</div>
        </template>
    </div>

    <!-- Pagination -->
    <template x-if="totalPages > 1">
        <div class="mt-8 flex items-center justify-center gap-2">
            <button @click="currentPage > 1 && currentPage--"
                    :disabled="currentPage === 1"
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                Previous
            </button>
            <template x-for="page in Array.from({length: totalPages}, (_, i) => i + 1)" :key="page">
                <button @click="currentPage = page"
                        :class="{'bg-blue-600 text-white': currentPage === page, 'border border-gray-300': currentPage !== page}"
                        class="px-4 py-2 rounded-lg hover:bg-gray-50">
                    <span x-text="page"></span>
                </button>
            </template>
            <button @click="currentPage < totalPages && currentPage++"
                    :disabled="currentPage === totalPages"
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                Next
            </button>
        </div>
    </template>

    <!-- Details Modal -->
    <template x-if="selectedOrder">
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-96 overflow-y-auto">
                <div class="sticky top-0 px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900">Order Details #<span x-text="selectedOrder.id"></span></h2>
                    <button @click="selectedOrder = null"
                            class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Order Info -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 uppercase">Order Date</p>
                            <p class="text-lg font-semibold" x-text="formatDate(selectedOrder.ordered_at)"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase">Expected Delivery</p>
                            <p class="text-lg font-semibold" x-text="selectedOrder.expected_at ? formatDate(selectedOrder.expected_at) : 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase">Total Amount</p>
                            <p class="text-lg font-semibold text-blue-600">$<span x-text="selectedOrder.total_cost?.toFixed(2)"></span></p>
                        </div>
                    </div>

                    <!-- Full Tracking -->
                    <div class="border-t pt-6">
                        <h3 class="font-bold text-gray-900 mb-4">Full Tracking Information</h3>
                        <div class="space-y-4">
                            <template x-for="supplier in selectedOrder.suppliers" :key="supplier.id">
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="font-semibold text-gray-900" x-text="supplier.name"></p>
                                            <p class="text-sm text-gray-500" x-text="supplier.email"></p>
                                        </div>
                                        <span :class="getStatusClass(supplier.status)" x-text="formatStatus(supplier.status)"></span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                                        <p>Qty: <span class="font-medium" x-text="supplier.quantity_ordered"></span></p>
                                        <p>Tracking: <span class="font-mono" x-text="supplier.tracking_number || 'Not yet shipped'"></span></p>
                                        <p>Shipped: <span x-text="supplier.shipped_at ? formatDate(supplier.shipped_at) : 'Pending'"></span></p>
                                        <p>Delivered: <span x-text="supplier.delivered_at ? formatDate(supplier.delivered_at) : 'Not yet delivered'"></span></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- All Products -->
                    <div class="border-t pt-6">
                        <h3 class="font-bold text-gray-900 mb-4">Products in this Order</h3>
                        <table class="w-full text-sm">
                            <thead class="border-b">
                                <tr>
                                    <th class="text-left py-2 px-2">Product</th>
                                    <th class="text-center py-2 px-2">Quantity</th>
                                    <th class="text-right py-2 px-2">Unit Price</th>
                                    <th class="text-right py-2 px-2">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="product in selectedOrder.products" :key="product.id">
                                    <tr class="border-b">
                                        <td class="py-3 px-2">
                                            <p class="font-medium" x-text="product.name"></p>
                                            <p class="text-gray-500 text-xs">SKU: <span x-text="product.sku"></span></p>
                                        </td>
                                        <td class="py-3 px-2 text-center" x-text="product.pivot?.quantity"></td>
                                        <td class="py-3 px-2 text-right">$<span x-text="product.pivot?.unit_price?.toFixed(2)"></span></td>
                                        <td class="py-3 px-2 text-right font-semibold">$<span x-text="(product.pivot?.unit_price * product.pivot?.quantity).toFixed(2)"></span></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-2">
                    <button @click="selectedOrder = null"
                            class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('orderTracking', () => ({
        orders: [],
        selectedOrder: null,
        currentPage: 1,
        itemsPerPage: 5,
        search: '',
        statusFilter: '',
        sortBy: 'recent',
        stats: { total: 0, pending: 0, in_transit: 0, delivered: 0 },

        async init() {
            await this.loadOrders();
        },

        async loadOrders() {
            try {
                const token = localStorage.getItem('token');
                const response = await fetch('/api/commands', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });
                const data = await response.json();
                this.orders = Array.isArray(data) ? data : data.commands || [];
                this.updateStats();
            } catch (error) {
                console.error('Error loading orders:', error);
            }
        },

        applyFilters() {
            this.currentPage = 1;
        },

        get filteredOrders() {
            let filtered = [...this.orders];

            if (this.search) {
                filtered = filtered.filter(order =>
                    order.id.toString().includes(this.search) ||
                    order.products?.some(p => p.name?.toLowerCase().includes(this.search.toLowerCase()))
                );
            }

            if (this.statusFilter) {
                filtered = filtered.filter(order => order.status === this.statusFilter);
            }

            if (this.sortBy === 'recent') {
                filtered.sort((a, b) => new Date(b.ordered_at) - new Date(a.ordered_at));
            } else if (this.sortBy === 'oldest') {
                filtered.sort((a, b) => new Date(a.ordered_at) - new Date(b.ordered_at));
            } else if (this.sortBy === 'amount') {
                filtered.sort((a, b) => b.total_cost - a.total_cost);
            } else if (this.sortBy === 'expected') {
                filtered.sort((a, b) => {
                    if (!a.expected_at) return 1;
                    if (!b.expected_at) return -1;
                    return new Date(a.expected_at) - new Date(b.expected_at);
                });
            }

            return filtered;
        },

        get paginatedOrders() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredOrders.slice(start, end);
        },

        get totalPages() {
            return Math.ceil(this.filteredOrders.length / this.itemsPerPage);
        },

        updateStats() {
            this.stats = {
                total: this.orders.length,
                pending: this.orders.filter(o => o.status === 'pending').length,
                in_transit: this.orders.filter(o => o.status === 'approved' || this.getOrderStatus(o) === 'in_transit').length,
                delivered: this.orders.filter(o => this.getOrderStatus(o) === 'delivered').length,
            };
        },

        getOrderStatus(order) {
            if (!order.suppliers || order.suppliers.length === 0) return order.status;
            const allDelivered = order.suppliers.every(s => s.status === 'delivered');
            if (allDelivered) return 'delivered';
            const anyShipped = order.suppliers.some(s => s.status === 'shipped');
            return anyShipped ? 'in_transit' : order.status;
        },

        formatStatus(status) {
            const map = {
                'pending': 'Pending',
                'approved': 'Approved',
                'shipped': 'Shipped',
                'in_transit': 'In Transit',
                'delivered': 'Delivered',
                'cancelled': 'Cancelled'
            };
            return map[status] || status;
        },

        getStatusClass(status) {
            const classes = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'approved': 'bg-blue-100 text-blue-800',
                'shipped': 'bg-purple-100 text-purple-800',
                'in_transit': 'bg-cyan-100 text-cyan-800',
                'delivered': 'bg-green-100 text-green-800',
                'cancelled': 'bg-red-100 text-red-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },

        getSupplierStatusColor(status) {
            const classes = {
                'pending': 'bg-yellow-100 text-yellow-600',
                'shipped': 'bg-blue-100 text-blue-600',
                'delivered': 'bg-green-100 text-green-600'
            };
            return classes[status] || 'bg-gray-100 text-gray-600';
        },

        async cancelOrder(order) {
            if (!confirm('Are you sure you want to cancel this order?')) return;

            try {
                const token = localStorage.getItem('token');
                const response = await fetch(`/api/commands/${order.id}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ reason: 'Cancelled by customer' })
                });

                if (response.ok) {
                    alert('Order cancelled successfully');
                    await this.loadOrders();
                } else {
                    alert('Failed to cancel order');
                }
            } catch (error) {
                console.error('Error cancelling order:', error);
                alert('Error cancelling order');
            }
        },

        showDetails(order) {
            this.selectedOrder = { ...order };
        },

        viewSupplierTracking(orderId, supplierId) {
            alert(`View tracking for supplier ${supplierId}`);
        },

        reorderItems(order) {
            alert('Redirecting to create new order with same items...');
        },

        formatDate(date) {
            return new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }
    }));
});
</script>
@endsection
