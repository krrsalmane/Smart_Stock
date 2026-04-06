<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    // Get all warehouses
    public function index()
    {
        $warehouses = Warehouse::with('products')->get();
        return response()->json($warehouses);
    }

   

    // Get a specific warehouse
  

   
}
