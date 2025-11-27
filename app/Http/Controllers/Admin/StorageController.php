<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StorageLocation;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    public function index()
    {
        $locations = StorageLocation::orderBy('location_code')->paginate(20);
        return view('admin.storage.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.storage.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'location_code' => 'required|string|max:20|unique:storage_locations,location_code',
            'capacity' => 'required|integer|min:1',
            'is_filled' => 'boolean',
        ]);

        StorageLocation::create($request->all());

        return redirect()->route('admin.storage.index')
            ->with('success', 'Storage location created successfully.');
    }

    public function edit(StorageLocation $storageLocation)
    {
        return view('admin.storage.edit', compact('storageLocation'));
    }

    public function update(Request $request, StorageLocation $storageLocation)
    {
        $request->validate([
            'location_code' => 'required|string|max:20|unique:storage_locations,location_code,' . $storageLocation->id,
            'capacity' => 'required|integer|min:1',
            'is_filled' => 'boolean',
        ]);

        $storageLocation->update($request->all());

        return redirect()->route('admin.storage.index')
            ->with('success', 'Storage location updated successfully.');
    }

    public function destroy(StorageLocation $storageLocation)
    {
        $storageLocation->delete();

        return redirect()->route('admin.storage.index')
            ->with('success', 'Storage location deleted successfully.');
    }
}