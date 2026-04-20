<?php

echo "\n=== SMARTSTOCK COMPREHENSIVE TEST SUITE ===\n\n";

$testResults = [];

// Test 1: Registration
echo "Testing User Registration...\n";
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://localhost:8000/api/register',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
    CURLOPT_POSTFIELDS => json_encode([
        'name' => 'TestUser_' . time(),
        'email' => 'test_' . time() . '@test.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'client'
    ]),
    CURLOPT_TIMEOUT => 10
));
$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($httpCode == 200 || $httpCode == 201) {
    $testResults['registration'] = "✓ PASS (HTTP $httpCode)";
    $data = json_decode($response, true);
    if (isset($data['token'])) {
        $testResults['token'] = "✓ Token received";
        $token = $data['token'];
    } else {
        $testResults['token'] = "✗ No token in response";
    }
} else {
    $testResults['registration'] = "✗ FAIL (HTTP $httpCode)";
}

// Test 2: Get Categories
echo "Testing GET Categories...\n";
$curl = curl_init();
$headers = array('Content-Type: application/json');
if (isset($token)) {
    $headers[] = "Authorization: Bearer $token";
}
curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://localhost:8000/api/categories',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_TIMEOUT => 10
));
$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
$testResults['categories'] = ($httpCode == 200) ? "✓ PASS (HTTP $httpCode)" : "✗ FAIL (HTTP $httpCode)";

// Test 3: Get Products
echo "Testing GET Products...\n";
$curl = curl_init();
$headers = array('Content-Type: application/json');
if (isset($token)) {
    $headers[] = "Authorization: Bearer $token";
}
curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://localhost:8000/api/products',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_TIMEOUT => 10
));
$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
$testResults['products'] = ($httpCode == 200) ? "✓ PASS (HTTP $httpCode)" : "✗ FAIL (HTTP $httpCode)";

// Test 4: Get Commands
echo "Testing GET Commands...\n";
$curl = curl_init();
$headers = array('Content-Type: application/json');
if (isset($token)) {
    $headers[] = "Authorization: Bearer $token";
}
curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://localhost:8000/api/commands',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_TIMEOUT => 10
));
$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
$testResults['commands'] = ($httpCode == 200) ? "✓ PASS (HTTP $httpCode)" : "✗ FAIL (HTTP $httpCode)";

// Test 5: Frontend Pages
echo "Testing Frontend Pages...\n";
$pages = ['/orders', '/archives'];
foreach ($pages as $page) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://localhost:8000$page",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10
    ));
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    $testResults["frontend_$page"] = ($httpCode == 200) ? "✓ PASS" : "✗ FAIL (HTTP $httpCode)";
}

// Display Results
echo "\n=== TEST RESULTS ===\n\n";
$passed = 0;
$failed = 0;
foreach ($testResults as $name => $result) {
    echo "[" . substr($result, 0, 1) . "] $name: $result\n";
    if (substr($result, 0, 1) === "✓") $passed++;
    else $failed++;
}

echo "\n=== SUMMARY ===\n";
echo "Passed: $passed\nFailed: $failed\nTotal: " . ($passed + $failed) . "\n";
echo "Status: " . ($failed === 0 ? "✓ ALL TESTS PASSED" : "✗ SOME TESTS FAILED") . "\n\n";

?>
