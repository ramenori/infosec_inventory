<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Deployment Reports</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size:11px; }
    table { width:100%; border-collapse: collapse; margin-top:10px; table-layout: fixed; }
    th, td { border:1px solid #ddd; padding:5px; text-align:left; word-wrap: break-word; }
    th { background:#f2f2f2; font-weight: bold; }
    h1 { font-size:18px; margin:0 0 8px 0; }
    .meta { font-size:10px; color:#666; }
    code { font-family: monospace; color: #4a5dca; }
  </style>
</head>
<body>
  <h1>Deployment Reports</h1>
  <div class="meta">Generated: {{ now()->toDateTimeString() }} — Total items deployed: {{ $totalItemsDeployed ?? 0 }}</div>

  <table>
    <thead>
      <tr>
        <th style="width: 3%;">#</th>
        <th style="width: 8%;">Waybill No.</th>
        <th style="width: 8%;">Date</th>
        <th style="width: 15%;">Component</th>
        <th style="width: 11%;">Serial Number</th>
        <th style="width: 11%;">Contact Person</th>
        <th style="width: 10%;">Contact No.</th>
        <th style="width: 14%;">Address</th>
        <th style="width: 10%;">Satellite Office</th>
        <th style="width: 10%;">Prepared By</th>
        <th style="width: 10%;">Remarks</th>
      </tr>
    </thead>
    <tbody>
      @foreach($reports as $i => $r)
        @php
            $displayComponent = $r->component;
            if (str_contains($r->component, '[SN:')) {
                preg_match('/\[SN:\s*(.*?)\]/', $r->component, $matches);
                $currentSerial = $matches[1] ?? 'No Serial';
                $displayComponent = trim(preg_replace('/\[SN:\s*.*?\]/', '', $r->component));
            } else {
                $currentSerial = optional($r->inventory)->serial_num ?? 'No Serial';
            }
        @endphp
        <tr>
          <td>{{ $i+1 }}</td>
          <td>{{ $r->waybill_number ?? 'N/A' }}</td>
          <td>{{ optional($r->deployment_date)->format('Y-m-d') }}</td>
          <td><strong>{{ $displayComponent }}</strong></td>
          <td><code>{{ $currentSerial }}</code></td>
          <td>{{ optional($r->contactPerson)->name ?? $r->deployed_to }}</td>
          <td>{{ $r->contact_number ?? optional($r->contactPerson)->contact_number ?? '—' }}</td>
          <td>{{ $r->address ?? optional($r->contactPerson)->address ?? '—' }}</td>
          <td>{{ $r->satellite_office ?? optional($r->contactPerson)->satellite_office ?? '—' }}</td>
          <td>{{ optional($r->user)->name ?? 'N/A' }}</td>
          <td>{{ $r->remarks ?? '—' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>