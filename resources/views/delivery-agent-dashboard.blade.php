@extends('layouts.app')

@section('page_title', 'Delivery Dashboard')

@section('content')
    <div class="mb-8">
        <h2 class="text-3xl font-bold tracking-tight text-white flex items-center">
            My Deliveries
            <span class="ml-3 px-2 py-0.5 rounded-md text-xs font-semibold bg-brand-primary/20 text-brand-primary border border-brand-primary/30" id="total_count">Loading...</span>
        </h2>
        <p class="text-gray-400 mt-1">Track assigned deliveries and update delivery progress.</p>
    </div>

    <div class="glass-panel overflow-hidden w-full relative z-10">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="text-xs text-gray-400 uppercase bg-black/30 border-b border-white/5">
                    <tr>
                        <th class="px-6 py-4 font-semibold tracking-wider w-16">ID</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Client</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Status</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Assigned</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body" class="divide-y divide-white/5 text-gray-300">
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">Loading deliveries...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        await loadDeliveries();
    });

    async function loadDeliveries() {
        const tBody = document.getElementById('table-body');
        try {
            const res = await apiCall('/my-deliveries', 'GET');
            if (res.status !== 200) {
                tBody.innerHTML = `<tr><td colspan="5" class="px-6 py-8 text-center text-brand-danger">Failed to load deliveries.</td></tr>`;
                return;
            }

            const items = res.data.deliveries || [];
            document.getElementById('total_count').innerText = `${items.length} Deliveries`;

            if (!items.length) {
                tBody.innerHTML = `<tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No deliveries assigned yet.</td></tr>`;
                return;
            }

            const statusMap = {
                pending: 'bg-brand-warning/20 text-brand-warning border-brand-warning/30',
                approved: 'bg-brand-primary/20 text-brand-primary border-brand-primary/30',
                in_transit: 'bg-brand-secondary/20 text-brand-secondary border-brand-secondary/30',
                delayed: 'bg-brand-warning/20 text-brand-warning border-brand-warning/30',
                delivered: 'bg-brand-success/20 text-brand-success border-brand-success/30',
                cancelled: 'bg-brand-danger/20 text-brand-danger border-brand-danger/30'
            };

            tBody.innerHTML = '';
            items.forEach((item) => {
                const badgeClass = statusMap[item.status] || 'bg-gray-500/20 text-gray-400 border-gray-500/30';
                const assignedDate = item.assigned_at ? new Date(item.assigned_at).toLocaleDateString() : '-';
                const canStart = item.status === 'pending' || item.status === 'approved';
                const canComplete = item.status === 'in_transit';

                tBody.insertAdjacentHTML('beforeend', `
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 font-mono text-gray-500">#${item.id}</td>
                        <td class="px-6 py-4 text-white">${item.client ? item.client.name : 'N/A'}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md border ${badgeClass}">
                                ${item.status}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-xs text-gray-400">${assignedDate}</td>
                        <td class="px-6 py-4 text-center space-x-2">
                            ${canStart ? `<button onclick="startDelivery(${item.id})" class="bg-brand-primary/20 text-brand-primary hover:bg-brand-primary/30 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors">Start</button>` : ''}
                            ${canComplete ? `<button onclick="completeDelivery(${item.id})" class="bg-brand-success/20 text-brand-success hover:bg-brand-success/30 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors">Complete</button>` : ''}
                            ${!canStart && !canComplete ? `<span class="text-xs text-gray-500">No action</span>` : ''}
                        </td>
                    </tr>
                `);
            });
        } catch (error) {
            tBody.innerHTML = `<tr><td colspan="5" class="px-6 py-8 text-center text-brand-danger">Server error while loading deliveries.</td></tr>`;
        }
    }

    async function startDelivery(id) {
        try {
            const res = await apiCall(`/my-deliveries/${id}/start`, 'PUT');
            if (res.status === 200) {
                showToast('Delivery started successfully');
                await loadDeliveries();
            } else {
                showToast(res.data.error || 'Failed to start delivery', 'error');
            }
        } catch (error) {
            showToast('Server error', 'error');
        }
    }

    async function completeDelivery(id) {
        try {
            const res = await apiCall(`/my-deliveries/${id}/complete`, 'PUT', {});
            if (res.status === 200) {
                showToast('Delivery completed successfully');
                await loadDeliveries();
            } else {
                showToast(res.data.error || 'Failed to complete delivery', 'error');
            }
        } catch (error) {
            showToast('Server error', 'error');
        }
    }
</script>
@endpush
