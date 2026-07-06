<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deployment;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DeploymentReportsExport;

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

    /**
     * Export deployment reports as PDF
     */
    public function exportPdf(Request $request)
    {
        $reportsQuery = Deployment::with(['user', 'inventory', 'contactPerson'])->orderBy('deployment_date', 'desc');

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

        $reports = $reportsQuery->get();

        $totalItemsDeployed  = $reports->sum('quantity');

        $data = compact('reports', 'totalItemsDeployed');

        $pdf = PDF::loadView('admin.reports_pdf', $data)->setPaper('a4', 'landscape');

        $filename = 'deployment_reports_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export deployment reports as Excel
     */
    public function exportExcel(Request $request)
    {
        $reportsQuery = Deployment::with(['user', 'inventory', 'contactPerson'])->orderBy('deployment_date', 'desc');

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

        $reports = $reportsQuery->get();

        $filename = 'deployment_reports_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new DeploymentReportsExport($reports), $filename);
    }
}
