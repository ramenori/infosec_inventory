<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Deployment Reports</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; }
    table { width:100%; border-collapse: collapse; margin-top:10px; }
    th, td { border:1px solid #ddd; padding:6px; text-align:left; }
    th { background:#f2f2f2; }
    h1 { font-size:18px; margin:0 0 8px 0; }
    .meta { font-size:11px; color:#666; }
  </style>
</head>
<body>
  <h1>Deployment Reports</h1>
  <div class="meta">Generated: {{ now()->toDateTimeString() }} — Total items deployed: {{ $totalItemsDeployed ?? 0 }}</div>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Waybill No.</th>
        <th>Date</th>
        <th>Component</th>
        <th>Contact Person</th>
        <th>Contact Number</th>
        <th>Address</th>
        <th>Satellite Office</th>
        <th>Prepared By</th>
        <th>Remarks</th>
      </tr>
    </thead>
    <tbody>
      @foreach($reports as $i => $r)
        <tr>
          <td>{{ $i+1 }}</td>
          <td>{{ $r->waybill_number ?? 'N/A' }}</td>
          <td>{{ optional($r->deployment_date)->format('Y-m-d') }}</td>
          <td>{{ $r->component }}</td>
          <td>{{ optional($r->contactPerson)->name ?? $r->deployed_to }}</td>
          <td>{{ $r->contact_number ?? optional($r->contactPerson)->contact_number }}</td>
          <td>{{ $r->address ?? optional($r->contactPerson)->address }}</td>
          <td>{{ $r->satellite_office ?? optional($r->contactPerson)->satellite_office }}</td>
          <td>{{ optional($r->user)->name }}</td>
          <td>{{ $r->remarks }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
