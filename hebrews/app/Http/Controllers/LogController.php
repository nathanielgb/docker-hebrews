<?php

namespace App\Http\Controllers;

use App\Models\InventoryLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    //

    public function index (Request $request)
    {
        $inventory_log = InventoryLog::orderBy('created_at','asc')->get();

        return response()->json($inventory_log, 400);

        dd($inventory_log);
    }
}
