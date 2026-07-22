<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Category;
use App\Models\ActivityLog;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        // Standardized to categoryRelation (removed unused supplier relation)
        $query = Inventory::with(['categoryRelation']);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('component', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhereHas('categoryRelation', function($q) use ($search) {
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
        return view('admin.inventory_create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category'    => 'required|string|max:255',
            'component'   => 'required|string|max:255',
            'brand'       => 'nullable|string|max:255',
            'stock_qty'   => 'required|integer|min:0',
            'status'      => 'required|in:Available,Low Stock,Out of Stock,Maintenance,Deployed',
            'description' => 'nullable|string',
        ]);

        // Auto-set status based on stock quantity
        $status = $request->status;
        if ($request->stock_qty == 0) {
            $status = 'Out of Stock';
        } elseif ($request->stock_qty < 5) {
            $status = 'Low Stock';
        } else {
            $status = 'Available';
        }

        $inventory = Inventory::create([
            'category'    => $request->category,
            'component'   => $request->component,
            'brand'       => $request->brand,
            'stock_qty'   => $request->stock_qty,
            'date_added'  => now(),
            'status'      => $status,
            'description' => $request->description,
        ]);

        // Log the activity with explicit initial stock details
        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'created',
            'entity_type' => 'inventory',
            'entity_id'   => $inventory->id,
            'component'   => $request->component,
            'details'     => "Created new inventory item: {$request->component} with {$request->stock_qty} unit(s)",
        ]);

        return redirect()->route('admin.inventory')->with('success', 'Item added successfully!');
    }

    public function edit($id)
    {
        $inventory = Inventory::findOrFail($id);
        $categories = Category::all();
        return view('admin.inventory_edit', compact('inventory', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);

        $request->validate([
            'category'    => 'required|string|max:255',
            'component'   => 'required|string|max:255',
            'brand'       => 'nullable|string|max:255',
            'stock_qty'   => 'required|integer|min:0',
            'date_added'  => 'required|date',
            'status'      => 'required|in:Available,Low Stock,Out of Stock,Maintenance',
            'description' => 'nullable|string',
        ]);

        // 1. Capture old stock quantity before updating
        $oldStock = (int) $inventory->stock_qty;
        $newStock = (int) $request->stock_qty;
        $stockDiff = $newStock - $oldStock;

        // 2. Auto-update status based on stock
        if ($newStock == 0) {
            $status = 'Out of Stock';
        } elseif ($newStock < 5) {
            $status = 'Low Stock';
        } else {
            $status = 'Available';
        }

        // 3. Perform inventory update
        $inventory->update([
            'category'    => $request->category,
            'component'   => $request->component,
            'brand'       => $request->brand,
            'stock_qty'   => $newStock,
            'date_added'  => $request->date_added,
            'status'      => $status,
            'description' => $request->description,
        ]);

        // 4. Build a dynamic, human-readable activity log detail string
        if ($stockDiff > 0) {
            $logDetails = "Added {$stockDiff} units to {$request->component} (Stock: {$oldStock} ➔ {$newStock})";
        } elseif ($stockDiff < 0) {
            $absDiff = abs($stockDiff);
            $logDetails = "Deducted {$absDiff} units from {$request->component} (Stock: {$oldStock} ➔ {$newStock})";
        } else {
            $logDetails = "Updated item details for {$request->component}";
        }

        // 5. Save activity log
        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'updated',
            'entity_type' => 'inventory',
            'entity_id'   => $inventory->id,
            'component'   => $request->component,
            'details'     => $logDetails,
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

        $component = $inventory->component;
        $inventory->delete();

        // Log the activity
        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'deleted',
            'entity_type' => 'inventory',
            'entity_id'   => $id,
            'component'   => $component,
            'details'     => "Deleted inventory item: {$component}",
        ]);

        return redirect()->route('admin.inventory')->with('success', 'Item deleted successfully!');
    }

    public function updateStock(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);

        $request->validate([
            'stock_qty' => 'required|integer|min:0',
        ]);

        $oldStock = (int) $inventory->stock_qty;
        $newStock = (int) $request->stock_qty;
        $stockDiff = $newStock - $oldStock;

        $inventory->stock_qty = $newStock;
        
        if ($newStock == 0) {
            $inventory->status = 'Out of Stock';
        } elseif ($newStock < 5) {
            $inventory->status = 'Low Stock';
        } else {
            $inventory->status = 'Available';
        }

        $inventory->save();

        // Log quick stock adjustments via AJAX
        if ($stockDiff !== 0) {
            $actionVerb = $stockDiff > 0 ? "Added " . $stockDiff : "Deducted " . abs($stockDiff);
            ActivityLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'updated',
                'entity_type' => 'inventory',
                'entity_id'   => $inventory->id,
                'component'   => $inventory->component,
                'details'     => "{$actionVerb} units on {$inventory->component} (Stock: {$oldStock} ➔ {$newStock})",
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully',
            'data'    => $inventory
        ]);
    }

    public function getLogs(Request $request)
    {
        $query = ActivityLog::where('entity_type', 'inventory')->with('user');

        if ($request->has('date') && !empty($request->date)) {
            $query->whereDate('created_at', $request->date);
        } else {
            $query->where('created_at', '>=', now()->subDays(30));
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.inventory_logs', compact('logs'));
    }

    public function exportPdf(Request $request)
    {
        $query = Inventory::with(['categoryRelation']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('component', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhereHas('categoryRelation', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $inventory = $query->orderBy('created_at', 'desc')->get();
        $pdf = PDF::loadView('admin.inventory_pdf', compact('inventory'))->setPaper('a4', 'landscape');

        return $pdf->download('inventory_report_' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportCsv(Request $request) 
    {
        $query = Inventory::with(['categoryRelation']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('component', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $inventory = $query->orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventory Report');

        $columns = ['A' => 'ID', 'B' => 'Item/Component', 'C' => 'Category', 'D' => 'Brand', 'E' => 'Stock Qty', 'F' => 'Status', 'G' => 'Date Added'];

        foreach ($columns as $col => $title) {
            $sheet->setCellValue($col . '1', $title);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F3B68'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);
        $sheet->getRowDimension('1')->setRowHeight(28);

        $rowNum = 2;
        foreach ($inventory as $item) {
            $sheet->setCellValue('A' . $rowNum, $item->id);
            $sheet->setCellValue('B' . $rowNum, $item->component);
            $sheet->setCellValue('C' . $rowNum, $item->category);
            $sheet->setCellValue('D' . $rowNum, $item->brand ?: 'N/A');
            $sheet->setCellValue('E' . $rowNum, $item->stock_qty);
            $sheet->setCellValue('F' . $rowNum, $item->status);
            $sheet->setCellValue('G' . $rowNum, $item->date_added ? $item->date_added->format('M d, Y') : 'N/A');

            $statusCell = 'F' . $rowNum;
            if ($item->status === 'Available') {
                $sheet->getStyle($statusCell)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1F8B4C'));
            } elseif ($item->status === 'Low Stock') {
                $sheet->getStyle($statusCell)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('A06E00'));
            } elseif ($item->status === 'Out of Stock') {
                $sheet->getStyle($statusCell)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('D12A3A'));
            }

            $rowNum++;
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $styleRange = 'A1:G' . ($rowNum - 1);
        $sheet->getStyle($styleRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $writer = new Xlsx($spreadsheet);
        $filename = 'inventory_report_' . now()->format('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}