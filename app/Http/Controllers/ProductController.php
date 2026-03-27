<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    
        public function index()
    {
        $products = Product::with(['category', 'warehouse'])->get();
        return response()->json($products);
    }

    

}
