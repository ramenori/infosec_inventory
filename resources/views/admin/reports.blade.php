@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <div>
            <h1 class="h2 mb-1 fw-bold text-gradient">Deployment Reports</h1>
            <p class="text-muted mb-0">View and manage deployment reports</p>
        </div>
    </div>

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-light p-3 rounded">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-file-earmark-text"></i> Reports
            </li>
        </ol>
    </nav>

    {{-- Controls Section --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0"></div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end align-items-center gap-2">
                        
                        {{-- Category Filter Dropdown --}}
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static">
                                <i class="bi bi-funnel me-1"></i> {{ request('category') ? request('category') : 'Filter by Category' }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                @foreach(\App\Models\Category::all() as $cat)
                                    @php
                                        $categoryIcons = [
                                            'Access Control' => 'bi-shield-lock-fill text-primary',
                                            'CCTV'           => 'bi-camera-video-fill text-info',
                                            'GPS'            => 'bi-geo-alt-fill text-danger',
                                            'Wireless Alarm' => 'bi-bell-fill text-warning',
                                            'Network'        => 'bi-wifi text-success',
                                            'Consumables'    => 'bi-box-seam-fill text-secondary',
                                        ];
                                        $icon = $categoryIcons[$cat->name] ?? 'bi-folder-fill text-primary';
                                    @endphp
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('admin.reports', array_merge(request()->query(), ['category' => $cat->name])) }}">
                                            <i class="bi {{ $icon }}"></i> {{ $cat->name }}
                                        </a>
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('admin.reports', \Illuminate\Support\Arr::except(request()->query(), ['category'])) }}">
                                        <i class="bi bi-eye text-primary"></i> View All
                                    </a>
                                </li>
                            </ul>
                        </div>

                        {{-- Export Dropdown --}}
                        <div class="dropdown">
                            <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static">
                                <i class="bi bi-download me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.reports.export.excel', request()->query()) }}">
                                        <i class="bi bi-file-earmark-excel me-2"></i> Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.reports.export', request()->query()) }}">
                                        <i class="bi bi-file-earmark-pdf me-2"></i> PDF
                                    </a>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reports Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-table me-2"></i> Deployment Reports
                </h6>
                <small class="text-muted">
                    Showing {{ $reports->firstItem() ?? 0 }} to {{ $reports->lastItem() ?? 0 }} of {{ $reports->total() }} reports
                </small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 ps-4">WAYBILL NO.</th>
                            <th class="border-0">DATE DEPLOYED</th>
                            <th class="border-0 text-center">CATEGORY</th>
                            <th class="border-0 text-center">QUANTITY</th>
                            <th class="border-0 text-center">DEPLOYED TO</th>
                            <th class="border-0 text-center">CONTACT NO.</th>
                            <th class="border-0 text-center">ADDRESS</th>
                            <th class="border-0 text-center">SATELLITE OFFICE</th>
                            <th class="border-0 text-center pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $deployment)
                            @php
                                $category = optional($deployment->inventory)->category ?? 'Other';
                                $categoryIcons = [
                                    'Access Control' => 'bi-shield-lock-fill text-primary',
                                    'CCTV'           => 'bi-camera-video-fill text-info',
                                    'GPS'            => 'bi-geo-alt-fill text-danger',
                                    'Wireless Alarm' => 'bi-bell-fill text-warning',
                                    'Network'        => 'bi-wifi text-success',
                                    'Consumables'    => 'bi-box-seam-fill text-secondary',
                                ];
                                $categoryIcon = $categoryIcons[$category] ?? 'bi-folder-fill text-primary';
                            @endphp
                            <tr class="hover-shadow">
                                {{-- COLUMN 1: WAYBILL NO. --}}
                                <td class="align-middle ps-4">
                                    <span class="waybill-pill">
                                        <i class="bi bi-upc-scan pill-icon"></i>
                                        <span class="waybill-text">{{ $deployment->waybill_number ?? 'N/A' }}</span>
                                    </span>
                                </td>

                                {{-- COLUMN 2: DATE DEPLOYED --}}
                                <td class="align-middle">
                                    <strong>{{ optional($deployment->deployment_date)->format('M d, Y') }}</strong>
                                </td>

                                {{-- COLUMN 3: CATEGORY --}}
                                <td class="text-center align-middle">
                                    <span class="d-inline-flex align-items-center gap-2 fw-semibold text-dark">
                                        <i class="bi {{ $categoryIcon }} fs-6"></i>
                                        <span>{{ $category }}</span>
                                    </span>
                                </td>

                                {{-- COLUMN 4: QUANTITY --}}
                                <td class="text-center align-middle fw-semibold text-dark">
                                    @if(!empty($deployment->components_payload))
                                        @php 
                                            $payloadArray = json_decode($deployment->components_payload, true) ?? [];
                                            $combinedTotal = collect($payloadArray)->sum('quantity');
                                        @endphp
                                        {{ $combinedTotal > 0 ? $combinedTotal : $deployment->quantity }}
                                    @else
                                        {{ $deployment->quantity }}
                                    @endif
                                </td>

                                {{-- COLUMN 5: DEPLOYED TO --}}
                                <td class="text-center align-middle">
                                    @php
                                        $displayName = optional($deployment->contactPerson)->name ?? $deployment->deployed_to;
                                        $displayContact = optional($deployment->contactPerson)->contact_number ?? $deployment->contact_number;
                                        $displayAddress = optional($deployment->contactPerson)->address ?? $deployment->address;
                                        $displayOffice = optional($deployment->contactPerson)->satellite_office ?? $deployment->satellite_office;
                                    @endphp
                                    <strong class="d-block">{{ $displayName }}</strong>
                                </td>

                                {{-- COLUMN 6: CONTACT NUMBER --}}
                                <td class="text-center align-middle">
                                    @if($displayContact)
                                        <i class="bi bi-telephone me-1"></i> {{ $displayContact }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                {{-- COLUMN 7: ADDRESS --}}
                                <td class="text-center align-middle">
                                    @if($displayAddress)
                                        <i class="bi bi-geo-alt me-1"></i> {{ Str::limit($displayAddress, 20) }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                {{-- COLUMN 8: SATELLITE OFFICE --}}
                                <td class="text-center align-middle">
                                    @if($displayOffice)
                                        <i class="bi bi-building me-1"></i> {{ $displayOffice }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                {{-- COLUMN 9: ACTIONS --}}
                                <td class="text-center align-middle pe-4">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary view-report-btn"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#reportDetailsModal"
                                            data-waybill="{{ $deployment->waybill_number ?? 'N/A' }}"
                                            data-date="{{ optional($deployment->deployment_date)->format('M d, Y') ?? 'N/A' }}"
                                            data-deployed-to="{{ $displayName }}"
                                            data-contact="{{ $displayContact ?: 'N/A' }}"
                                            data-address="{{ $displayAddress ?: 'N/A' }}"
                                            data-office="{{ $displayOffice ?: 'N/A' }}"
                                            data-prepared-by="{{ optional($deployment->user)->name ?? 'N/A' }}"
                                            data-remarks="{{ e($deployment->remarks ?: 'No remarks provided.') }}"
                                            data-components-bundle="{{ $deployment->components_payload }}"
                                            data-category="{{ $category }}"
                                            data-component="{{ $deployment->component }}"
                                            data-brand="{{ optional($deployment->inventory)->brand ?? 'N/A' }}"
                                            data-quantity="{{ $deployment->quantity }}"
                                            title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-file-earmark-text display-4 text-muted mb-3"></i>
                                        <h5 class="text-muted">No deployment reports found</h5>
                                        <p class="text-muted mb-4">Deploy some items first to see reports here</p>
                                        <a href="{{ route('admin.deployment') }}" class="btn btn-primary">
                                            <i class="bi bi-truck me-1"></i> Go to Deployment
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Footer with Pagination --}}
        @if($reports->hasPages())
            <div class="card-footer bg-light py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} entries
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            {{ $reports->links() }}
                        </ul>
                    </nav>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Custom CSS --}}
<style>
.text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.hover-shadow:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}
.empty-state { padding: 3rem 1rem; }
.table tbody tr { transition: all 0.2s ease; }
.waybill-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background-color: rgba(102, 126, 234, 0.08);
    color: #4a5dca;
    border: 1px solid rgba(102, 126, 234, 0.15);
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.2s ease-in-out;
}
.waybill-pill:hover {
    background-color: rgba(102, 126, 234, 0.15);
    border-color: rgba(102, 126, 234, 0.3);
    color: #3b4cb4;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(102, 126, 234, 0.1);
}
.pill-icon {
    font-size: 0.85rem;
    opacity: 0.85;
}
.waybill-text {
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Monaco, monospace;
    letter-spacing: 0.5px;
}
</style>

{{-- Deployment Report Details Modal --}}
<div class="modal fade" id="reportDetailsModal" tabindex="-1" aria-labelledby="reportDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            {{-- Modal Header --}}
            <div class="modal-header border-0 bg-primary bg-gradient text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white bg-opacity-20 p-2.5 rounded-3 d-flex align-items-center justify-content-center">
                        <i class="bi bi-file-earmark-text fs-3 text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="reportDetailsModalLabel">Deployment Details</h5>
                        <small class="text-white-50">Comprehensive report summary & serial tracking</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-light-subtle">
                {{-- Quick Overview Cards --}}
                <div class="row g-3 mb-4">
                    {{-- Deployment Info Card --}}
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                            <div class="d-flex align-items-center gap-2 mb-3 text-primary border-bottom pb-2">
                                <i class="bi bi-truck fs-5"></i>
                                <h6 class="fw-bold mb-0 text-dark">Deployment Info</h6>
                            </div>
                            <div class="d-flex flex-column gap-2.5">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Waybill No.:</span>
                                    <span class="waybill-pill py-1 px-2.5 m-0" id="modalWaybill">-</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Date Deployed:</span>
                                    <span class="fw-semibold text-dark small" id="modalDate">-</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Prepared By:</span>
                                    <span class="fw-semibold text-dark small" id="modalPreparedBy">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Recipient Info Card --}}
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                            <div class="d-flex align-items-center gap-2 mb-3 text-primary border-bottom pb-2">
                                <i class="bi bi-person-badge fs-5"></i>
                                <h6 class="fw-bold mb-0 text-dark">Recipient Details</h6>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                <div>
                                    <span class="text-muted d-block small" style="font-size: 11px;">CONTACT PERSON</span>
                                    <span class="fw-bold text-dark" id="modalDeployedTo">-</span>
                                </div>
                                <div class="row g-2 mt-1">
                                    <div class="col-6">
                                        <span class="text-muted d-block small" style="font-size: 11px;">PHONE</span>
                                        <span class="fw-semibold text-dark small" id="modalContact">-</span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block small" style="font-size: 11px;">OFFICE</span>
                                        <span class="fw-semibold text-dark small" id="modalOffice">-</span>
                                    </div>
                                </div>
                                <div class="mt-1">
                                    <span class="text-muted d-block small" style="font-size: 11px;">LOCATION ADDRESS</span>
                                    <span class="fw-medium text-secondary small" id="modalAddress">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Component List Section --}}
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                        <div class="d-flex align-items-center gap-2 text-primary">
                            <i class="bi bi-boxes fs-5"></i>
                            <h6 class="fw-bold mb-0 text-dark">Deployed Component Items</h6>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1" id="modalTotalItemsBadge">1 item(s)</span>
                    </div>

                    <div id="modalComponentContainer" class="d-flex flex-column gap-2" style="max-height: 280px; overflow-y: auto;">
                        {{-- Items loaded dynamically via JavaScript --}}
                    </div>
                </div>

                {{-- Remarks Section --}}
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-1 text-muted">
                        <i class="bi bi-chat-left-text"></i>
                        <span class="fw-bold small text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Remarks & Notes</span>
                    </div>
                    <p class="mb-0 text-dark small fst-italic ps-4" id="modalRemarks">-</p>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer border-0 p-3 bg-light">
                <button type="button" class="btn btn-secondary px-4 rounded-3 fw-medium" data-bs-dismiss="modal">
                    Close Details
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const reportDetailsModal = document.getElementById('reportDetailsModal');
    
    if (reportDetailsModal) {
        reportDetailsModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            const waybill = button.getAttribute('data-waybill') || 'N/A';
            const date = button.getAttribute('data-date') || 'N/A';
            const preparedBy = button.getAttribute('data-prepared-by') || 'N/A';
            const deployedTo = button.getAttribute('data-deployed-to') || 'N/A';
            const contact = button.getAttribute('data-contact') || 'N/A';
            const office = button.getAttribute('data-office') || 'N/A';
            const address = button.getAttribute('data-address') || 'N/A';
            const remarks = button.getAttribute('data-remarks') || 'No remarks provided.';
            
            const fallbackCategory = button.getAttribute('data-category') || 'Other';
            const fallbackComponent = button.getAttribute('data-component') || 'N/A';
            const fallbackBrand = button.getAttribute('data-brand') || 'N/A';
            const fallbackQuantity = parseInt(button.getAttribute('data-quantity')) || 1;

            const componentsBundle = button.getAttribute('data-components-bundle') || '';

            document.getElementById('modalWaybill').textContent = waybill;
            document.getElementById('modalDate').textContent = date;
            document.getElementById('modalPreparedBy').textContent = preparedBy;
            document.getElementById('modalDeployedTo').textContent = deployedTo;
            document.getElementById('modalContact').textContent = contact;
            document.getElementById('modalOffice').textContent = office;
            document.getElementById('modalAddress').textContent = address;
            document.getElementById('modalRemarks').textContent = remarks;

            const container = document.getElementById('modalComponentContainer');
            container.innerHTML = '';

            let itemsList = [];
            
            if (componentsBundle && componentsBundle !== 'null') {
                try {
                    itemsList = JSON.parse(componentsBundle);
                } catch (e) {
                    console.error("Failed to parse components bundle JSON:", e);
                    itemsList = [];
                }
            }

            if (itemsList.length === 0) {
                let cleanComponent = fallbackComponent;
                let cleanSerial = 'No Serial';
                if (fallbackComponent.includes('[SN:')) {
                    const serialMatch = fallbackComponent.match(/\[SN:\s*(.*?)\]/);
                    if (serialMatch && serialMatch[1]) {
                        cleanSerial = serialMatch[1];
                        cleanComponent = fallbackComponent.replace(/\[SN:\s*.*?\]/, '').trim();
                    }
                }

                itemsList = [{
                    category: fallbackCategory,
                    component: cleanComponent,
                    brand: fallbackBrand,
                    quantity: fallbackQuantity,
                    serials: [cleanSerial]
                }];
            }

            const categoryIconMap = {
                'Access Control': 'bi-shield-lock-fill text-primary',
                'CCTV': 'bi-camera-video-fill text-info',
                'GPS': 'bi-geo-alt-fill text-danger',
                'Wireless Alarm': 'bi-bell-fill text-warning',
                'Network': 'bi-wifi text-success',
                'Consumables': 'bi-box-seam-fill text-secondary'
            };

            let totalUnitsCount = 0;

            itemsList.forEach((item) => {
                const category = item.category || 'Other';
                const componentName = item.component || 'N/A';
                const brand = item.brand || 'N/A';
                const itemQuantity = parseInt(item.quantity) || 1;
                const serialsArray = item.serials || [];
                const iconClass = categoryIconMap[category] || 'bi-folder-fill text-primary';

                for (let j = 0; j < itemQuantity; j++) {
                    totalUnitsCount++;
                    const currentUnitSerial = serialsArray[j] || 'No Serial';

                    const componentRow = `
                        <div class="d-flex align-items-center justify-content-between p-2.5 px-3 bg-light rounded-3 border">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-white text-secondary border fw-bold px-2 py-1">#${totalUnitsCount}</span>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi ${iconClass} small"></i>
                                        <span class="fw-bold text-dark small">${componentName}</span>
                                        ${itemQuantity > 1 ? '<span class="text-muted small" style="font-size:11px;">(Unit ' + (j + 1) + ')</span>' : ''}
                                    </div>
                                    <small class="text-muted d-block mt-0.5" style="font-size: 11px;">
                                        Category: <strong class="text-dark">${category}</strong> | Brand: <strong>${brand}</strong>
                                    </small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="text-muted d-block small" style="font-size: 10px;">SERIAL NO.</span>
                                <code class="bg-white border rounded px-2 py-0.5 text-primary fw-bold small">${currentUnitSerial}</code>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', componentRow);
                }
            });

            document.getElementById('modalTotalItemsBadge').textContent = `${totalUnitsCount} unit(s)`;
        });
    }
});
</script>
@endpush
@endsection