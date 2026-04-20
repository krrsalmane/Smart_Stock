@extends('layouts.app')

@section('page_title', 'Category Management')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-white flex items-center">
                Product Categories
                <span class="ml-3 px-2 py-0.5 rounded-md text-xs font-semibold bg-brand-primary/20 text-brand-primary border border-brand-primary/30" id="total_count">0 Categories</span>
            </h2>
            <p class="text-gray-400 mt-1">Organize your inventory with logical categories.</p>
        </div>

        <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="bg-brand-primary hover:bg-cyan-400 text-black font-semibold px-4 py-2 rounded-xl shadow-[0_0_15px_rgba(0,212,255,0.4)] transition-all hover:-translate-y-0.5 flex items-center">
            <i class="ph ph-plus-circle text-lg mr-2"></i> Add Category
        </button>
    </div>

    <!-- Data Table -->
    <div class="glass-panel overflow-hidden w-full relative z-10 max-w-4xl">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-400 uppercase bg-black/30 border-b border-white/5">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider w-16">ID</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Category Name</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body" class="divide-y divide-white/5 text-gray-300">
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                            <i class="ph ph-spinner-gap animate-spin text-3xl mx-auto mb-2 text-brand-primary"></i>
                            <p>Loading categories...</p>
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
                    <i class="ph ph-tag text-brand-primary mr-2"></i> New Category
                </h3>
                <button onclick="document.getElementById('createModal').classList.add('hidden')" class="text-gray-400 hover:text-white transition-colors">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>

            <form id="createForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Category Name *</label>
                    <input type="text" id="c_name" required class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-brand-primary" placeholder="e.g. Electronics">
                </div>

                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-white/10">
                    <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-white/10 text-gray-300 hover:bg-white/5 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="btnSubmit" class="bg-brand-primary hover:bg-cyan-400 text-black font-semibold px-6 py-2 rounded-lg shadow-[0_0_15px_rgba(0,212,255,0.4)] transition-all">
                        Save
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
            const response = await apiCall('/categories', 'GET');
            if(response.status === 200) {
                const items = response.data;
                document.getElementById('total_count').innerText = items.length + ' Categories';
                
                tBody.innerHTML = ''; 
                if(items.length === 0) {
                    tBody.innerHTML = `<tr><td colspan="3" class="px-6 py-8 text-center text-gray-500">No categories found.</td></tr>`;
                    return;
                }

                items.forEach(item => {
                    const row = `
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4 font-mono text-gray-500">#${item.id}</td>
                            <td class="px-6 py-4 font-medium text-white group-hover:text-brand-primary transition-colors">
                                <i class="ph ph-tag text-gray-500 mr-2"></i> ${item.name}
                            </td>
                            <td class="px-6 py-4 text-right text-lg space-x-2">
                                <button onclick="deleteItem(${item.id})" class="text-gray-500 hover:text-brand-danger transition-colors"><i class="ph ph-trash"></i></button>
                            </td>
                        </tr>
                    `;
                    tBody.insertAdjacentHTML('beforeend', row);
                });
            }
        } catch (error) {
            tBody.innerHTML = `<tr><td colspan="3" class="px-6 py-8 text-center text-brand-danger">API Error</td></tr>`;
        }
    }

    document.getElementById('createForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const payload = { name: document.getElementById('c_name').value };
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = 'Saving...'; btn.disabled = true;

        try {
            const res = await apiCall('/categories', 'POST', payload);
            if(res.status === 201) {
                showToast("Category created successfully!");
                document.getElementById('createModal').classList.add('hidden');
                document.getElementById('createForm').reset();
                loadTable();
            } else {
                // Show the actual error message from the server
                let errorMsg = "Failed to create category";
                if (res.data.message) {
                    errorMsg = res.data.message;
                } else if (res.data.errors && res.data.errors.name) {
                    errorMsg = Array.isArray(res.data.errors.name) ? res.data.errors.name[0] : res.data.errors.name;
                } else if (res.data.error) {
                    errorMsg = res.data.error;
                }
                showToast(errorMsg, "error");
                console.error("Category creation failed:", res.data);
            }
        } catch (error) {
            showToast("Server error: " + error.message, "error");
            console.error("Category creation error:", error);
        } finally {
            btn.innerHTML = 'Save'; btn.disabled = false;
        }
    });

    async function deleteItem(id) {
        if(!confirm("Are you sure? This will fail if products still use this category.")) return;
        try {
            const res = await apiCall(`/categories/${id}`, 'DELETE');
            if(res.status === 200 || res.status === 204) {
                showToast("Category deleted!");
                loadTable();
            } else {
                showToast("Could not delete. Check references.", "error");
            }
        } catch (error) {
            showToast("Error", "error");
        }
    }
</script>
@endpush
