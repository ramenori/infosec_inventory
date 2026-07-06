{{-- Deployment Reports - Excel Export Template --}}
{{-- This template is used to render data for Excel export with proper formatting --}}

@php
    $columns = [
        'Waybill No.',
        'Date',
        'Component',
        'Contact Person',
        'Contact Number',
        'Address',
        'Satellite Office',
        'Prepared By',
        'Remarks',
    ];
@endphp

@if($reports->count() > 0)
    @foreach($reports as $i => $r)
        <tr data-row="{{ $i+1 }}">
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
@else
    <tr>
        <td colspan="9" class="text-center">No deployment reports found</td>
    </tr>
@endif
