@extends('layouts.app')

@section('page_title', 'Supplier Portal')

@section('content')
    <div class="mb-8">
        <h2 class="text-3xl font-bold tracking-tight text-white flex items-center">
            My Assigned Commands
            <span class="ml-3 px-2 py-0.5 rounded-md text-xs font-semibold bg-brand-warning/20 text-brand-warning border border-brand-warning/30" id="total_count">Loading...</span>
        </h2>
        <p class="text-gray-400 mt-1">Review assigned commands, accept or decline them, then ship accepted ones.</p>
    </div>

    <!-- Data Table -->
    <div class="glass-panel overflow-hidden w-full relative z-10">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="text-xs text-gray-400 uppercase bg-black/30 border-b border-white/5">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider w-16">CMD ID</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Client</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-right">Total Cost</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Order Date</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Expected Delivery</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Delivery Status</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body" class="divide-y divide-white/5 text-gray-300">
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="ph ph-spinner-gap animate-spin text-3xl mx-auto mb-2 text-brand-primary"></i>
                            <p>Loading your commands...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        await loadMyCommands();
    });

    async function loadMyCommands() {
        const tBody = document.getElementById('table-body');
        try {
            const response = await apiCall('/my-commands', 'GET');
            if (response.status === 200) {
                const data = response.data;
                const items = data.commands || [];
                document.getElementById('total_count').innerText = items.length + ' Commands';

                tBody.innerHTML = '';
                if (items.length === 0) {
                    tBody.innerHTML = `<tr><td colspan="8" class="px-6 py-8 text-center text-gray-500">No commands assigned to you yet.</td></tr>`;
                    return;
                }

                items.forEach(item => {
                    const deliveryStatusColors = {
                        'pending': 'bg-brand-warning/20 text-brand-warning border-brand-warning/30',
                        'confirmed': 'bg-brand-secondary/20 text-brand-secondary border-brand-secondary/30',
                        'shipped': 'bg-brand-primary/20 text-brand-primary border-brand-primary/30',
                        'delivered': 'bg-brand-success/20 text-brand-success border-brand-success/30',
                        'cancelled': 'bg-brand-danger/20 text-brand-danger border-brand-danger/30'
                    };
                    const badgeClass = deliveryStatusColors[item.delivery_status] || 'bg-gray-500/20 text-gray-400 border-gray-500/30';

                    const canDecide = item.delivery_status === 'pending';
                    const canShip = item.delivery_status === 'confirmed';

                    const row = `
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4 font-mono text-gray-500">#${item.command_id}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded bg-brand-secondary/20 text-brand-secondary border border-brand-secondary/30">
                                    ${item.command_type || 'purchase'}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-white">
                                <i class="ph ph-user text-gray-500 mr-2"></i> ${item.client}
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-brand-primary font-medium">
                                $${parseFloat(item.total_cost).toFixed(2)}
                            </td>
                            <td class="px-6 py-4 text-center text-xs text-gray-400">
                                ${item.order_date || item.ordered_at || '-'}
                            </td>
                            <td class="px-6 py-4 text-center text-xs text-gray-400">
                                ${item.expected_delivery || item.expected_at || '-'}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md border ${badgeClass}">
                                    ${item.delivery_status}
                                </span>
                                ${item.shipped_at ? `<p class="text-[10px] text-gray-500 mt-1">Shipped: ${new Date(item.shipped_at).toLocaleDateString()}</p>` : ''}
                                ${item.delivered_at ? `<p class="text-[10px] text-gray-500 mt-1">Delivered: ${new Date(item.delivered_at).toLocaleDateString()}</p>` : ''}
                            </td>
                            <td class="px-6 py-4 text-center">
                                ${canDecide ? `
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="decideCommand(${item.command_id}, 'accept')" class="bg-brand-success/20 text-brand-success hover:bg-brand-success/30 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors flex items-center">
                                            <i class="ph ph-check text-sm mr-1"></i> Accept
                                        </button>
                                        <button onclick="decideCommand(${item.command_id}, 'decline')" class="bg-brand-danger/20 text-brand-danger hover:bg-brand-danger/30 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors flex items-center">
                                            <i class="ph ph-x text-sm mr-1"></i> Decline
                                        </button>
                                    </div>
                                ` : canShip ? `
                                    <button onclick="shipCommand(${item.command_id})" class="bg-brand-primary/20 text-brand-primary hover:bg-brand-primary/30 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors flex items-center mx-auto">
                                        <i class="ph ph-truck text-sm mr-1"></i> Ship Now
                                    </button>
                                ` : `
                                    <span class="text-gray-600 text-xs">
                                        ${item.delivery_status === 'shipped' ? '<i class="ph ph-clock text-brand-primary"></i> Awaiting confirmation' : item.delivery_status === 'cancelled' ? '<i class="ph ph-x-circle text-brand-danger"></i> Declined' : '<i class="ph ph-check-circle text-brand-success"></i> Complete'}
                                    </span>
                                `}
                            </td>
                        </tr>
                    `;
                    tBody.insertAdjacentHTML('beforeend', row);
                });
            } else if (response.status === 404) {
                tBody.innerHTML = `<tr><td colspan="8" class="px-6 py-8 text-center text-gray-500">No supplier profile is linked to your account. Contact an administrator.</td></tr>`;
                document.getElementById('total_count').innerText = '0 Commands';
            }
        } catch (error) {
            tBody.innerHTML = `<tr><td colspan="8" class="px-6 py-8 text-center text-brand-danger">Failed to load commands. Make sure you have a supplier profile.</td></tr>`;
        }
    }

    async function shipCommand(commandId) {
        if (!confirm('Are you sure you want to mark this command as shipped? This action cannot be undone.')) return;

        try {
            const res = await apiCall(`/my-commands/${commandId}/ship`, 'PUT');
            if (res.status === 200) {
                showToast('Delivery shipped successfully!');
                await loadMyCommands();
            } else {
                showToast(res.data.message || 'Failed to ship', 'error');
            }
        } catch (error) {
            showToast('Server error', 'error');
        }
    }

    async function decideCommand(commandId, decision) {
        const actionText = decision === 'accept' ? 'accept' : 'decline';
        if (!confirm(`Are you sure you want to ${actionText} this command?`)) return;

        try {
            const res = await apiCall(`/my-commands/${commandId}/decision`, 'PUT', { decision });
            if (res.status === 200) {
                showToast(res.data.message || 'Command updated successfully!');
                await loadMyCommands();
            } else {
                showToast(res.data.message || 'Failed to update command', 'error');
            }
        } catch (error) {
            showToast('Server error', 'error');
        }
    }
</script>
@endpush
