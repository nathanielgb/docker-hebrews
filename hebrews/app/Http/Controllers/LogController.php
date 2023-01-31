<?php

namespace App\Http\Controllers;

use App\Models\Branch;
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

    public function showInventory (Request $request)
    {
        $logs = new InventoryLog;
        if ($request->except(['page'])) {
            $logs=$logs->where(function ($query) use ($request) {
                if ($request->module !== null) {
                    $query->whereJsonContains('data->module', $request->module);
                }
                if ($request->order_id !== null) {
                    $order_id = strtolower($request->order_id);
                    $query->whereRaw("LOWER(JSON_EXTRACT(data, '$.order_id')) like ?", '%' . $order_id . '%');
                }
                if ($request->branch_id !== null) {
                    $query->whereJsonContains('data->inventory->branch_id', $request->branch_id);
                }
                if ($request->inventory_name !== null) {
                    $inventory_name = strtolower($request->inventory_name);
                    $query->whereRaw("LOWER(JSON_EXTRACT(data, '$.inventory.name')) like ?", '%' . $inventory_name . '%');
                }
                if ($request->inventory_code !== null) {
                    $inventory_code = strtolower($request->inventory_code);
                    $query->whereRaw("LOWER(JSON_EXTRACT(data, '$.inventory.inventory_code')) like ?", '%' . $inventory_code . '%');
                }
            });
        }

        $logs = $logs->orderBy('created_at','DESC')->paginate(20);

        $branches = Branch::all();

        return view('logs.inventory.index', compact(
            'logs',
            'branches'
        ));
    }
}
