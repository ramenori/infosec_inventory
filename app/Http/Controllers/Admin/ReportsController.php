<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deployment;
use App\Models\Inventory;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $reportsQuery = Deployment::with(['user', 'inventory'])
            ->orderBy('deployment_date', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $reportsQuery->where(function($query) use ($search) {
                $query->where('deployed_to', 'like', "%{$search}%")
                      ->orWhere('reference_number', 'like', "%{$search}%")
                      ->orWhere('remarks', 'like', "%{$search}%")
                      ->orWhere('component', 'like', "%{$search}%")
                      ->orWhereHas('inventory', function($q) use ($search) {
                          $q->where('category', 'like', "%{$search}%")
                            ->orWhere('brand', 'like', "%{$search}%");
                      });
            });
        }

        $reports = $reportsQuery->paginate(10);

        $totalItemsDeployed  = Deployment::sum('quantity');

        return view('admin.reports', compact('reports', 'totalItemsDeployed'));
    }
}