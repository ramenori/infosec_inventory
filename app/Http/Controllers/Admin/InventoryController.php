<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Category;
use App\Models\Supplier;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::with(['category', 'supplier']);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('component', 'like', "%{$search}%")
                  ->orWhere('serial_num', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhereHas('category', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by category
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $inventory = $query->orderBy('created_at', 'desc')->paginate(10);
        $categories = Category::all();
        
        return view('admin.inventory', compact('inventory', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('admin.inventory_create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'component' => 'required|string|max:255',
            'serial_num' => 'nullable|string|max:255|unique:inventories,serial_num',
            'brand' => 'nullable|string|max:255',
            'stock_qty' => 'required|integer|min:0',
            'date_added' => 'required|date',
            'status' => 'required|in:Available,Low Stock,Out of Stock,Maintenance',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        // Set status based on stock quantity
        $status = $request->status;
        if ($request->stock_qty == 0) {
            $status = 'Out of Stock';
        } elseif ($request->stock_qty < 5) {
            $status = 'Low Stock';
        }

        Inventory::create([
            'category' => $request->category,
            'component' => $request->component,
            'serial_num' => $request->serial_num,
            'brand' => $request->brand,
            'stock_qty' => $request->stock_qty,
            'date_added' => $request->date_added,
            'status' => $status,
            'supplier_id' => $request->supplier_id,
        ]);

        return redirect()->route('admin.inventory')->with('success', 'Item added successfully!');
    }

    public function edit($id)
    {
        $inventory = Inventory::findOrFail($id);
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('admin.inventory_edit', compact('inventory', 'categories', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);

        $request->validate([
            'category' => 'required|string|max:255',
            'component' => 'required|string|max:255',
            'serial_num' => 'nullable|string|max:255|unique:inventories,serial_num,' . $id,
            'brand' => 'nullable|string|max:255',
            'stock_qty' => 'required|integer|min:0',
            'date_added' => 'required|date',
            'status' => 'required|in:Available,Low Stock,Out of Stock,Maintenance',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        // Auto-update status based on stock
        $status = $request->status;
        if ($request->stock_qty == 0) {
            $status = 'Out of Stock';
        } elseif ($request->stock_qty < 5) {
            $status = 'Low Stock';
        }

        $inventory->update([
            'category' => $request->category,
            'component' => $request->component,
            'serial_num' => $request->serial_num,
            'brand' => $request->brand,
            'stock_qty' => $request->stock_qty,
            'date_added' => $request->date_added,
            'status' => $status,
            'supplier_id' => $request->supplier_id,
        ]);

        return redirect()->route('admin.inventory')->with('success', 'Item updated successfully!');
    }

    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        
        // Check if item is used in deployments
        if ($inventory->deploymentItems()->exists()) {
            return redirect()->route('admin.inventory')->with('error', 'Cannot delete item that has deployment history!');
        }

        $inventory->delete();
        return redirect()->route('admin.inventory')->with('success', 'Item deleted successfully!');
    }

    // Quick stock update (for AJAX requests)
    public function updateStock(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);

        $request->validate([
            'stock_qty' => 'required|integer|min:0',
        ]);

        $inventory->stock_qty = $request->stock_qty;
        
        // Auto-update status
        if ($inventory->stock_qty == 0) {
            $inventory->status = 'Out of Stock';
        } elseif ($inventory->stock_qty < 5) {
            $inventory->status = 'Low Stock';
        } else {
            $inventory->status = 'Available';
        }

        $inventory->save();

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully',
            'data' => $inventory
        ]);
    }
}