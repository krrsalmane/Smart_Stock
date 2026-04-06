<?php

namespace App\Http\Controllers;

use App\Models\Mouvement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MouvementController extends Controller
{
    // Get all mouvements
    public function index()
    {
        $mouvements = Mouvement::with(['product', 'command', 'user'])->get();
        return response()->json($mouvements);
    }

   

    
}
