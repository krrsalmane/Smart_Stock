<?php

use Illuminate\Support\Facades\Route;

// Import all your controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\MouvementController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommandController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DeliveryAgentController;
use App\Http\Controllers\RoleManagementController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
// Swagger Documentation
Route::get('/documentation', function () {
    return response()->file(storage_path('api-docs/swagger.json'));
});

Route::get('/docs', function () {
    return view('swagger-ui');
})->name('swagger.ui');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Requires JWT)
|--------------------------------------------------------------------------
*/
Route::middleware('jwt')->group(function () {
    
    // Common User Routes
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::put('/user', [AuthController::class, 'updateUser']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Routes for Clients (General Authenticated Users)
    Route::get('/commands', [CommandController::class, 'index']);
    Route::get('/commands/{id}', [CommandController::class, 'show']);
    Route::get('/commands/{id}/tracking', [CommandController::class, 'tracking']);
    Route::post('/commands', [CommandController::class, 'store']);
    Route::put('/commands/{id}', [CommandController::class, 'update']);
    Route::post('/commands/{id}/cancel', [CommandController::class, 'cancel']);
    
    // Products (viewable by all authenticated users for order creation)
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // Supplier CRUD Routes (accessible to authenticated users)
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::get('/suppliers/{id}', [SupplierController::class, 'show']);
    Route::post('/suppliers', [SupplierController::class, 'store']);
    Route::put('/suppliers/{id}', [SupplierController::class, 'update']);
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']);
    
    // Supplier-Product & Supplier-Command Relationships
    Route::post('/suppliers/{id}/products', [SupplierController::class, 'assignProducts']);
    Route::delete('/suppliers/{id}/products/{productId}', [SupplierController::class, 'detachProduct']);
    Route::post('/suppliers/{id}/commands', [SupplierController::class, 'assignCommand']);
    Route::delete('/suppliers/{id}/commands/{commandId}', [SupplierController::class, 'detachCommand']);
    
    // Supplier Workflow Endpoints (FS8)
    Route::put('/suppliers/{supplierId}/commands/{commandId}', [SupplierController::class, 'updateCommandStatus']);
    Route::post('/suppliers/{supplierId}/commands/{commandId}/receive', [SupplierController::class, 'updateCommandStatus']);
    Route::post('/suppliers/{supplierId}/commands/{commandId}/ship', [SupplierController::class, 'updateCommandDelivery']);
    Route::post('/suppliers/{supplierId}/commands/{commandId}/confirm', [SupplierController::class, 'confirmShipment']);

    /*
    |-- Magasinier Specific Routes --|
    */
    Route::middleware('magasinier')->group(function () {
        // Categories
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::get('/categories/{id}', [CategoryController::class, 'show']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

        // Warehouses (now includes DELETE)
        Route::get('/warehouses', [WarehouseController::class, 'index']);
        Route::post('/warehouses', [WarehouseController::class, 'store']);
        Route::get('/warehouses/{id}', [WarehouseController::class, 'show']);
        Route::put('/warehouses/{id}', [WarehouseController::class, 'update']);
        Route::delete('/warehouses/{id}', [WarehouseController::class, 'destroy']);

        // Mouvements
        Route::get('/mouvements', [MouvementController::class, 'index']);
        Route::post('/mouvements', [MouvementController::class, 'store']);
        Route::get('/mouvements/{id}', [MouvementController::class, 'show']);
        Route::put('/mouvements/{id}', [MouvementController::class, 'update']);

        // Products (create, update, delete - only for magasinier)
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        // Alerts
        Route::get('/alerts', [AlertController::class, 'index']);
        Route::get('/alerts/active/count', [AlertController::class, 'getActiveCount']);
        Route::get('/alerts/low-stock/list', [AlertController::class, 'getLowStockAlerts']);
        Route::get('/alerts/{id}', [AlertController::class, 'show']);
        Route::put('/alerts/{id}', [AlertController::class, 'update']);
        Route::delete('/alerts/{id}', [AlertController::class, 'destroy']);

        // Archives
        Route::get('/archives', [ArchiveController::class, 'index']);
        Route::get('/archives/{id}', [ArchiveController::class, 'show']);
        Route::post('/archives', [ArchiveController::class, 'store']);
        Route::get('/archives/statistics', [ArchiveController::class, 'getStatistics']);
        Route::get('/archives/historical-value', [ArchiveController::class, 'getHistoricalValue']);
        Route::get('/archives/changes', [ArchiveController::class, 'getChanges']);
        Route::post('/archives/cleanup', [ArchiveController::class, 'cleanup']);

        // Confirm delivery (Magasinier receives shipment from supplier)
        Route::put('/supplier-deliveries/{supplierId}/commands/{commandId}/confirm', [SupplierController::class, 'confirmShipment']);
        
        // Delivery Agent Assignment
        Route::get('/delivery-agents', [CommandController::class, 'getAvailableDeliveryAgents']);
        Route::post('/commands/{id}/assign-delivery-agent', [CommandController::class, 'assignDeliveryAgent']);
        Route::get('/commands/for-delivery', [CommandController::class, 'getCommandsForDelivery']);
        
        // Role Management (Magasinier)
        Route::get('/magasinier/assignable-roles', [RoleManagementController::class, 'getAssignableRoles']);
        Route::get('/magasinier/users-with-roles', [RoleManagementController::class, 'getAllUsersWithRoles']);
        Route::post('/magasinier/users/{userId}/assign-role', [RoleManagementController::class, 'assignRole']);
    });

    /*
    |-- Supplier Specific Routes (FS8: receiveCommand, sendDelivery) --|
    */
    Route::middleware('supplier')->group(function () {
        // Supplier sees commands assigned to them
        Route::get('/my-commands', [SupplierController::class, 'receiveCommand']);
        Route::get('/my-commands/{id}', [SupplierController::class, 'getCommands']);
        Route::put('/my-commands/{commandId}/decision', [SupplierController::class, 'decideCommand']);
        // Supplier marks a command as shipped
        Route::put('/my-commands/{commandId}/ship', [SupplierController::class, 'sendDelivery']);
        Route::put('/my-commands/{supplierId}/{commandId}/ship', [SupplierController::class, 'updateCommandDelivery']);
    });

    /*
    |-- Delivery Agent Specific Routes --|
    */
    Route::middleware('delivery_agent')->group(function () {
        // Delivery Agent CRUD Operations
        Route::get('/my-deliveries', [DeliveryAgentController::class, 'index']);
        Route::get('/my-deliveries/{id}', [DeliveryAgentController::class, 'show']);
        Route::put('/my-deliveries/{id}/start', [DeliveryAgentController::class, 'startDelivery']);
        Route::put('/my-deliveries/{id}/update-status', [DeliveryAgentController::class, 'updateStatus']);
        Route::put('/my-deliveries/{id}/complete', [DeliveryAgentController::class, 'completeDelivery']);
        
        // Movement Management - Delivery Agent updates movement status
        Route::post('/my-deliveries/{commandId}/movements', [DeliveryAgentController::class, 'updateMouvement']);
        Route::get('/my-deliveries/{commandId}/movements', [DeliveryAgentController::class, 'getMovements']);
        
        // Admin-only routes for delivery management
        Route::get('/deliveries/available', [DeliveryAgentController::class, 'getAvailableDeliveries']);
        Route::post('/commands/{commandId}/assign-agent/{agentId}', [DeliveryAgentController::class, 'assignDeliveryAgent']);
    });

    /*
    |-- Admin Specific Routes --|
    */
    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'index']);
        Route::apiResource('/users', UserController::class);
        
        // Role Management (Admin)
        Route::get('/roles/available', [RoleManagementController::class, 'getAvailableRoles']);
        Route::get('/roles/assignable', [RoleManagementController::class, 'getAssignableRoles']);
        Route::get('/admin/users-with-roles', [RoleManagementController::class, 'getAllUsersWithRoles']);
        Route::post('/users/{userId}/assign-role', [RoleManagementController::class, 'assignRole']);
        
        // Chart Data Endpoints
        Route::get('/reports/movement-chart', [ReportController::class, 'getMovementChartData']);
        Route::get('/reports/category-chart', [ReportController::class, 'getCategoryChartData']);
        Route::get('/reports/alerts-chart', [ReportController::class, 'getAlertsChartData']);
        
        // Report Export Endpoints
        Route::get('/reports/export/products', [ReportController::class, 'exportProductsCSV']);
        Route::get('/reports/export/movements', [ReportController::class, 'exportMovementsCSV']);
        Route::get('/reports/export/commands', [ReportController::class, 'exportCommandsCSV']);
        Route::get('/reports/export/inventory-summary', [ReportController::class, 'exportInventorySummary']);
    });

});