<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Deployment Reports</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size:10px; }
    table { width:100%; border-collapse: collapse; margin-top:10px; table-layout: fixed; }
    th, td { border:1px solid #ddd; padding:5px; text-align:left; word-wrap: break-word; vertical-align: top; }
    th { background:#f2f2f2; font-weight: bold; font-size:9px; text-transform: uppercase; }
    h1 { font-size:16px; margin:0 0 6px 0; }
    .meta { font-size:9px; color:#666; }
    code { font-family: monospace; color: #4a5dca; font-size: 9px; }
    .serial-item { margin-bottom: 2px; }
    .serial-num { color: #555; font-size: 8px; }
  </style>
</head>
<body>
  <h1>Deployment Reports</h1>
  <div class="meta">Generated: {{ now()->toDateTimeString() }} — Total Items Deployed: {{ $totalItemsDeployed ?? $reports->sum('quantity') }}</div>

  <table>
    <thead>
      <tr>
        <th style="width: 3%;">#</th>
        <th style="width: 8%;">Waybill</th>
        <th style="width: 8%;">Date</th>
        <th style="width: 14%;">Component</th>
        <th style="width: 4%;">Qty</th>
        <th style="width: 14%;">Serial Number(s)</th>
        <th style="width: 11%;">Contact Person</th>
        <th style="width: 9%;">Contact No.</th>
        <th style="width: 11%;">Address</th>
        <th style="width: 9%;">Satellite Office</th>
        <th style="width: 9%;">Prepared By</th>
      </tr>
    </thead>
    <tbody>
      @foreach($reports as $i => $r)
        @php
            // Clean component name if legacy bracket syntax exists
            $displayComponent = $r->component;
            if (str_contains($r->component, '[SN:')) {
                $displayComponent = trim(preg_replace('/\[SN:\s*.*?\]/', '', $r->component));
            }

            // Extract serials array from JSON payload stored in department field or fallback
            $serials = [];
            if (!empty($r->department)) {
                $decoded = json_decode($r->department, true);
                if (is_array($decoded)) {
                    $serials = $decoded;
                }
            }

            // Fallback for older legacy records
            if (empty($serials)) {
                if (str_contains($r->component, '[SN:')) {
                    preg_match('/\[SN:\s*(.*?)\]/', $r->component, $matches);
                    $serials[] = $matches[1] ?? 'No Serial';
                } else {
                    $serials[] = 'No Serial';
                }
            }
        @endphp
        <tr>
          <td>{{ $i+1 }}</td>
          <td><code>{{ $r->waybill_number ?? 'N/A' }}</code></td>
          <td>{{ optional($r->deployment_date)->format('Y-m-d') }}</td>
          <td>
            <strong>{{ $displayComponent }}</strong>
            @if(optional($r->inventory)->brand)
              <br><span style="color:#777; font-size:8px;">Brand: {{ $r->inventory->brand }}</span>
            @endif
          </td>
          <td style="text-align: center;"><strong>{{ $r->quantity }}</strong></td>
          <td>
            @foreach($serials as $idx => $serial)
              <div class="serial-item">
                @if(count($serials) > 1)
                  <span class="serial-num">#{{ $idx + 1 }}:</span>
                @endif
                <code>{{ $serial }}</code>
              </div>
            @endforeach
          </td>
          <td>{{ optional($r->contactPerson)->name ?? $r->deployed_to }}</td>
          <td>{{ $r->contact_number ?? optional($r->contactPerson)->contact_number ?? '—' }}</td>
          <td>{{ $r->address ?? optional($r->contactPerson)->address ?? '—' }}</td>
          <td>{{ $r->satellite_office ?? optional($r->contactPerson)->satellite_office ?? '—' }}</td>
          <td>{{ optional($r->user)->name ?? 'N/A' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>