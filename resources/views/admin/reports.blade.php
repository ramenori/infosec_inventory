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
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static">
                                <i class="bi bi-funnel me-1"></i> Filter by Status
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"><span class="badge bg-success me-2">●</span> Completed</a></li>
                                <li><a class="dropdown-item" href="#"><span class="badge bg-primary me-2">●</span> In Progress</a></li>
                                <li><a class="dropdown-item" href="#"><span class="badge bg-warning me-2">●</span> Pending</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i> View All</a></li>
                            </ul>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static">
                                <i class="bi bi-download me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
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
                            <th class="border-0">WAYBILL NO.</th>
                            <th class="border-0">DATE DEPLOYED</th>
                            <th class="border-0">CATEGORY</th>
                            <th class="border-0 text-center">COMPONENT</th>
                            <th class="border-0 text-center">QUANTITY</th>
                            <th class="border-0 text-center">DEPLOYED TO</th>
                            <th class="border-0 text-center">CONTACT NO.</th>
                            <th class="border-0 text-center">ADDRESS</th>
                            <th class="border-0 text-center">SATELLITE OFFICE</th>
                            <th class="border-0 text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $deployment)
                            <tr class="hover-shadow">
                                {{-- COLUMN 1: WAYBILL NO. --}}
                                <td class="align-middle">
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
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="item-icon me-2">
                                            @php
                                                $categoryIcons = [
                                                    'Access Control' => 'bi-shield-lock',
                                                    'CCTV'           => 'bi-camera-video',
                                                    'GPS'            => 'bi-geo-alt',
                                                    'Wireless Alarm' => 'bi-bell',
                                                    'Network'        => 'bi-wifi',
                                                    'Consumables'    => 'bi-briefcase',
                                                ];
                                                $category = optional($deployment->inventory)->category ?? 'Other';
                                                $icon = $categoryIcons[$category] ?? 'bi-box-seam';
                                            @endphp
                                            <i class="bi {{ $icon }} text-primary"></i>
                                        </div>
                                        <div>
                                            <strong class="d-block">{{ $category }}</strong>
                                        </div>
                                    </div>
                                </td>

                                {{-- COLUMN 4: COMPONENT --}}
                                <td class="text-center align-middle">
                                    <strong class="d-block">{{ $deployment->component }}</strong>
                                    <small class="text-muted">{{ optional($deployment->inventory)->brand ?? 'N/A' }}</small>
                                    <br>
                                    <small class="text-muted">
                                        <code>{{ optional($deployment->inventory)->serial_num ?? 'No Serial' }}</code>
                                    </small>
                                </td>

                                {{-- COLUMN 5: QUANTITY --}}
                                <td class="text-center align-middle">
                                    {{ $deployment->quantity }}
                                </td>

                                {{-- COLUMN 6: DEPLOYED TO --}}
                                <td class="text-center align-middle">
                                    @php
                                        $displayName = optional($deployment->contactPerson)->name ?? $deployment->deployed_to;
                                        $displayContact = optional($deployment->contactPerson)->contact_number ?? $deployment->contact_number;
                                        $displayAddress = optional($deployment->contactPerson)->address ?? $deployment->address;
                                        $displayOffice = optional($deployment->contactPerson)->satellite_office ?? $deployment->satellite_office;
                                    @endphp
                                    <strong class="d-block">{{ $displayName }}</strong>
                                </td>

                                {{-- COLUMN 7: CONTACT NUMBER --}}
                                <td class="text-center align-middle">
                                    @if($displayContact)
                                        <i class="bi bi-telephone me-1"></i> {{ $displayContact }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                {{-- COLUMN 8: ADDRESS --}}
                                <td class="text-center align-middle">
                                    @if($displayAddress)
                                            <i class="bi bi-geo-alt me-1"></i> {{ Str::limit($displayAddress, 20) }}

                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                {{-- COLUMN 9: SATELLITE OFFICE --}}
                                <td class="text-center align-middle">
                                    @if($displayOffice)
                                            <i class="bi bi-building me-1"></i> {{ $displayOffice }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                {{-- COLUMN 10: ACTIONS --}}
                                <td class="text-center align-middle">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary view-report-btn"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#reportDetailsModal"
                                            data-waybill="{{ $deployment->waybill_number ?? 'N/A' }}"
                                            data-date="{{ optional($deployment->deployment_date)->format('M d, Y') ?? 'N/A' }}"
                                            data-category="{{ $category }}"
                                            data-component="{{ $deployment->component }}"
                                            data-brand="{{ optional($deployment->inventory)->brand ?? 'N/A' }}"
                                            data-serial="{{ optional($deployment->inventory)->serial_num ?? 'No Serial' }}"
                                            data-quantity="{{ $deployment->quantity }}"
                                            data-deployed-to="{{ $displayName }}"
                                            data-contact="{{ $displayContact ?: 'N/A' }}"
                                            data-address="{{ $displayAddress ?: 'N/A' }}"
                                            data-office="{{ $displayOffice ?: 'N/A' }}"
                                            data-prepared-by="{{ optional($deployment->user)->name ?? 'N/A' }}"
                                            data-remarks="{{ e($deployment->remarks ?: 'No remarks provided.') }}"
                                            title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
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
.stat-card { border-radius: 10px; transition: transform 0.3s ease; }
.stat-card:hover { transform: translateY(-5px); }
.stat-icon { font-size: 3rem; opacity: 0.3; }
.status-badge {
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    display: inline-block;
}
.hover-shadow:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}
.empty-state { padding: 3rem 1rem; }
.table tbody tr { transition: all 0.2s ease; }
.item-icon {
    width: 40px;
    height: 40px;
    background: rgba(13, 110, 253, 0.1);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Modern Waybill Pill */
.waybill-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background-color: rgba(102, 126, 234, 0.08); /* Soft tint matching your header's indigo/purple gradient */
    color: #4a5dca;
    border: 1px solid rgba(102, 126, 234, 0.15);
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.2s ease-in-out;
}

/* Adds a nice interactive lift when hovering over the table row */
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

/* Monospace font gives tracking and waybill numbers a high-tech, readable layout */
.waybill-text {
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Monaco, monospace;
    letter-spacing: 0.5px;
}
</style>

{{-- Deployment Report Details Modal --}}
<div class="modal fade" id="reportDetailsModal" tabindex="-1" aria-labelledby="reportDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="reportDetailsModalLabel">
                    <i class="bi bi-file-earmark-text me-2"></i> Deployment Report Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    {{-- Left Section: Deployment Info --}}
                    <div class="col-md-6 border-end">
                        <h5 class="text-primary mb-3 fw-bold"><i class="bi bi-truck me-2"></i>Deployment Info</h5>
                        <div class="mb-3">
                            <label class="text-muted small d-block mb-1">Waybill Number</label>
                            <span class="waybill-pill py-1 px-2" id="modalWaybill">-</span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">Date Deployed</label>
                            <p class="fw-semibold mb-0" id="modalDate">-</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">Prepared By</label>
                            <p class="fw-semibold mb-0" id="modalPreparedBy">-</p>
                        </div>
                    </div>

                    {{-- Right Section: Client Info --}}
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3 fw-bold"><i class="bi bi-person-circle me-2"></i>Recipient Info</h5>
                        <div class="mb-3">
                            <label class="text-muted small d-block">Deployed To / Contact Person</label>
                            <p class="fw-semibold mb-0" id="modalDeployedTo">-</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">Contact Number</label>
                            <p class="fw-semibold mb-0" id="modalContact">-</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">Satellite Office</label>
                            <p class="fw-semibold mb-0" id="modalOffice">-</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">Address</label>
                            <p class="fw-semibold mb-0" id="modalAddress">-</p>
                        </div>
                    </div>

                    <div class="col-12">
                        <hr class="my-1">
                    </div>

                    {{-- Bottom Section: Component Details --}}
                    <div class="col-12">
                        <h5 class="text-primary mb-3 fw-bold"><i class="bi bi-box-seam me-2"></i>Component Details</h5>
                        
                        {{-- JavaScript completely controls this container. Keep this div empty! --}}
                        <div id="modalComponentContainer"></div>
                    </div>

                    {{-- Remarks Section --}}
                    <div class="col-12">
                        <div class="bg-light p-3 rounded">
                            <label class="text-muted small d-block fw-bold mb-1"><i class="bi bi-card-text me-1"></i>Remarks</label>
                            <p class="mb-0 text-dark" id="modalRemarks">-</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize standard tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

    // Get the modal element
    const reportDetailsModal = document.getElementById('reportDetailsModal');
    
    if (reportDetailsModal) {
        // Listen to Bootstrap's native show event
        reportDetailsModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        
        // Extract base layout attributes
        const waybill = button.getAttribute('data-waybill') || 'N/A';
        const date = button.getAttribute('data-date') || 'N/A';
        const preparedBy = button.getAttribute('data-prepared-by') || 'N/A';
        const deployedTo = button.getAttribute('data-deployed-to') || 'N/A';
        const contact = button.getAttribute('data-contact') || 'N/A';
        const office = button.getAttribute('data-office') || 'N/A';
        const address = button.getAttribute('data-address') || 'N/A';
        const category = button.getAttribute('data-category') || 'N/A';
        const component = button.getAttribute('data-component') || 'N/A';
        const brand = button.getAttribute('data-brand') || 'N/A';
        const serial = button.getAttribute('data-serial') || 'No Serial';
        const quantity = parseInt(button.getAttribute('data-quantity')) || 1; // Base integer fallback
        const remarks = button.getAttribute('data-remarks') || 'No remarks provided.';

        // Inject base values
        document.getElementById('modalWaybill').textContent = waybill;
        document.getElementById('modalDate').textContent = date;
        document.getElementById('modalPreparedBy').textContent = preparedBy;
        document.getElementById('modalDeployedTo').textContent = deployedTo;
        document.getElementById('modalContact').textContent = contact;
        document.getElementById('modalOffice').textContent = office;
        document.getElementById('modalAddress').textContent = address;
        document.getElementById('modalRemarks').textContent = remarks;

        // Grab container and purge historical lines
        const container = document.getElementById('modalComponentContainer');
        container.innerHTML = '';

        // Loop through the quantity and render structured grid line-items
        for (let i = 1; i <= quantity; i++) {
            const componentRow = `
                <div class="row g-3 mb-3 align-items-center ${i < quantity ? 'border-bottom pb-3' : ''}">
                    <div class="col-md-3">
                        <label class="text-muted small d-block">Category ${quantity > 1 ? '#' + i : ''}</label>
                        <p class="fw-semibold mb-0">${category}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small d-block">Component Name</label>
                        <p class="fw-semibold mb-0">${component}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="text-muted small d-block">Brand / Serial</label>
                        <p class="fw-semibold mb-0"><span>${brand}</span> • <code>${serial}</code></p>
                    </div>
                    <div class="col-md-2 text-md-center">
                        <label class="text-muted small d-block">Item Unit</label>
                        <span class="badge bg-primary fs-6 px-3">1 pc</span>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', componentRow);
        }
    });
    }

    // Fix dropdown z-index overlap issues
    document.querySelectorAll('.dropdown-toggle').forEach(dropdown => {
        dropdown.addEventListener('click', function() {
            const menu = this.nextElementSibling;
            if (menu) menu.style.zIndex = '9999';
        });
    });
});
</script>
@endpush
@endsection