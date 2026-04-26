<?php

namespace App\Services;

use App\Models\Archive;
use App\Models\Product;

class ArchiveService
{
    public static function snapshot(Product $product, string $action = Archive::ACTION_SNAPSHOT, ?string $notes = null): Archive
    {
        try {
            $snapshotData = [
                'name' => $product->name,
                'sku' => $product->sku,
                'quantity' => $product->quantity,
                'price' => $product->price,
                'alert_threshold' => $product->alert_threshold,
                'category_id' => $product->category_id,
                'warehouse_id' => $product->warehouse_id,
                'archived_at' => now()->toDateTimeString(),
            ];

            return Archive::create([
                'product_id' => $product->id,
                'user_id' => auth()->id() ?? 1,
                'quantity' => $product->quantity,
                'action' => $action,
                'notes' => $notes,
                'snapshot_data' => $snapshotData,
            ]);
        } catch (\Exception $e) {
            \Log::error('Archive snapshot_data failed, trying without it', [
                'error' => $e->getMessage(),
                'product_id' => $product->id
            ]);

            return Archive::create([
                'product_id' => $product->id,
                'user_id' => auth()->id() ?? 1,
                'quantity' => $product->quantity,
                'action' => $action,
                'notes' => $notes,
                'snapshot_data' => null,
            ]);
        }
    }

    public static function archiveBeforeDelete(Product $product, ?string $reason = null): Archive
    {
        $notes = $reason ? "Product deleted. Reason: {$reason}" : "Product deleted";
        return self::snapshot($product, Archive::ACTION_BEFORE_DELETE, $notes);
    }

    public static function archiveBeforeUpdate(Product $product, array $changes = []): Archive
    {
        $notes = "Product updated. Changes: " . json_encode($changes);
        return self::snapshot($product, Archive::ACTION_BEFORE_UPDATE, $notes);
    }

    public static function archiveStockIn(Product $product, int $quantityAdded, ?string $note = null): Archive
    {
        $notes = $note ? "Stock IN: +{$quantityAdded} units. {$note}" : "Stock IN: +{$quantityAdded} units";
        return self::snapshot($product, Archive::ACTION_STOCK_IN, $notes);
    }

    public static function archiveStockOut(Product $product, int $quantityRemoved, ?string $note = null): Archive
    {
        $notes = $note ? "Stock OUT: -{$quantityRemoved} units. {$note}" : "Stock OUT: -{$quantityRemoved} units";
        return self::snapshot($product, Archive::ACTION_STOCK_OUT, $notes);
    }

    public static function getInventoryValueAtDate(\DateTime $date): array
    {
        $archives = Archive::selectRaw('
            product_id,
            MAX(created_at) as latest_archive
        ')
            ->where('created_at', '<=', $date->format('Y-m-d H:i:s'))
            ->groupBy('product_id')
            ->get();

        $totalValue = 0;
        $productValues = [];

        foreach ($archives as $archive) {
            $snapshot = Archive::where('product_id', $archive->product_id)
                ->where('created_at', $archive->latest_archive)
                ->first();

            if ($snapshot && $snapshot->snapshot_data) {
                $value = $snapshot->snapshot_data['quantity'] * $snapshot->snapshot_data['price'];
                $totalValue += $value;
                $productValues[] = [
                    'product_id' => $snapshot->product_id,
                    'product_name' => $snapshot->product_name,
                    'quantity' => $snapshot->snapshot_data['quantity'],
                    'price' => $snapshot->snapshot_data['price'],
                    'total_value' => $value,
                ];
            }
        }

        return [
            'date' => $date->format('Y-m-d'),
            'total_value' => $totalValue,
            'products' => $productValues,
            'product_count' => count($productValues),
        ];
    }

    public static function getInventoryChanges(\DateTime $startDate, \DateTime $endDate): array
    {
        $archives = Archive::with(['product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->get();

        $changes = [];
        foreach ($archives as $archive) {
            $changes[] = [
                'date' => $archive->created_at->format('Y-m-d H:i:s'),
                'product_name' => $archive->product_name,
                'product_sku' => $archive->product_sku,
                'quantity' => $archive->quantity,
                'action' => $archive->action,
                'action_label' => $archive->action_label,
                'user' => $archive->user ? $archive->user->name : 'System',
                'notes' => $archive->notes,
            ];
        }

        return [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_changes' => count($changes),
            'changes' => $changes,
        ];
    }

    public static function cleanupOldArchives(int $keepCount = 50): int
    {
        $products = Product::all();
        $deleted = 0;

        foreach ($products as $product) {
            $archives = Archive::where('product_id', $product->id)
                ->orderBy('created_at', 'desc')
                ->get();

            if ($archives->count() > $keepCount) {
                $toDelete = $archives->slice($keepCount);
                foreach ($toDelete as $archive) {
                    $archive->delete();
                    $deleted++;
                }
            }
        }

        return $deleted;
    }
}
