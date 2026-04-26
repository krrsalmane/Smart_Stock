<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to login explicitly
Route::get('/', function () {
    return redirect('/login');
});

// The UI Login shell
Route::get('/login', function () {
    return view('auth.login');
});

// The UI Register shell
Route::get('/register', function () {
    return view('auth.register');
});

// The UI Dashboard shell
Route::get('/dashboard', function () {
    return view('dashboard');
});

// Products shell
Route::get('/products', function () {
    return view('products'); 
});

// Categories shell
Route::get('/categories', function () {
    return view('categories'); 
});

// Warehouses shell
Route::get('/warehouses', function () {
    return view('warehouses'); 
});

// Archives shell (FS10)
Route::get('/archives', function () {
    return view('archives');
});

// Order Tracking shell (FS7)
Route::get('/orders', function () {
    return view('order-tracking');
});

// Movements shell
Route::get('/mouvements', function () {
    return view('mouvements'); 
});

// Commands shell
Route::get('/commands', function () {
    return view('commands'); 
});

// Suppliers shell
Route::get('/suppliers', function () {
    return view('suppliers'); 
});

// Alerts shell
Route::get('/alerts', function () {
    return view('alerts'); 
});

// Supplier Dashboard shell
Route::get('/supplier-dashboard', function () {
    return view('supplier-dashboard'); 
});

// Supplier Portal shell (for users with supplier role)
Route::get('/supplier-portal', function () {
    return view('supplier-portal');
});

// Delivery Agent Dashboard shell
Route::get('/delivery-agent/dashboard', function () {
    return view('delivery-agent-dashboard');
});
