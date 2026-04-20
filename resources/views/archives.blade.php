@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Archive History</h1>
        <p class="text-gray-600 mt-2">View archived product snapshots and historical records</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Archives</p>
                    <p class="text-3xl font-bold text-gray-900" x-text="archives.total">0</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Today</p>
                    <p class="text-3xl font-bold text-gray-900" x-text="todayCount">0</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">This Month</p>
                    <p class="text-3xl font-bold text-gray-900" x-text="monthCount">0</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Quantity</p>
                    <p class="text-3xl font-bold text-gray-900" x-text="totalQuantity">0</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m0 10v10l8 4m8-4v-10"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search by Product</label>
                <input type="text" x-model="filters.search" placeholder="Product name..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by User</label>
                <select x-model="filters.user_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Users</option>
                    <template x-for="user in users" :key="user.id">
                        <option :value="user.id" x-text="user.name"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" x-model="filters.from_date"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" x-model="filters.to_date"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
        <div class="mt-4 flex gap-2">
            <button @click="applyFilters()"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                Apply Filters
            </button>
            <button @click="resetFilters()"
                    class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 font-medium">
                Reset
            </button>
        </div>
    </div>

    <!-- Archives Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">ID</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Product</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Quantity</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">User</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Date</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="archive in paginatedArchives" :key="archive.id">
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">#<span x-text="archive.id"></span></td>
                            <td class="px-6 py-4 text-sm">
                                <span class="font-medium text-gray-900" x-text="archive.product.name"></span>
                                <p class="text-gray-500 text-xs">SKU: <span x-text="archive.product.sku"></span></p>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <span x-text="archive.quantity"></span> units
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="text-gray-900" x-text="archive.user?.name || 'N/A'"></span>
                                <p class="text-gray-500 text-xs" x-text="archive.user?.email || ''"></p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span x-text="formatDate(archive.created_at)"></span>
                                <p class="text-gray-400 text-xs" x-text="formatTime(archive.created_at)"></p>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <button @click="viewDetails(archive)"
                                        class="text-blue-600 hover:text-blue-800 font-medium">
                                    View Details
                                </button>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredArchives.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No archive records found
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <template x-if="totalPages > 1">
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <p class="text-sm text-gray-600">
                    Showing <span x-text="(currentPage - 1) * itemsPerPage + 1"></span> to 
                    <span x-text="Math.min(currentPage * itemsPerPage, filteredArchives.length)"></span> of 
                    <span x-text="filteredArchives.length"></span>
                </p>
                <div class="flex gap-2">
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
            </div>
        </template>
    </div>

    <!-- Details Modal -->
    <template x-if="selectedArchive">
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Archive Details</h2>
                    <button @click="selectedArchive = null"
                            class="text-gray-400 hover:text-gray-600 text-2xl leading-none">×</button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Archive ID</p>
                            <p class="text-lg font-semibold text-gray-900" x-text="'#' + selectedArchive.id"></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Product</p>
                            <p class="text-lg font-semibold text-gray-900" x-text="selectedArchive.product?.name"></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">SKU</p>
                            <p class="text-lg font-semibold text-gray-900" x-text="selectedArchive.product?.sku"></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Quantity Archived</p>
                            <p class="text-lg font-semibold text-blue-600" x-text="selectedArchive.quantity + ' units'"></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Category</p>
                            <p class="text-lg font-semibold text-gray-900" x-text="selectedArchive.product?.category?.name || 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Archived By</p>
                            <p class="text-lg font-semibold text-gray-900" x-text="selectedArchive.user?.name || 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Date & Time</p>
                            <p class="text-lg font-semibold text-gray-900" x-text="formatDate(selectedArchive.created_at) + ' ' + formatTime(selectedArchive.created_at)"></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">User Email</p>
                            <p class="text-lg font-semibold text-gray-900" x-text="selectedArchive.user?.email || 'N/A'"></p>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                    <button @click="selectedArchive = null"
                            class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium">
                        Close
                    </button>
                    <button @click="exportArchive(selectedArchive)"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                        Export
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('archive', () => ({
        archives: { total: 0, items: [] },
        users: [],
        selectedArchive: null,
        currentPage: 1,
        itemsPerPage: 10,
        filters: {
            search: '',
            user_id: '',
            from_date: '',
            to_date: ''
        },

        async init() {
            await this.loadArchives();
        },

        async loadArchives() {
            try {
                const token = localStorage.getItem('token');
                const response = await fetch('/api/archives', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });
                const data = await response.json();
                this.archives = data;
                this.archives.items = data.archives || [];
                this.updateStats();
            } catch (error) {
                console.error('Error loading archives:', error);
            }
        },

        async applyFilters() {
            try {
                const token = localStorage.getItem('token');
                const params = new URLSearchParams();
                if (this.filters.user_id) params.append('user_id', this.filters.user_id);
                if (this.filters.from_date) params.append('date', this.filters.from_date);
                
                const response = await fetch(`/api/archives?${params}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });
                const data = await response.json();
                this.archives.items = data.archives || [];
                this.currentPage = 1;
                this.updateStats();
            } catch (error) {
                console.error('Error applying filters:', error);
            }
        },

        resetFilters() {
            this.filters = { search: '', user_id: '', from_date: '', to_date: '' };
            this.loadArchives();
        },

        get filteredArchives() {
            if (!this.filters.search) {
                return this.archives.items || [];
            }
            return (this.archives.items || []).filter(archive =>
                archive.product?.name?.toLowerCase().includes(this.filters.search.toLowerCase()) ||
                archive.product?.sku?.toLowerCase().includes(this.filters.search.toLowerCase())
            );
        },

        get paginatedArchives() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredArchives.slice(start, end);
        },

        get totalPages() {
            return Math.ceil(this.filteredArchives.length / this.itemsPerPage);
        },

        get todayCount() {
            const today = new Date().toDateString();
            return (this.archives.items || []).filter(archive =>
                new Date(archive.created_at).toDateString() === today
            ).length;
        },

        get monthCount() {
            const now = new Date();
            const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);
            return (this.archives.items || []).filter(archive =>
                new Date(archive.created_at) >= monthStart
            ).length;
        },

        get totalQuantity() {
            return (this.archives.items || []).reduce((sum, archive) => sum + archive.quantity, 0);
        },

        updateStats() {
            this.$nextTick(() => {
                // Stats are computed properties, no need for manual update
            });
        },

        viewDetails(archive) {
            this.selectedArchive = archive;
        },

        exportArchive(archive) {
            const csv = `Archive Details\n\nID,Product,SKU,Quantity,User,Date\n${archive.id},${archive.product?.name},${archive.product?.sku},${archive.quantity},${archive.user?.name},${archive.created_at}`;
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `archive-${archive.id}.csv`;
            a.click();
        },

        formatDate(date) {
            return new Date(date).toLocaleDateString();
        },

        formatTime(date) {
            return new Date(date).toLocaleTimeString();
        }
    }));
});
</script>
@endsection
