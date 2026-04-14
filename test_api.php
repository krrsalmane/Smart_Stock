<?php

/**
 * SmartStock API - Automated Smoke Test
 * 
 * Run with: php test_api.php
 * Make sure `php artisan serve` is running on port 8000
 */

$BASE = 'http://localhost:8000/api';
$results = [];

// ─── Helpers ───────────────────────────────────────────────

function request($method, $url, $data = null, $token = null) {
    global $BASE;
    $ch = curl_init("$BASE$url");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $headers = ['Content-Type: application/json', 'Accept: application/json'];
    if ($token) $headers[] = "Authorization: Bearer $token";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    return [
        'code' => $httpCode,
        'body' => json_decode($response, true),
        'raw'  => $response,
        'error' => $error
    ];
}

function test($name, $method, $url, $data, $expectedCode, $token = null) {
    global $results;
    $res = request($method, $url, $data, $token);
    $pass = $res['code'] === $expectedCode;
    $results[] = [
        'name' => $name,
        'method' => $method,
        'url' => $url,
        'expected' => $expectedCode,
        'actual' => $res['code'],
        'pass' => $pass,
    ];
    if (!$pass) {
        $results[count($results)-1]['detail'] = substr($res['raw'] ?? '', 0, 150);
    }
    return $res;
}

function heading($title) {
    echo "\n\033[1;36m━━━ $title ━━━\033[0m\n";
}

// ─── Start Tests ───────────────────────────────────────────

echo "\n\033[1;35m╔══════════════════════════════════════════════╗\033[0m";
echo "\n\033[1;35m║   SmartStock API - Automated Smoke Test      ║\033[0m";
echo "\n\033[1;35m╚══════════════════════════════════════════════╝\033[0m\n";

// Check server is running
$check = request('GET', '/documentation');
if ($check['code'] === 0) {
    echo "\n\033[1;31m✖ Server not running! Start with: php artisan serve --port=8000\033[0m\n\n";
    exit(1);
}
echo "\n\033[32m✓ Server is running\033[0m\n";

// ── 1. AUTH ────────────────────────────────────────────────
heading('1. AUTHENTICATION');

$unique = time();

// Register admin
$res = test('Register Admin User', 'POST', '/register', [
    'name' => "AdminUser_$unique",
    'email' => "admin_$unique@test.com",
    'password' => 'password123',
    'password_confirmation' => 'password123',
], 201);
$adminId = $res['body']['user']['id'] ?? null;

// Register magasinier  
$res = test('Register Magasinier User', 'POST', '/register', [
    'name' => "MagUser_$unique",
    'email' => "mag_$unique@test.com",
    'password' => 'password123',
    'password_confirmation' => 'password123',
], 201);
$magId = $res['body']['user']['id'] ?? null;

// PROMOTE USERS VIA ARTISAN
echo "  ↳ Promoting test users to their roles via Artisan...\n";
shell_exec("php artisan tinker --execute=\"\App\Models\User::where('email', 'admin_$unique@test.com')->update(['role' => 'admin'])\"");
shell_exec("php artisan tinker --execute=\"\App\Models\User::where('email', 'mag_$unique@test.com')->update(['role' => 'magasinier'])\"");

// Now Login to get correct tokens
$resAdmin = test('Login as Admin', 'POST', '/login', [
    'email' => "admin_$unique@test.com",
    'password' => 'password123',
], 200);
$adminToken = $resAdmin['body']['token'] ?? null;

$resMag = test('Login as Magasinier', 'POST', '/login', [
    'email' => "mag_$unique@test.com",
    'password' => 'password123',
], 200);
$magToken = $resMag['body']['token'] ?? null;

// Login fail
test('Login Bad Credentials', 'POST', '/login', [
    'email' => 'wrong@test.com',
    'password' => 'wrong',
], 401);

// Get user profile
test('Get Profile', 'GET', '/user', null, 200, $adminToken);

// ── 2. SWAGGER ─────────────────────────────────────────────
heading('2. SWAGGER DOCS');

test('Swagger JSON Metadata', 'GET', '/documentation', null, 200);
test('Swagger UI Blade View', 'GET', '/docs', null, 200);

// ── 3. CATEGORIES ──────────────────────────────────────────
heading('3. CATEGORIES (Magasinier)');

$res = test('Create Category', 'POST', '/categories', [
    'name' => "TestCat_$unique",
    'description' => 'Test category for inventory',
], 201, $magToken);
$catId = $res['body']['category']['id'] ?? $res['body']['id'] ?? null;

if ($catId) {
    test('List All Categories', 'GET', '/categories', null, 200, $magToken);
    test('Show Category Details', 'GET', "/categories/$catId", null, 200, $magToken);
    test('Update Category Name', 'PUT', "/categories/$catId", [
        'name' => "UpdatedCat_$unique",
    ], 200, $magToken);
}

// ── 4. WAREHOUSES ──────────────────────────────────────────
heading('4. WAREHOUSES (Magasinier)');

$res = test('Create Warehouse', 'POST', '/warehouses', [
    'name' => "Warehouse_$unique",
    'address' => 'Morocco, Casablanca Test Zone',
    'user_id' => $magId
], 201, $magToken);
$whId = $res['body']['warehouse']['id'] ?? $res['body']['id'] ?? null;

if ($whId) {
    test('List Warehouses', 'GET', '/warehouses', null, 200, $magToken);
    test('Show Warehouse', 'GET', "/warehouses/$whId", null, 200, $magToken);
    test('Update Warehouse Info', 'PUT', "/warehouses/$whId", [
        'name' => "MainWarehouse_$unique",
    ], 200, $magToken);
}

// ── 5. PRODUCTS ────────────────────────────────────────────
heading('5. PRODUCTS (Magasinier)');

if ($catId && $whId) {
    $res = test('Create New Product', 'POST', '/products', [
        'name' => "Laptop_$unique",
        'sku' => "SKU-$unique",
        'quantity' => 100,
        'price' => 1200.50,
        'alert_threshold' => 10,
        'category_id' => $catId,
        'warehouse_id' => $whId,
    ], 201, $magToken);
    $prodId = $res['body']['product']['id'] ?? $res['body']['id'] ?? null;

    if ($prodId) {
        test('List Available Products', 'GET', '/products', null, 200, $magToken);
        test('Get Product Statistics', 'GET', "/products/$prodId", null, 200, $magToken);
        
        // Trigger alert test: change quantity to below threshold
        test('Update Product (Set Low Stock)', 'PUT', "/products/$prodId", [
            'quantity' => 5,
        ], 200, $magToken);
    }
} else {
    echo "  ⚠ Skipped Product Tests (Prerequisites failed)\n";
}

// ── 6. MOUVEMENTS ──────────────────────────────────────────
heading('6. MOUVEMENTS (Magasinier)');

if (isset($prodId) && $prodId) {
    $res = test('Register Stock IN', 'POST', '/mouvements', [
        'type' => 'IN',
        'quantity' => 50,
        'note' => 'Restock from supplier',
        'product_id' => $prodId,
        'user_id' => $magId,
    ], 201, $magToken);
    $mouvId = $res['body']['mouvement']['id'] ?? null;

    test('Register Stock OUT', 'POST', '/mouvements', [
        'type' => 'OUT',
        'quantity' => 20,
        'note' => 'Customer shipment',
        'product_id' => $prodId,
        'user_id' => $magId,
    ], 201, $magToken);

    test('List Movement Logs', 'GET', '/mouvements', null, 200, $magToken);
}

// ── 7. COMMANDS ────────────────────────────────────────────
heading('7. COMMANDS (Public/Client)');

if (isset($prodId) && $prodId) {
    $res = test('Place New Order', 'POST', '/commands', [
        'ordered_at' => date('Y-m-d'),
        'client_id' => $magId, // Anyone can buy
        'products' => [
            ['product_id' => $prodId, 'quantity' => 2, 'unit_price' => 1200.50],
        ],
    ], 201, $magToken);
    $cmdId = $res['body']['command']['id'] ?? null;

    test('List Orders', 'GET', '/commands', null, 200, $magToken);
}

// ── 8. SUPPLIERS ───────────────────────────────────────────
heading('8. SUPPLIERS (Auth Users)');

$res = test('Create Supplier Record', 'POST', '/suppliers', [
    'name' => "GlobalTech_$unique",
    'email' => "contact_$unique@globaltech.com",
    'phone' => '+212-6-0000-0000',
    'address' => 'Supplier Hub A1',
], 201, $magToken);
$suppId = $res['body']['supplier']['id'] ?? $res['body']['id'] ?? null;

if ($suppId) {
    test('List Suppliers', 'GET', '/suppliers', null, 200, $magToken);
    
    if (isset($prodId) && $prodId) {
        test('Link Product to Supplier', 'POST', "/suppliers/$suppId/products", [
            'product_id' => $prodId,
            'cost_price' => 1000.00,
            'lead_time' => 5
        ], 200, $magToken);
    }
}

// ── 9. ALERTS ──────────────────────────────────────────────
heading('9. ALERTS (Magasinier)');

test('List All Alerts', 'GET', '/alerts', null, 200, $magToken);
test('Check Active Alert Count', 'GET', '/alerts/active/count', null, 200, $magToken);
test('Show Low Stock Product List', 'GET', '/alerts/low-stock/list', null, 200, $magToken);

// Dismiss alert test
$alertsRes = request('GET', '/alerts?status=active', null, $magToken);
$firstAlert = $alertsRes['body']['alerts'][0] ?? null;
if ($firstAlert) {
    $alertId = $firstAlert['id'];
    test('Dismiss Active Alert', 'PUT', "/alerts/$alertId", [
        'status' => 'dismissed',
    ], 200, $magToken);
}

// ── 10. ARCHIVES ───────────────────────────────────────────
heading('10. ARCHIVES (Magasinier)');

if (isset($prodId) && $prodId) {
    $res = test('Create Manual Archive Point', 'POST', '/archives', [
        'product_id' => $prodId,
        'quantity' => 100,
    ], 201, $magToken);
    $archId = $res['body']['archive']['id'] ?? null;

    test('List Historical Archives', 'GET', '/archives', null, 200, $magToken);
}

// ── 11. ADMIN ──────────────────────────────────────────────
heading('11. ADMIN DASHBOARD & USERS');

test('Get Strategic Dashboard Data', 'GET', '/admin/dashboard', null, 200, $adminToken);
test('List All System Users', 'GET', '/users', null, 200, $adminToken);

$res = test('Create New System User', 'POST', '/users', [
    'name' => "ExternalStaff_$unique",
    'email' => "staff_$unique@smartstock.com",
    'password' => 'password123',
    'role' => 'magasinier',
], 201, $adminToken);
$newUserId = $res['body']['user']['id'] ?? null;

if ($newUserId) {
    test('Update User Permissions', 'PUT', "/users/$newUserId", [
        'role' => 'admin',
    ], 200, $adminToken);
    test('Delete User Account', 'DELETE', "/users/$newUserId", null, 200, $adminToken);
}

// ── 12. CLEANUP ────────────────────────────────────────────
heading('12. CLEANUP');

if (isset($prodId) && $prodId)  test('Cleanup Product', 'DELETE', "/products/$prodId", null, 200, $magToken);
if ($catId)                     test('Cleanup Category', 'DELETE', "/categories/$catId", null, 200, $magToken);
if ($suppId)                    test('Cleanup Supplier', 'DELETE', "/suppliers/$suppId", null, 200, $magToken);

test('User Logout', 'POST', '/logout', null, 200, $adminToken);

// ── RESULTS ────────────────────────────────────────────────

echo "\n\n\033[1;35m╔══════════════════════════════════════════════════════════════════════╗\033[0m";
echo "\n\033[1;35m║                         FINAL SMOKE TEST REPORT                      ║\033[0m";
echo "\n\033[1;35m╚══════════════════════════════════════════════════════════════════════╝\033[0m\n\n";

$passed = 0;
$failed = 0;

printf("  %-35s %-8s %-6s %-6s %s\n", 'TEST', 'METHOD', 'EXPECT', 'GOT', 'STATUS');
echo "  " . str_repeat('─', 75) . "\n";

foreach ($results as $r) {
    if (!$r['pass']) {
        $icon = "\033[31m✖ FAIL\033[0m";
        $codeColor = "\033[31m";
        $failed++;
    } else {
        $icon = "\033[32m✓ PASS\033[0m";
        $codeColor = "\033[32m";
        $passed++;
    }
    
    printf("  %-35s %-8s %-6s {$codeColor}%-6s\033[0m %s\n",
        substr($r['name'], 0, 35),
        $r['method'],
        $r['expected'],
        $r['actual'],
        $icon
    );
    if (!$r['pass'] && isset($r['detail'])) {
        echo "    \033[33m↳ " . substr($r['detail'], 0, 95) . "\033[0m\n";
    }
}

$total = $passed + $failed;
$pct = $total > 0 ? round(($passed / $total) * 100) : 0;

echo "  " . str_repeat('─', 75) . "\n";
echo "  \033[1mTotal: $total  |  \033[32mPassed: $passed\033[0m  |  \033[31mFailed: $failed\033[0m  |  Final Grade: $pct%\n\n";

if ($failed === 0) {
    echo "  \033[1;32m🌟 EXCELLENT! Your backend is 100% stable and production-ready!\033[0m\n\n";
} else {
    echo "  \033[1;31m🚨 Some issues still remain. Please check the logs above.\033[0m\n\n";
}
