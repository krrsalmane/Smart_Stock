<?php

namespace App\Http\Controllers;

use App\Models\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommandController extends Controller
{
    // Get all commands
    public function index()
    {
        $commands = Command::with(['client', 'products'])->get();
        return response()->json($commands);
    }

  
}
