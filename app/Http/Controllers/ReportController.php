<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Mouvement;
use App\Models\Alert;
use App\Models\Category;
use App\Models\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Get movement chart data (last 7 days)
     */
    public function getMovementChartData()
    {
        $days = 7;
        $labels = [];
        $inData = [];
        $outData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('M d');
            $labels[] = $dateStr;

            // Get IN movements for this day
            $inCount = Mouvement::where('type', 'IN')
                ->whereDate('created_at', $date)
                ->sum('quantity');
            $inData[] = $inCount;

            // Get OUT movements for this day
            $outCount = Mouvement::where('type', 'OUT')
                ->whereDate('created_at', $date)
                ->sum('quantity');
            $outData[] = $outCount;
        }

        return response()->json([
            'labels' => $labels,
            'in_data' => $inData,
            'out_data' => $outData
        ]);
    }

    /**
     * Get inventory by category chart data
     */
    public function getCategoryChartData()
    {
        $categories = Category::with(['products' => function($query) {
            $query->select('id', 'category_id', 'quantity', 'price');
        }])->get();

        $labels = [];
        $values = [];

        foreach ($categories as $category) {
            $totalValue = $category->products->sum(function($product) {
                return $product->quantity * $product->price;
            });

            if ($totalValue > 0) {
                $labels[] = $category->name;
                $values[] = $totalValue;
            }
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values
        ]);
    }

    /**
     * Get alerts trend chart data (last 7 days)
     */
    public function getAlertsChartData()
    {
        $days = 7;
        $labels = [];
        $values = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('M d');
            $labels[] = $dateStr;

            // Count active alerts at end of each day
            $alertCount = Alert::whereDate('created_at', '<=', $date)
                ->where(function($query) use ($date) {
                    $query->whereNull('resolved_at')
                          ->orWhere('resolved_at', '>', $date);
                })
                ->where('status', 'active')
                ->count();
            
            $values[] = $alertCount;
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values
        ]);
    }

    /**
     * Export products report as CSV
     */
    public function exportProductsCSV()
    {
        $products = Product::with(['category', 'warehouse'])->get();

        $filename = 'products-report-' . now()->format('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['ID', 'Name', 'SKU', 'Quantity', 'Price', 'Alert Threshold', 'Category', 'Warehouse', 'Created At']);
            
            // Data
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->sku,
                    $product->quantity,
                    $product->price,
                    $product->alert_threshold,
                    $product->category ? $product->category->name : 'N/A',
                    $product->warehouse ? $product->warehouse->name : 'N/A',
                    $product->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export movements report as CSV
     */
    public function exportMovementsCSV()
    {
        $movements = Mouvement::with(['product', 'user'])->orderBy('created_at', 'desc')->get();

        $filename = 'movements-report-' . now()->format('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($movements) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['ID', 'Type', 'Product', 'Quantity', 'User', 'Note', 'Created At']);
            
            // Data
            foreach ($movements as $movement) {
                fputcsv($file, [
                    $movement->id,
                    $movement->type,
                    $movement->product ? $movement->product->name : 'N/A',
                    $movement->quantity,
                    $movement->user ? $movement->user->name : 'System',
                    $movement->note ?? '',
                    $movement->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export commands report as CSV
     */
    public function exportCommandsCSV()
    {
        $commands = Command::with(['client', 'products'])->orderBy('created_at', 'desc')->get();

        $filename = 'commands-report-' . now()->format('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($commands) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['ID', 'Client', 'Status', 'Type', 'Total Cost', 'Products Count', 'Ordered At', 'Expected At', 'Created At']);
            
            // Data
            foreach ($commands as $command) {
                fputcsv($file, [
                    $command->id,
                    $command->client ? $command->client->name : 'N/A',
                    $command->status,
                    $command->command_type ?? 'N/A',
                    $command->total_cost,
                    $command->products->count(),
                    $command->ordered_at,
                    $command->expected_at ?? 'N/A',
                    $command->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export inventory summary report
     */
    public function exportInventorySummary()
    {
        $products = Product::with(['category', 'warehouse'])->get();
        
        $totalValue = $products->sum(function($product) {
            return $product->quantity * $product->price;
        });
        
        $totalItems = $products->sum('quantity');
        $lowStockCount = $products->filter(function($product) {
            return $product->quantity < $product->alert_threshold;
        })->count();

        $filename = 'inventory-summary-' . now()->format('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($products, $totalValue, $totalItems, $lowStockCount) {
            $file = fopen('php://output', 'w');
            
            // Summary section
            fputcsv($file, ['INVENTORY SUMMARY REPORT']);
            fputcsv($file, ['Generated:', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, []);
            fputcsv($file, ['Total Products', $products->count()]);
            fputcsv($file, ['Total Items in Stock', $totalItems]);
            fputcsv($file, ['Total Inventory Value', '$' . number_format($totalValue, 2)]);
            fputcsv($file, ['Low Stock Alerts', $lowStockCount]);
            fputcsv($file, []);
            
            // Detailed section
            fputcsv($file, ['DETAILED INVENTORY']);
            fputcsv($file, ['Name', 'SKU', 'Category', 'Warehouse', 'Quantity', 'Price', 'Total Value', 'Status']);
            
            foreach ($products as $product) {
                $status = $product->quantity < $product->alert_threshold ? 'LOW STOCK' : 'OK';
                fputcsv($file, [
                    $product->name,
                    $product->sku,
                    $product->category ? $product->category->name : 'N/A',
                    $product->warehouse ? $product->warehouse->name : 'N/A',
                    $product->quantity,
                    '$' . number_format($product->price, 2),
                    '$' . number_format($product->quantity * $product->price, 2),
                    $status
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
