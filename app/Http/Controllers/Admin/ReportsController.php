<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deployment;
use App\Models\DeploymentCart;
use App\Models\Inventory;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // Query deployment reports with related data - use 'items' not 'deploymentCarts'
        $reportsQuery = Deployment::with(['items.inventory'])
            ->orderBy('deployment_date', 'desc');
        
        // Apply search filter if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $reportsQuery->where(function($query) use ($search) {
                $query->where('deployed_to', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%")
                    ->orWhereHas('items', function($q) use ($search) {
                        $q->where('component', 'like', "%{$search}%")
                          ->orWhereHas('inventory', function($query2) use ($search) {
                              $query2->where('category', 'like', "%{$search}%")
                                    ->orWhere('brand', 'like', "%{$search}%");
                          });
                    });
            });
        }
        
        // Paginate the results
        $reports = $reportsQuery->paginate(10);
        
        // Calculate totals for the header
        $totalReports = Deployment::count();
        $totalItemsDeployed = DeploymentCart::sum('quantity');
        
        return view('admin.reports', compact('reports', 'totalReports', 'totalItemsDeployed'));
    }
}