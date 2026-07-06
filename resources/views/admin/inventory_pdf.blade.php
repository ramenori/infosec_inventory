<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inventory Report</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size:11px; }
    table { width:100%; border-collapse: collapse; margin-top:10px; }
    th, td { border:1px solid #ddd; padding:6px; text-align:left; }
    th { background:#f2f2f2; font-weight:bold; }
    h1 { font-size:18px; margin:0 0 8px 0; }
    .meta { font-size:10px; color:#666; }
    .status-badge { padding:2px 6px; border-radius:3px; font-size:10px; }
    .status-available { background:#d4edda; color:#155724; }
    .status-low { background:#fff3cd; color:#856404; }
    .status-out { background:#f8d7da; color:#721c24; }
  </style>
</head>
<body>
  <h1>Inventory Report</h1>
  <div class="meta">Generated: {{ now()->toDateTimeString() }} — Total Items: {{ $inventory->count() ?? 0 }}</div>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Component</th>
        <th>Serial Number</th>
        <th>Brand</th>
        <th>Category</th>
        <th>Stock Qty</th>
        <th>Status</th>
        <th>Date Added</th>
        <th>Supplier</th>
      </tr>
    </thead>
    <tbody>
      @forelse($inventory as $i => $item)
        <tr>
          <td>{{ $i+1 }}</td>
          <td>{{ $item->component ?? 'N/A' }}</td>
          <td>{{ $item->serial_num ?? 'N/A' }}</td>
          <td>{{ $item->brand ?? 'N/A' }}</td>
          <td>{{ $item->category ?? 'N/A' }}</td>
          <td>{{ $item->stock_qty ?? 0 }}</td>
          <td>
            @if($item->status === 'Available')
              <span class="status-badge status-available">{{ $item->status }}</span>
            @elseif($item->status === 'Low Stock')
              <span class="status-badge status-low">{{ $item->status }}</span>
            @elseif($item->status === 'Out of Stock')
              <span class="status-badge status-out">{{ $item->status }}</span>
            @else
              {{ $item->status }}
            @endif
          </td>
          <td>{{ optional($item->date_added)->format('Y-m-d') ?? 'N/A' }}</td>
          <td>{{ optional($item->supplier)->name ?? 'N/A' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="9" style="text-align:center;">No inventory items found</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
