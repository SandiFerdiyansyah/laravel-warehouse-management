<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::withCount('products')->with('products.category');
        
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('contact_person', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'like', '%' . $searchTerm . '%');
        }
        
        $suppliers = $query->orderBy('name')->paginate(15);
        
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'email' => 'nullable|email|max:255',
            // Account creation fields
            'account_email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            // Get supplier role
            $supplierRole = Role::where('name', 'supplier')->first();
            
            if (!$supplierRole) {
                return back()->with('error', 'Supplier role not found. Please contact administrator.');
            }

            // Create user account
            $user = User::create([
                'name' => $request->name,
                'email' => $request->account_email,
                'password' => Hash::make($request->password),
                'role_id' => $supplierRole->id,
            ]);

            // Create supplier profile linked to user
            $supplier = Supplier::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'contact_person' => $request->contact_person,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            return redirect()->route('admin.suppliers.index')
                ->with('success', 'Supplier created successfully. Account email: ' . $request->account_email);
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating supplier: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Supplier $supplier)
    {
        $supplier->load('products');
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('products.category');
        return view('admin.suppliers.show', compact('supplier'));
    }

    public function details(Supplier $supplier)
    {
        $supplier->load('products.category');
        return response()->json($supplier);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $supplier->update($request->all());

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->products()->count() > 0) {
            return back()->with('error', 'Cannot delete supplier with products.');
        }

        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}