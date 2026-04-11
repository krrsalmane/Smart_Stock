<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Get all suppliers
     */
    public function index()
    {
        $suppliers = Supplier::with(['products', 'commands'])->get();

        return response()->json([
            'message' => 'Suppliers retrieved successfully',
            'suppliers' => $suppliers
        ]);
    }


  

  
  
   
}
