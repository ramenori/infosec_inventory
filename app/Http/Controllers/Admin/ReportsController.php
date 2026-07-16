<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deployment;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DeploymentReportsExport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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
                      ->orWhere('remarks', 'like', "%{$search}%")
                      ->orWhere('component', 'like', "%{$search}%") // This row will now dynamically match your custom serial inputs!
                      ->orWhereHas('inventory', function($q) use ($search) {
                          $q->where('category', 'like', "%{$search}%")
                            ->orWhere('brand', 'like', "%{$search}%");
                      });
            });
        }

        $reports = $reportsQuery->paginate(10);

        // Calculates sum of individually logged units correctly
        $totalItemsDeployed = Deployment::sum('quantity');

        return view('admin.reports', compact('reports', 'totalItemsDeployed'));
    }

    /**
     * Export deployment reports as PDF
     */
    public function exportPdf(Request $request)
    {
        $reportsQuery = Deployment::with(['user', 'inventory', 'contactPerson'])
            ->orderBy('deployment_date', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $reportsQuery->where(function($query) use ($search) {
                $query->where('deployed_to', 'like', "%{$search}%")
                      ->orWhere('remarks', 'like', "%{$search}%")
                      ->orWhere('component', 'like', "%{$search}%")
                      ->orWhereHas('inventory', function($q) use ($search) {
                          $q->where('category', 'like', "%{$search}%")
                            ->orWhere('brand', 'like', "%{$search}%");
                      });
            });
        }

        $reports = $reportsQuery->get();
        $totalItemsDeployed = $reports->sum('quantity');
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
        $reportsQuery = Deployment::with(['user', 'inventory', 'contactPerson'])
            ->orderBy('deployment_date', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $reportsQuery->where(function($query) use ($search) {
                $query->where('deployed_to', 'like', "%{$search}%")
                      ->orWhere('remarks', 'like', "%{$search}%")
                      ->orWhere('component', 'like', "%{$search}%")
                      ->orWhereHas('inventory', function($q) use ($search) {
                          $q->where('category', 'like', "%{$search}%")
                            ->orWhere('brand', 'like', "%{$search}%");
                      });
            });
        }

        $reports = $reportsQuery->get();

        // Initialize Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Deployment Reports');

        // Define Header Columns
        $columns = [
            'A' => 'Waybill No.',
            'B' => 'Date Deployed',
            'C' => 'Component / Serial Number', // Clean header title tracking change
            'D' => 'Contact Person',
            'E' => 'Contact Number',
            'F' => 'Address',
            'G' => 'Satellite Office',
            'H' => 'Prepared By',
            'I' => 'Remarks',
        ];

        // Write Header Row
        foreach ($columns as $col => $title) {
            $sheet->setCellValue($col . '1', $title);
        }

        // Header Row Styling (Dark Blue fill, white bold text, center-aligned)
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F3B68'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        $sheet->getRowDimension('1')->setRowHeight(28);

        // Populate Data Rows
        $rowNum = 2;
        foreach ($reports as $r) {
            $displayName = optional($r->contactPerson)->name ?: ($r->deployed_to ?: 'N/A');
            $displayContact = $r->contact_number ?: (optional($r->contactPerson)->contact_number ?: 'N/A');
            $displayAddress = $r->address ?: (optional($r->contactPerson)->address ?: 'N/A');
            $displayOffice = $r->satellite_office ?: (optional($r->contactPerson)->satellite_office ?: 'N/A');

            $sheet->setCellValue('A' . $rowNum, $r->waybill_number ?? 'N/A');
            $sheet->setCellValue('B' . $rowNum, $r->deployment_date ? $r->deployment_date->format('Y-m-d') : 'N/A');
            $sheet->setCellValue('C' . $rowNum, $r->component); // Outputs component text strings built with [SN: XXX] automatically!
            $sheet->setCellValue('D' . $rowNum, $displayName);
            $sheet->setCellValue('E' . $rowNum, $displayContact);
            $sheet->setCellValue('F' . $rowNum, $displayAddress);
            $sheet->setCellValue('G' . $rowNum, $displayOffice);
            $sheet->setCellValue('H' . $rowNum, optional($r->user)->name ?? 'N/A');
            $sheet->setCellValue('I' . $rowNum, $r->remarks ?? 'N/A');

            $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $rowNum++;
        }

        // Auto-fit all column widths perfectly
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Apply thin borders to all data rows
        $lastRow = $rowNum - 1;
        if ($lastRow >= 1) {
            $sheet->getStyle("A1:I{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        // Export Excel headers
        $filename = 'deployment_reports_' . now()->format('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}