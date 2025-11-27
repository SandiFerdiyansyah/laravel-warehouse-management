<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::with('user')->paginate(20);
        return view('admin.stores.index', compact('stores'));
    }

    public function create()
    {
        return view('admin.stores.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'contact_person' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        // Get store role
        $storeRole = Role::where('name', 'store')->first();
        if (!$storeRole) {
            return redirect()->back()->with('error', 'Store role not found');
        }

        // Create user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $storeRole->id,
        ]);

        // Create store profile
        Store::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'contact_person' => $data['contact_person'],
            'phone' => $data['phone'],
            'address' => $data['address'],
        ]);

        return redirect()->route('admin.stores.index')->with('success', 'Store created successfully');
    }

    public function edit(Store $store)
    {
        return view('admin.stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'contact_person' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $store->update($data);
        $store->user->update(['name' => $data['name']]);

        return redirect()->route('admin.stores.index')->with('success', 'Store updated successfully');
    }

    public function destroy(Store $store)
    {
        $store->user->delete(); // Deletes store via cascade
        return redirect()->route('admin.stores.index')->with('success', 'Store deleted successfully');
    }
}
