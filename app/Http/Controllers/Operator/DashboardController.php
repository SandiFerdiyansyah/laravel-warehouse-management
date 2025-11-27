<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\StorageLocation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $storageLocations = StorageLocation::orderBy('location_code')->get();
        
        return view('operator.dashboard', compact('storageLocations'));
    }

    public function filterLocations($status)
    {
        if ($status === 'filled') {
            $locations = StorageLocation::where('is_filled', true)->orderBy('location_code')->get();
        } elseif ($status === 'empty') {
            $locations = StorageLocation::where('is_filled', false)->orderBy('location_code')->get();
        } else {
            $locations = StorageLocation::orderBy('location_code')->get();
        }

        return view('operator.partials.location_list', compact('locations'));
    }
}