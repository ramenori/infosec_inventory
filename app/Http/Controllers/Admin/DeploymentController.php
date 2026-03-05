<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Category;
use App\Models\Deployment;
use App\Models\ContactPerson;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DeploymentController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all()->map(function($category) {
            $itemsCount = Inventory::where('category', $category->name)->count();
            $availableCount = Inventory::where('category', $category->name)
                ->where('status', 'Available')
                ->where('stock_qty', '>', 0)
                ->count();

            return (object) [
                'id'              => $category->id,
                'name'            => $category->name,
                'items_count'     => $itemsCount,
                'available_count' => $availableCount
            ];
        });

        // Get cart items from session
        $cartItems = collect(session('deployment_cart', []));

        if ($cartItems->isNotEmpty()) {
            $inventoryIds  = $cartItems->pluck('inventory_id')->toArray();
            $inventoryItems = Inventory::whereIn('id', $inventoryIds)->get()->keyBy('id');

            $cartItems = $cartItems->map(function($item) use ($inventoryItems) {
                $inventory = $inventoryItems[$item['inventory_id']] ?? null;
                if ($inventory) {
                    return (object) [
                        'cart_item_id' => $item['inventory_id'],
                        'inventory_id' => $item['inventory_id'],
                        'quantity'     => $item['quantity'],
                        'added_at'     => $item['added_at'] ?? now(),
                        'inventory'    => $inventory
                    ];
                }
                return null;
            })->filter();
        }

        // Get items for selected category
        $categoryItems       = collect();
        $selectedCategoryId  = session('selected_category');
        $selectedCategoryName = session('selected_category_name');

        if ($selectedCategoryId) {
            $selectedCategory = Category::find($selectedCategoryId);
            if ($selectedCategory) {
                $query = Inventory::where('category', $selectedCategory->name)
                    ->where('status', 'Available')
                    ->where('stock_qty', '>', 0);

                if ($request->filled('component_search')) {
                    $search = $request->component_search;
                    $query->where(function($q) use ($search) {
                        $q->where('component', 'like', "%{$search}%")
                          ->orWhere('serial_num', 'like', "%{$search}%")
                          ->orWhere('brand', 'like', "%{$search}%");
                    });
                }

                $categoryItems = $query->paginate(10);
            }
        }

        $contactPersons = ContactPerson::orderBy('name')
            ->get(['id', 'name', 'contact_number', 'address', 'satellite_office']);

        $recentDeployments = Deployment::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.deployment', compact(
            'categories',
            'cartItems',
            'categoryItems',
            'recentDeployments',
            'selectedCategoryId',
            'selectedCategoryName',
            'contactPersons'
        ));
    }

    public function selectCategory(Request $request)
    {
        $request->validate([
            'category_id'     => 'required|exists:categories,id',
            'category_name'   => 'required|string',
            'available_count' => 'required|integer'
        ]);

        session([
            'selected_category'           => $request->category_id,
            'selected_category_name'      => $request->category_name,
            'selected_category_available' => $request->available_count
        ]);

        return redirect()->route('admin.deployment')
            ->with('success', 'Category selected. Now choose components to deploy.');
    }

    public function clearCategory()
    {
        session()->forget([
            'selected_category',
            'selected_category_name',
            'selected_category_available'
        ]);

        return redirect()->route('admin.deployment')
            ->with('info', 'Category selection cleared.');
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'quantity'     => 'required|integer|min:1'
        ]);

        $inventory = Inventory::findOrFail($request->inventory_id);

        if ($inventory->status !== 'Available' || $inventory->stock_qty <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Item is not available for deployment.'
            ], 400);
        }

        if ($request->quantity > $inventory->stock_qty) {
            return response()->json([
                'success' => false,
                'message' => 'Quantity cannot exceed available stock.'
            ], 400);
        }

        $cart      = session()->get('deployment_cart', []);
        $itemIndex = null;

        foreach ($cart as $index => $item) {
            if ($item['inventory_id'] == $request->inventory_id) {
                $itemIndex = $index;
                break;
            }
        }

        if ($itemIndex !== null) {
            $newQuantity = $cart[$itemIndex]['quantity'] + $request->quantity;

            if ($newQuantity > $inventory->stock_qty) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add more than available stock.'
                ], 400);
            }

            $cart[$itemIndex]['quantity'] = $newQuantity;
        } else {
            $cart[] = [
                'inventory_id' => $request->inventory_id,
                'quantity'     => $request->quantity,
                'added_at'     => now()->toDateTimeString()
            ];
        }

        session()->put('deployment_cart', $cart);

        return response()->json([
            'success'    => true,
            'message'    => 'Item added to cart!',
            'cart_count' => count($cart)
        ]);
    }

    public function bulkAddToCart(Request $request)
    {
        $request->validate([
            'component_ids'   => 'required|array',
            'component_ids.*' => 'exists:inventories,id',
            'category_id'     => 'required|exists:categories,id'
        ]);

        $selectedCategory = Category::find($request->category_id);
        if (!$selectedCategory) {
            return redirect()->back()->with('error', 'Selected category not found.');
        }

        $cart       = session()->get('deployment_cart', []);
        $addedCount = 0;
        $errors     = [];

        foreach ($request->component_ids as $componentId) {
            $quantity = $request->input('quantities.' . $componentId, 1);

            if ($quantity < 1) continue;

            $inventory = Inventory::find($componentId);

            if (!$inventory) {
                $errors[] = "Item ID {$componentId} not found.";
                continue;
            }

            if ($inventory->category !== $selectedCategory->name) {
                $errors[] = "Item '{$inventory->component}' does not belong to selected category.";
                continue;
            }

            if ($inventory->status !== 'Available') {
                $errors[] = "Item '{$inventory->component}' is not available.";
                continue;
            }

            $existingInCart = 0;
            foreach ($cart as $item) {
                if ($item['inventory_id'] == $componentId) {
                    $existingInCart = $item['quantity'];
                    break;
                }
            }

            $totalNeeded = $existingInCart + $quantity;

            if ($totalNeeded > $inventory->stock_qty) {
                $errors[] = "Insufficient stock for '{$inventory->component}'. Available: {$inventory->stock_qty}, Already in cart: {$existingInCart}, Requested: {$quantity}";
                continue;
            }

            $itemIndex = null;
            foreach ($cart as $index => $item) {
                if ($item['inventory_id'] == $componentId) {
                    $itemIndex = $index;
                    break;
                }
            }

            if ($itemIndex !== null) {
                $cart[$itemIndex]['quantity'] = $totalNeeded;
            } else {
                $cart[] = [
                    'inventory_id' => $componentId,
                    'quantity'     => $quantity,
                    'added_at'     => now()->toDateTimeString()
                ];
            }

            $addedCount++;
        }

        session()->put('deployment_cart', $cart);
        session([
            'selected_category'      => $selectedCategory->id,
            'selected_category_name' => $selectedCategory->name
        ]);

        if ($addedCount > 0) {
            $message = "Successfully added {$addedCount} item(s) to cart!";
            if (!empty($errors)) {
                $message .= " Some items could not be added: " . implode(' ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= "... and " . (count($errors) - 3) . " more";
                }
            }
            return redirect()->route('admin.deployment')->with('success', $message);
        }

        return redirect()->back()
            ->with('error', 'No items were added to cart. ' . implode(' ', $errors))
            ->withInput();
    }

    public function removeFromCart($inventoryId)
    {
        $cart          = session()->get('deployment_cart', []);
        $originalCount = count($cart);

        foreach ($cart as $index => $item) {
            if ($item['inventory_id'] == $inventoryId) {
                unset($cart[$index]);
                break;
            }
        }

        $cart = array_values($cart);
        session()->put('deployment_cart', $cart);

        return count($cart) < $originalCount
            ? redirect()->back()->with('success', 'Item removed from cart!')
            : redirect()->back()->with('error', 'Item not found in cart!');
    }

    public function clearCart()
    {
        session()->forget('deployment_cart');
        return redirect()->back()->with('success', 'Cart cleared successfully!');
    }

    public function updateCart(Request $request, $inventoryId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart    = session()->get('deployment_cart', []);
        $updated = false;

        foreach ($cart as &$item) {
            if ($item['inventory_id'] == $inventoryId) {
                $inventory = Inventory::find($inventoryId);

                if (!$inventory) {
                    return redirect()->back()->with('error', 'Item not found!');
                }

                if ($request->quantity > $inventory->stock_qty) {
                    return redirect()->back()->with('error', 'Quantity cannot exceed available stock of ' . $inventory->stock_qty);
                }

                $item['quantity'] = $request->quantity;
                $updated          = true;
                break;
            }
        }

        if ($updated) {
            session()->put('deployment_cart', $cart);
            return redirect()->back()->with('success', 'Cart updated!');
        }

        return redirect()->back()->with('error', 'Item not found in cart!');
    }

    public function deploy(Request $request)
    {
        $request->validate([
            'waybill_number'   => 'nullable|string|max:255',
            'deployed_to'      => 'required|string|max:255',
            'contact_person_id'=> 'nullable|exists:contactperson,id',
            'contact_number'   => 'nullable|string|max:20',
            'address'          => 'nullable|string|max:500',
            'satellite_office' => 'nullable|string|max:255',
            'deployment_date'  => 'required|date',
            'remarks'          => 'nullable|string|max:500',
            'new_contact_name' => 'nullable|string|max:255',
            'new_contact_number' => 'nullable|string|max:20',
            'new_address'      => 'nullable|string|max:500',
            'new_satellite_office' => 'nullable|string|max:255'
        ]);

        $cartItems = session()->get('deployment_cart', []);

        if (empty($cartItems)) {
            return redirect()->back()->with('error', 'No items in cart to deploy.');
        }

        try {
            DB::beginTransaction();

            // Handle new contact person creation
            $contactPersonId = $request->contact_person_id;
            if ($request->filled('new_contact_name')) {
                $contactPerson = ContactPerson::create([
                    'name'             => $request->new_contact_name,
                    'contact_number'   => $request->new_contact_number,
                    'address'          => $request->new_address,
                    'satellite_office' => $request->new_satellite_office
                ]);
                $contactPersonId = $contactPerson->id;
            }

            foreach ($cartItems as $cartItem) {
                $inventory = Inventory::where('id', $cartItem['inventory_id'])
                    ->where('stock_qty', '>=', $cartItem['quantity'])
                    ->first();

                if (!$inventory) {
                    throw new \Exception("Item ID {$cartItem['inventory_id']} is no longer available in sufficient quantity.");
                }

                Deployment::create([
                    'user_id'          => Auth::id(),
                    'contact_person_id'=> $contactPersonId,
                    'deployed_to'      => $request->deployed_to,
                    'deployment_date'  => $request->deployment_date,
                    'remarks'          => $request->remarks,
                    'status'           => 'completed',
                    'waybill_number'   => $request->filled('waybill_number') ? $request->waybill_number : null,
                    'contact_number'   => $request->filled('contact_number') ? $request->contact_number : null,
                    'address'          => $request->filled('address') ? $request->address : null,
                    'satellite_office' => $request->filled('satellite_office') ? $request->satellite_office : null,
                    'inventory_id'     => $inventory->id,
                    'component'        => $inventory->component,
                    'quantity'         => $cartItem['quantity'],
                ]);

                $inventory->stock_qty -= $cartItem['quantity'];
                $inventory->status = $inventory->stock_qty <= 0 ? 'Out of Stock'
                    : ($inventory->stock_qty < 5 ? 'Low Stock' : 'Available');
                $inventory->save();
            }

            DB::commit();

            session()->forget(['deployment_cart', 'selected_category', 'selected_category_name', 'selected_category_available']);

            return redirect()->route('admin.deployment')
                ->with('success', 'Deployment completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Deployment failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $deployment = Deployment::with(['user', 'inventory'])->findOrFail($id);
        return view('admin.deployment_show', compact('deployment'));
    }

    public function history(Request $request)
    {
        $query = Deployment::with(['user', 'inventory']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('deployed_to', 'like', "%{$search}%")
                ->orWhere('component', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('deployment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('deployment_date', '<=', $request->date_to);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.reports', compact('reports'));
    }
}