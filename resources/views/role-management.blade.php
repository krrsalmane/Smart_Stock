@extends('layouts.app')

@section('page_title', 'Role Management')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="roleManagement()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">Role Management</h1>
        <p class="text-gray-400 mt-2" x-text="userRole === 'admin' ? 'Manage user roles and permissions' : 'Assign delivery and supplier roles'"></p>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="flex items-center justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
    </div>

    <!-- Error Message -->
    <div x-show="error" x-transition class="mb-6 bg-red-500/10 border border-red-500/30 rounded-lg p-4">
        <div class="flex items-center">
            <span class="material-symbols-outlined text-red-400 mr-3">error</span>
            <span class="text-red-400" x-text="error"></span>
        </div>
    </div>

    <!-- Success Message -->
    <div x-show="success" x-transition class="mb-6 bg-green-500/10 border border-green-500/30 rounded-lg p-4">
        <div class="flex items-center">
            <span class="material-symbols-outlined text-green-400 mr-3">check_circle</span>
            <span class="text-green-400" x-text="success"></span>
        </div>
    </div>

    <!-- Users Table -->
    <div class="glass-card rounded-lg p-4 md:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-semibold text-white">Users</h2>
            <div class="w-full sm:w-auto">
                <input 
                    type="text" 
                    x-model="searchQuery" 
                    placeholder="Search users..." 
                    class="w-full sm:w-64 px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500"
                >
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left py-3 px-4 text-gray-400 font-medium">Name</th>
                        <th class="text-left py-3 px-4 text-gray-400 font-medium">Email</th>
                        <th class="text-left py-3 px-4 text-gray-400 font-medium">Current Role</th>
                        <th class="text-left py-3 px-4 text-gray-400 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="user in filteredUsers" :key="user.id">
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="py-3 px-4 text-white" x-text="user.name"></td>
                            <td class="py-3 px-4 text-gray-300" x-text="user.email"></td>
                            <td class="py-3 px-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium" :class="getRoleBadgeClass(user.role)" x-text="getRoleLabel(user.role)"></span>
                            </td>
                            <td class="py-3 px-4">
                                <button 
                                    @click="openRoleModal(user)" 
                                    class="w-full sm:w-auto px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition-colors min-h-[44px]"
                                    :disabled="user.id === currentUserId"
                                >
                                    <span x-show="user.id === currentUserId">Cannot Change Own Role</span>
                                    <span x-show="user.id !== currentUserId">Change Role</span>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div x-show="filteredUsers.length === 0 && !loading" class="text-center py-12">
            <span class="material-symbols-outlined text-6xl text-gray-600">person_off</span>
            <p class="text-gray-400 mt-4">No users found</p>
        </div>
    </div>

    <!-- Role Assignment Modal -->
    <div x-show="showRoleModal" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="closeRoleModal()">
        <div class="glass-card rounded-lg p-4 md:p-6 max-w-md w-full mx-4" @click.stop>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-white">Change User Role</h3>
                <button @click="closeRoleModal()" class="text-gray-400 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="mb-6">
                <p class="text-gray-300 mb-2">Assigning role to:</p>
                <div class="p-4 bg-white/5 rounded-lg">
                    <p class="text-white font-medium" x-text="selectedUser?.name"></p>
                    <p class="text-sm text-gray-400" x-text="selectedUser?.email"></p>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-300 mb-2">Select New Role</label>
                <select 
                    x-model="selectedRole" 
                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
                >
                    <option value="">Choose a role...</option>
                    <template x-for="role in assignableRoles" :key="role.role">
                        <option :value="role.role" x-text="role.label"></option>
                    </template>
                </select>
            </div>

            <div class="flex space-x-3">
                <button 
                    @click="closeRoleModal()" 
                    class="flex-1 px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-colors"
                >
                    Cancel
                </button>
                <button 
                    @click="assignRole()" 
                    :disabled="!selectedRole || assigning"
                    class="flex-1 px-4 py-2 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-600 disabled:cursor-not-allowed text-white rounded-lg transition-colors"
                >
                    <span x-show="!assigning">Assign Role</span>
                    <span x-show="assigning" class="flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Assigning...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('roleManagement', () => ({
        loading: true,
        assigning: false,
        error: null,
        success: null,
        users: [],
        assignableRoles: [],
        searchQuery: '',
        showRoleModal: false,
        selectedUser: null,
        selectedRole: '',
        userRole: null,
        currentUserId: null,

        get filteredUsers() {
            if (!this.searchQuery) return this.users;
            const query = this.searchQuery.toLowerCase();
            return this.users.filter(user => 
                user.name.toLowerCase().includes(query) || 
                user.email.toLowerCase().includes(query) ||
                user.role.toLowerCase().includes(query)
            );
        },

        async init() {
            await this.loadData();
        },

        async loadData() {
            try {
                this.loading = true;
                this.error = null;

                // Get current user info
                const userResponse = await this.apiCall('/user');
                this.userRole = userResponse.role;
                this.currentUserId = userResponse.id;

                // Load users
                const usersEndpoint = this.userRole === 'admin' 
                    ? '/admin/users-with-roles' 
                    : '/magasinier/users-with-roles';
                const usersResponse = await this.apiCall(usersEndpoint);
                this.users = usersResponse.users;

                // Load assignable roles
                const assignableEndpoint = this.userRole === 'admin'
                    ? '/roles/assignable'
                    : '/magasinier/assignable-roles';
                const assignableResponse = await this.apiCall(assignableEndpoint);
                this.assignableRoles = assignableResponse.roles;
            } catch (error) {
                this.error = error.message || 'Failed to load data';
            } finally {
                this.loading = false;
            }
        },

        openRoleModal(user) {
            this.selectedUser = user;
            this.selectedRole = '';
            this.showRoleModal = true;
            this.error = null;
            this.success = null;
        },

        closeRoleModal() {
            this.showRoleModal = false;
            this.selectedUser = null;
            this.selectedRole = '';
        },

        async assignRole() {
            if (!this.selectedRole || !this.selectedUser) return;

            try {
                this.assigning = true;
                this.error = null;

                const endpoint = this.userRole === 'admin'
                    ? `/users/${this.selectedUser.id}/assign-role`
                    : `/magasinier/users/${this.selectedUser.id}/assign-role`;

                await this.apiCall(endpoint, 'POST', { role: this.selectedRole });

                this.success = `Role successfully assigned to ${this.selectedUser.name}`;
                this.closeRoleModal();
                await this.loadData();

                setTimeout(() => { this.success = null; }, 3000);
            } catch (error) {
                this.error = error.message || 'Failed to assign role';
            } finally {
                this.assigning = false;
            }
        },

        getRoleLabel(role) {
            const labels = {
                'admin': 'Administrator',
                'magasinier': 'Magasinier',
                'client': 'Client',
                'supplier': 'Supplier',
                'delivery_agent': 'Delivery Agent',
            };
            return labels[role] || role;
        },

        getRoleBadgeClass(role) {
            const classes = {
                'admin': 'bg-purple-500/20 text-purple-400',
                'magasinier': 'bg-blue-500/20 text-blue-400',
                'client': 'bg-green-500/20 text-green-400',
                'supplier': 'bg-orange-500/20 text-orange-400',
                'delivery_agent': 'bg-yellow-500/20 text-yellow-400',
            };
            return classes[role] || 'bg-gray-500/20 text-gray-400';
        },

        async apiCall(endpoint, method = 'GET', data = null) {
            const token = localStorage.getItem('smartstock_token');
            const headers = {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            };

            const config = { method, headers };
            if (data && method !== 'GET') {
                config.body = JSON.stringify(data);
            }

            const response = await fetch(`/api${endpoint}`, config);
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.error || result.message || 'Request failed');
            }

            return result;
        }
    }));
});
</script>
@endsection
