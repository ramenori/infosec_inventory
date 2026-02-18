<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory;
use App\Models\Deployment;
use App\Models\DeploymentCart;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Your existing authentication checks...
        if (!Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('admin.login')
                ->with('error', 'Session expired. Please login.');
        }
        
        $user = Auth::user();
        if (!$user) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('admin.login')
                ->with('error', 'User not found. Please login again.');
        }
        
        // Fetch dashboard data
        $dashboardData = $this->getDashboardData();
        
        return view('admin.dashboard', compact('dashboardData'));
    }
    
    private function getDashboardData()
    {
        // ============================================
        // MOST DEPLOYED ITEM
        // ============================================
        $mostDeployedItem = DeploymentCart::select(
                'component',
                DB::raw('SUM(quantity) as total_deployed')
            )
            ->groupBy('component')
            ->orderBy('total_deployed', 'DESC')
            ->first();
        
        // ============================================
        // TOP SUPPLIER
        // ============================================
        $topSupplier = Supplier::withCount('inventories')
            ->orderBy('inventories_count', 'desc')
            ->first();
        
        // ============================================
        // TOTAL STOCK
        // ============================================
        $totalStockQty = Inventory::sum('stock_qty');
        
        // ============================================
        // ITEMS DEPLOYED THIS MONTH
        // ============================================
        $itemsDeployedThisMonth = DeploymentCart::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('quantity');
        
        // ============================================
        // ANALYTICS DATA - This is the important part!
        // ============================================
        
        // 1. Daily deployment trend (last 7 days)
        $dailyTrend = DeploymentCart::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(quantity) as total')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');
        
        $dailyLabels = [];
        $dailyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dailyLabels[] = now()->subDays($i)->format('D');
            $dailyData[] = $dailyTrend[$date]->total ?? 0;
        }
        
        // 2. Top 5 deployed items (for pie chart)
        $topItems = DeploymentCart::select(
                'component',
                DB::raw('SUM(quantity) as total')
            )
            ->groupBy('component')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->get();
        
        $topItemsLabels = $topItems->pluck('component')->toArray();
        $topItemsData = $topItems->pluck('total')->toArray();
        
        // Add "Others" category if needed
        $otherItemsTotal = DeploymentCart::whereNotIn('component', $topItems->pluck('component'))
            ->sum('quantity');
        
        if ($otherItemsTotal > 0) {
            $topItemsLabels[] = 'Others';
            $topItemsData[] = $otherItemsTotal;
        }
        
        // 3. Monthly comparison (last 6 months)
        $monthlyData = DeploymentCart::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(quantity) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function($item) {
                $key = $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                return [$key => $item->total];
            });
        
        $monthlyLabels = [];
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $monthlyLabels[] = $date->format('M Y');
            $monthlyData[] = $monthlyData[$key] ?? 0;
        }
        
        // 4. Category distribution (from inventory)
        $categoryDistribution = Inventory::select(
                'category',
                DB::raw('SUM(stock_qty) as total')
            )
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->get();
        
        $categoryLabels = $categoryDistribution->pluck('category')->toArray();
        $categoryData = $categoryDistribution->pluck('total')->toArray();
        
        // 5. Deployment status breakdown
        $statusBreakdown = [
            'completed' => Deployment::where('status', 'completed')->count(),
            'pending' => Deployment::where('status', 'pending')->count(),
            'cancelled' => Deployment::where('status', 'cancelled')->count(),
        ];
        
        // 6. Recent activities
        $recentActivities = DeploymentCart::with('deployment')
            ->latest()
            ->take(10)
            ->get()
            ->map(function($cart) {
                return [
                    'component' => $cart->component,
                    'quantity' => $cart->quantity,
                    'deployed_to' => $cart->deployment->deployed_to ?? 'Unknown',
                    'date' => $cart->created_at->format('M d, Y h:i A'),
                    'time_ago' => $cart->created_at->diffForHumans(),
                ];
            });
        
        return [
            // Stats cards data
            'most_deployed_item' => $mostDeployedItem ? $mostDeployedItem->component : 'No data',
            'most_deployed_quantity' => $mostDeployedItem ? $mostDeployedItem->total_deployed : 0,
            'top_supplier' => $topSupplier ? $topSupplier->name : 'No supplier',
            'total_stock_qty' => number_format($totalStockQty),
            'items_deployed_month' => $itemsDeployedThisMonth,
            
            // Analytics data
            'daily_chart' => [
                'labels' => $dailyLabels,
                'data' => $dailyData,
            ],
            'top_items_chart' => [
                'labels' => $topItemsLabels,
                'data' => $topItemsData,
            ],
            'monthly_chart' => [
                'labels' => $monthlyLabels,
                'data' => $monthlyData,
            ],
            'category_chart' => [
                'labels' => $categoryLabels,
                'data' => $categoryData,
            ],
            'status_breakdown' => $statusBreakdown,
            'recent_activities' => $recentActivities,
        ];
    }
}