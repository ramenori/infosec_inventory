<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Category;
use App\Models\Deployment;
use App\Models\DeploymentCart;
use App\Models\ContactPerson;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DeploymentController extends Controller
{
    public function index(Request $request)
    {
        // Get categories with item counts
        $categories = Category::all()->map(function($category) {
            $itemsCount = Inventory::where('category', $category->name)->count();
            $availableCount = Inventory::where('category', $category->name)
                ->where('status', 'Available')
                ->where('stock_qty', '>', 0)
                ->count();
            
            return (object) [
                'id' => $category->id,
                'name' => $category->name,
                'items_count' => $itemsCount,
                'available_count' => $availableCount
            ];
        });

        // Get cart items from session
        $cartItems = collect(session('deployment_cart', []));
        
        // Load inventory details for cart items
        if ($cartItems->isNotEmpty()) {
            $inventoryIds = $cartItems->pluck('inventory_id')->toArray();
            $inventoryItems = Inventory::whereIn('id', $inventoryIds)->get()->keyBy('id');
            
            $cartItems = $cartItems->map(function($item) use ($inventoryItems) {
                $inventory = $inventoryItems[$item['inventory_id']] ?? null;
                if ($inventory) {
                    return (object) [
                        'cart_item_id' => $item['inventory_id'],
                        'inventory_id' => $item['inventory_id'],
                        'quantity' => $item['quantity'],
                        'added_at' => $item['added_at'] ?? now(),
                        'inventory' => $inventory
                    ];
                }
                return null;
            })->filter();
        }

        // Get items for selected category
        $categoryItems = collect();
        $selectedCategoryId = session('selected_category');
        $selectedCategoryName = session('selected_category_name');
        
        if ($selectedCategoryId) {
            $selectedCategory = Category::find($selectedCategoryId);
            if ($selectedCategory) {
                $query = Inventory::where('category', $selectedCategory->name)
                    ->where('status', 'Available')
                    ->where('stock_qty', '>', 0);

                if ($request->has('component_search') && !empty($request->component_search)) {
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

        // ADD THIS: Get contact persons for dropdown
        $contactPersons = ContactPerson::orderBy('name')->get(['id', 'name', 'contact_number', 'address', 'satellite_office']);

        $recentDeployments = Deployment::with(['user', 'items'])
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
            'category_id' => 'required|exists:categories,id',
            'category_name' => 'required|string',
            'available_count' => 'required|integer'
        ]);

        // Store selected category in session
        session([
            'selected_category' => $request->category_id,
            'selected_category_name' => $request->category_name,
            'selected_category_available' => $request->available_count
        ]);

        return redirect()->route('admin.deployment')
            ->with('success', 'Category selected. Now choose components to deploy.');
    }

    public function clearCategory()
    {
        // Clear selected category from session
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
            'quantity' => 'required|integer|min:1'
        ]);

        $inventory = Inventory::findOrFail($request->inventory_id);

        // Check if item is available
        if ($inventory->status !== 'Available' || $inventory->stock_qty <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Item is not available for deployment.'
            ], 400);
        }

        // Check if quantity doesn't exceed available stock
        if ($request->quantity > $inventory->stock_qty) {
            return response()->json([
                'success' => false,
                'message' => 'Quantity cannot exceed available stock.'
            ], 400);
        }

        // Get current cart from session
        $cart = session()->get('deployment_cart', []);

        // Check if item already in cart
        $itemIndex = null;
        foreach ($cart as $index => $item) {
            if ($item['inventory_id'] == $request->inventory_id) {
                $itemIndex = $index;
                break;
            }
        }

        if ($itemIndex !== null) {
            // Update quantity for existing item
            $newQuantity = $cart[$itemIndex]['quantity'] + $request->quantity;
            
            // Check if new total doesn't exceed stock
            if ($newQuantity > $inventory->stock_qty) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add more than available stock.'
                ], 400);
            }
            
            $cart[$itemIndex]['quantity'] = $newQuantity;
        } else {
            // Add new item to cart
            $cart[] = [
                'inventory_id' => $request->inventory_id,
                'quantity' => $request->quantity,
                'added_at' => now()->toDateTimeString()
            ];
        }

        // Save cart to session
        session()->put('deployment_cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart!',
            'cart_count' => count($cart)
        ]);
    }

    public function bulkAddToCart(Request $request)
    {
        $request->validate([
            'component_ids' => 'required|array',
            'component_ids.*' => 'exists:inventories,id',
            'category_id' => 'required|exists:categories,id'
        ]);

        $selectedCategory = Category::find($request->category_id);
        if (!$selectedCategory) {
            return redirect()->back()->with('error', 'Selected category not found.');
        }

        $cart = session()->get('deployment_cart', []);
        $addedCount = 0;
        $errors = [];

        foreach ($request->component_ids as $componentId) {
            $quantityKey = 'quantities.' . $componentId;
            $quantity = $request->input($quantityKey, 1);
            
            if ($quantity < 1) continue;

            $inventory = Inventory::find($componentId);

            // Verify item belongs to selected category
            if (!$inventory) {
                $errors[] = "Item ID {$componentId} not found.";
                continue;
            }

            if ($inventory->category !== $selectedCategory->name) {
                $errors[] = "Item '{$inventory->component}' does not belong to selected category.";
                continue;
            }

            // Check availability
            if ($inventory->status !== 'Available') {
                $errors[] = "Item '{$inventory->component}' is not available.";
                continue;
            }

            // Check if item already in cart to calculate total needed
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

            // Update or add to cart
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
                    'quantity' => $quantity,
                    'added_at' => now()->toDateTimeString()
                ];
            }
            
            $addedCount++;
        }

        // Save cart to session
        session()->put('deployment_cart', $cart);

        // Preserve selected category in session
        session([
            'selected_category' => $selectedCategory->id,
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
            return redirect()->route('admin.deployment')
                ->with('success', $message);
        }

        return redirect()->back()
            ->with('error', 'No items were added to cart. ' . implode(' ', $errors))
            ->withInput();
    }

    public function removeFromCart($inventoryId)
    {
        $cart = session()->get('deployment_cart', []);
        $originalCount = count($cart);
        
        // Find and remove the item
        foreach ($cart as $index => $item) {
            if ($item['inventory_id'] == $inventoryId) {
                unset($cart[$index]);
                break;
            }
        }

        // Re-index array
        $cart = array_values($cart);
        
        // Save cart to session
        session()->put('deployment_cart', $cart);

        if (count($cart) < $originalCount) {
            return redirect()->back()->with('success', 'Item removed from cart!');
        }

        return redirect()->back()->with('error', 'Item not found in cart!');
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

        $cart = session()->get('deployment_cart', []);
        $updated = false;

        foreach ($cart as &$item) {
            if ($item['inventory_id'] == $inventoryId) {
                $inventory = Inventory::find($inventoryId);
                
                if (!$inventory) {
                    return redirect()->back()->with('error', 'Item not found!');
                }

                // Check if quantity is valid
                if ($request->quantity > $inventory->stock_qty) {
                    return redirect()->back()->with('error', 'Quantity cannot exceed available stock of ' . $inventory->stock_qty);
                }

                $item['quantity'] = $request->quantity;
                $updated = true;
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
            'waybill_number' => 'nullable|string|max:255', // CHANGED: not required
            'deployed_to' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20', // CHANGED: not required
            'address' => 'nullable|string|max:500', // CHANGED: not required
            'satellite_office' => 'nullable|string|max:255', // CHANGED: not required
            'deployment_date' => 'required|date',
            'remarks' => 'nullable|string|max:500'
        ]);

        $cartItems = session()->get('deployment_cart', []);

        if (empty($cartItems)) {
            return redirect()->back()->with('error', 'No items in cart to deploy.');
        }

        try {
            DB::beginTransaction();

            // Create deployment record - all fields optional except original ones
            $deploymentData = [
                'user_id' => Auth::id(),
                'reference_number' => $this->generateReferenceNumber(),
                'deployed_to' => $request->deployed_to,
                'deployment_date' => $request->deployment_date,
                'remarks' => $request->remarks,
                'status' => 'completed',
            ];
            
            // Add optional fields only if they have values
            if ($request->filled('waybill_number')) {
                $deploymentData['waybill_number'] = $request->waybill_number;
            }
            
            if ($request->filled('contact_number')) {
                $deploymentData['contact_number'] = $request->contact_number;
            }
            
            if ($request->filled('address')) {
                $deploymentData['address'] = $request->address;
            }
            
            if ($request->filled('satellite_office')) {
                $deploymentData['satellite_office'] = $request->satellite_office;
            }

            $deployment = Deployment::create($deploymentData);

            // Process each cart item
            foreach ($cartItems as $cartItem) {
                $inventory = Inventory::where('id', $cartItem['inventory_id'])
                    ->where('stock_qty', '>=', $cartItem['quantity'])
                    ->first();

                if (!$inventory) {
                    throw new \Exception("Item is no longer available in sufficient quantity.");
                }

                DeploymentCart::create([
                    'deployment_id' => $deployment->id,
                    'inventory_id' => $inventory->id,
                    'component' => $inventory->component,
                    'quantity' => $cartItem['quantity'],
                ]);

                // Update inventory stock
                $inventory->stock_qty -= $cartItem['quantity'];
                
                if ($inventory->stock_qty <= 0) {
                    $inventory->status = 'Out of Stock';
                } elseif ($inventory->stock_qty < 5) {
                    $inventory->status = 'Low Stock';
                } else {
                    $inventory->status = 'Available';
                }
                
                $inventory->save();
            }

            DB::commit();

            // Clear cart and selected category
            session()->forget('deployment_cart');
            session()->forget([
                'selected_category',
                'selected_category_name',
                'selected_category_available'
            ]);

            return redirect()->route('admin.deployment')
                ->with('success', 'Deployment completed successfully! Reference #: ' . $deployment->reference_number);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Deployment failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function generateReferenceNumber()
    {
        $count = Deployment::whereDate('created_at', today())->count();
        $date = now()->format('Ymd');
        return 'DEP-' . $date . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    // View deployment details
    public function show($id)
    {
        $deployment = Deployment::with(['user', 'items.inventory'])->findOrFail($id);
        return view('admin.deployment_show', compact('deployment'));
    }

    // Get deployment history
    public function history(Request $request)
    {
        $query = Deployment::with(['user', 'items']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('deployed_to', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($request->has('date_from')) {
            $query->whereDate('deployment_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('deployment_date', '<=', $request->date_to);
        }

        $deployments = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.deployment_history', compact('deployments'));
    }
}