@extends('layouts.app')

@section('title', 'Career Applicants Management')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card border-0 shadow-lg">
            <div class="card-header ">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="fw-bold mb-0">
                        <i class="fas fa-user-tie me-2"></i> Career Applicants
                    </h3>
                    <div>
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <div class="dropdown d-inline-block ms-2">
                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-download me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('career-applicants.export', ['status' => 'new']) }}">
                                        <i class="fas fa-clock text-warning me-2"></i> New
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('career-applicants.export', ['status' => 'shortlisted']) }}">
                                        <i class="fas fa-star text-primary me-2"></i> Shortlist
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('career-applicants.export', ['status' => 'interview']) }}">
                                        <i class="fas fa-comments text-info me-2"></i> Interview
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('career-applicants.export', ['status' => 'mock']) }}">
                                        <i class="fas fa-headset text-secondary me-2"></i> Mock Calls
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('career-applicants.export') }}">
                                        <i class="fas fa-table text-success me-2"></i> All Candidates
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-3">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table id="applicantTable" class="table table-hover table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-nowrap">ID</th>
                                <th class="text-nowrap">Position</th>
                                <th class="text-nowrap">Name</th>
                                <th class="text-nowrap">Contact</th>
                                <th class="text-nowrap">Experience</th>
                                <th class="text-nowrap">Education</th>
                                <th class="text-nowrap">Applied On</th>
                                <th class="text-nowrap text-center">Status</th>
                                <th class="text-nowrap text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applicants as $applicant)
                            <tr>
                                <td class="fw-semibold">#{{ $applicant->id }}</td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        {{ $applicant->position }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <!-- <div class="avatar avatar-sm me-2">
                                            <span class="avatar-text bg-info rounded-circle">
                                                {{ substr($applicant->name, 0, 1) }}
                                            </span>
                                        </div> -->
                                        <div>
                                            <h6 class="mb-0">{{ $applicant->name }}</h6>
                                            <small class="text-muted">{{ $applicant->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $applicant->phone }}</td>
                                <td>
                                    @if($applicant->total_experience)
                                        <span class="badge bg-success bg-opacity-60 text-success">
                                            {{ $applicant->total_experience }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                            No experience
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        {{ $applicant->last_education }}<br>
                                        <span class="text-muted">{{ $applicant->last_education_year }}</span>
                                    </small>
                                </td>
                                <td>
                                    <span class="d-block">{{ $applicant->created_at->format('d M Y') }}</span>
                                    <small class="text-muted">{{ $applicant->created_at->diffForHumans() }}</small>
                                </td>
                                <td class="text-center">
                                    @php
                                        $status = $applicant->shortlisted;
                                        $statusMap = [
                                            0 => ['label' => 'New', 'class' => 'warning', 'icon' => 'fa-clock'],
                                            1 => ['label' => 'Shortlisted', 'class' => 'primary', 'icon' => 'fa-star'],
                                            2 => ['label' => 'Rejected', 'class' => 'danger', 'icon' => 'fa-times-circle'],
                                            3 => ['label' => 'Initial Interview', 'class' => 'info', 'icon' => 'fa-comments'],
                                            4 => ['label' => 'Mock Calls', 'class' => 'dark', 'icon' => 'fa-headset'],
                                            5 => ['label' => 'Hired', 'class' => 'success', 'icon' => 'fa-check-circle'],
                                        ];
                                        $currentStatus = $statusMap[$status] ?? $statusMap[0];
                                    @endphp

                                    <span class="badge bg-{{ $currentStatus['class'] }} bg-opacity-60 text-white">
                                        <i class="fas {{ $currentStatus['icon'] }} me-1"></i> {{ $currentStatus['label'] }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" 
                                                data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <button class="dropdown-item" 
                                                        data-bs-toggle="offcanvas" 
                                                        data-bs-target="#offcanvas_applicant_{{ $applicant->id }}">
                                                    <i class="fas fa-eye me-2"></i> View Details
                                                </button>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" 
                                                   href="{{ asset('storage/app/public/' . $applicant->resume_upload) }}" 
                                                   target="_blank">
                                                    <i class="fas fa-file-pdf me-2"></i> View Resume
                                                </a>
                                            </li>
                                            <li>
                                                <form id="shortlistForm_{{ $applicant->id }}" action="{{ route('career-applicants.update-status', $applicant->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="1">
                                                    <button type="button" class="dropdown-item text-success" onclick="updateStatus('shortlistForm_{{ $applicant->id }}')">
                                                        <i class="fas fa-check-circle me-2"></i> Shortlist
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form id="rejectForm_{{ $applicant->id }}" action="{{ route('career-applicants.update-status', $applicant->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="2">
                                                    <button type="button" class="dropdown-item text-danger" onclick="updateStatus('rejectForm_{{ $applicant->id }}')">
                                                        <i class="fas fa-times-circle me-2"></i> Reject
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Applicant Detail Offcanvas -->
@foreach($applicants as $applicant)
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_applicant_{{ $applicant->id }}">
    <div class="offcanvas-header border-bottom">
        <div>
            <h5 class="offcanvas-title fw-bold">{{ $applicant->name }}</h5>
            <p class="mb-0 text-muted">Applied for: {{ $applicant->position }}</p>
            <div class="mt-2">
                @switch($applicant->shortlisted)
                    @case(1)
                        <span class="badge bg-success bg-opacity-15 text-white">
                            <i class="fas fa-check me-1"></i> Shortlisted
                        </span>
                        @break
                    @case(2)
                        <span class="badge bg-danger bg-opacity-15 text-white">
                            <i class="fas fa-times me-1"></i> Rejected
                        </span>
                        @break
                    @case(3)
                        <span class="badge bg-info bg-opacity-15 text-white">
                            <i class="fas fa-comments me-1"></i> Initial Interview
                        </span>
                        @break
                    @case(4)
                        <span class="badge bg-purple bg-opacity-15 text-white">
                            <i class="fas fa-phone me-1"></i> Mock Calls
                        </span>
                        @break
                    @case(5)
                        <span class="badge bg-primary bg-opacity-15 text-white">
                            <i class="fas fa-trophy me-1"></i> Hired
                        </span>
                        @break
                    @default
                        <span class="badge bg-warning bg-opacity-15 text-white">
                            <i class="fas fa-clock me-1"></i> New
                        </span>
                @endswitch
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <!-- Basic Information Section -->
        <div class="mb-4">
            <h6 class="fw-bold mb-3"><i class="fas fa-user me-2"></i> Basic Information</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-muted small mb-1">Email</label>
                    <p class="mb-0">{{ $applicant->email }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small mb-1">Phone</label>
                    <p class="mb-0">{{ $applicant->phone }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small mb-1">Age</label>
                    <p class="mb-0">{{ $applicant->age }} years</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small mb-1">Area</label>
                    <p class="mb-0">{{ $applicant->area }}</p>
                </div>
                <!-- <div class="col-12">
                    <label class="form-label text-muted small mb-1">Address</label>
                    <p class="mb-0">{{ $applicant->address ?? 'N/A' }}</p>
                </div> -->
            </div>
        </div>

        <!-- Education & Experience Section -->
        <div class="mb-4">
            <h6 class="fw-bold mb-3"><i class="fas fa-graduation-cap me-2"></i> Education & Experience</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-muted small mb-1">Last Education</label>
                    <p class="mb-0">{{ $applicant->last_education }} ({{ $applicant->last_education_year }})</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small mb-1">Institute</label>
                    <p class="mb-0">{{ $applicant->last_education_institute }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small mb-1">Last Experience</label>
                    <p class="mb-0">{{ $applicant->last_experience ?? 'N/A' }}</p>
                </div>
                <div class="col-6">
                    <label class="form-label text-muted small mb-1">Total Experience</label>
                    <p class="mb-0">{{ $applicant->total_experience ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Application Documents Section -->
        <div class="mb-4">
            <h6 class="fw-bold mb-3"><i class="fas fa-file-alt me-2"></i> Application Documents</h6>
            <div class="d-flex gap-2">
                <a href="{{ asset('storage/app/public/' . $applicant->resume_upload) }}" 
                   target="_blank" 
                   class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-file-pdf me-2"></i> View Resume
                </a>
                <a href="{{ asset('storage/app/public/' . $applicant->resume_upload) }}" 
                   download 
                   class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-download me-2"></i> Download
                </a>
            </div>
        </div>

        <!-- Application Timeline Section -->
        <div class="mb-4">
            <h6 class="fw-bold mb-3"><i class="fas fa-history me-2"></i> Application Timeline</h6>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-point">                        
                    </div>
                    <div class="timeline-content">
                        <h6 class="fw-bold mb-1">Application Submitted</h6>
                        <p class="text-muted small mb-0">{{ $applicant->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-point">                        
                    </div>
                    <div class="timeline-content">
                        <h6 class="fw-bold mb-1">Last Update</h6>
                        <p class="text-muted small mb-0">{{ $applicant->updated_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                <!-- You can add more timeline items here for status changes -->
            </div>
        </div>

        <!-- Status Actions Section -->
        <div class="border-top pt-3 mt-4">
            <h6 class="fw-bold mb-3"><i class="fas fa-tasks me-2"></i> Update Status</h6>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <form action="{{ route('career-applicants.update-status', $applicant->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="1">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-check me-2"></i> Shortlist
                    </button>
                </form>
                <form action="{{ route('career-applicants.update-status', $applicant->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="2">
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-times me-2"></i> Reject
                    </button>
                </form>
                <form action="{{ route('career-applicants.update-status', $applicant->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="3">
                    <button type="submit" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-comments me-2"></i> Initial Interview
                    </button>
                </form>
                <form action="{{ route('career-applicants.update-status', $applicant->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="4">
                    <button type="submit" class="btn btn-outline-purple btn-sm">
                        <i class="fas fa-phone me-2"></i> Mock Calls
                    </button>
                </form>
                <form action="{{ route('career-applicants.update-status', $applicant->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="5">
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-trophy me-2"></i> Hired
                    </button>
                </form>
            </div>

            <!-- Notes Section -->
            <div class="notes-section">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0"><i class="fas fa-comment me-2"></i> Notes</h6>
                    <button class="btn btn-link btn-sm p-0" onclick="toggleNotes({{ $applicant->id }})">
                        <i class="fas fa-edit"></i> {{ $applicant->notes ? 'Edit' : 'Add' }} Notes
                    </button>
                </div>
                
                @if($applicant->notes)
                    <div class="mb-3 p-3 bg-light rounded">
                        <p class="mb-0">{{ $applicant->notes }}</p>
                    </div>
                @endif
                
                <div id="notesForm_{{ $applicant->id }}" style="display: {{ $applicant->notes ? 'none' : 'block' }};">
                    <form action="{{ route('career-applicants.add-note', $applicant->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea name="note" class="form-control" rows="3" 
                                    placeholder="Add your notes about this applicant...">{{ old('note', $applicant->notes) }}</textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light btn-sm" 
                                    onclick="toggleNotes({{ $applicant->id }})">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                Save Note
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach


<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Applicants</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm" method="GET" action="{{ route('career-applicants.index') }}">
                    <!-- Position Filter -->
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <select class="form-select" name="position">
                            <option value="">All Positions</option>
                            @foreach($positions as $position)
                                <option value="{{ $position }}" {{ request('position') == $position ? 'selected' : '' }}>
                                    {{ $position }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Status Filter -->
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">All Statuses</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>New</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Shortlisted</option>
                            <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Rejected</option>
                            <option value="3" {{ request('status') === '3' ? 'selected' : '' }}>Initial Interview</option>
                            <option value="4" {{ request('status') === '4' ? 'selected' : '' }}>Mock Calls</option>
                            <option value="5" {{ request('status') === '5' ? 'selected' : '' }}>Hired</option>
                        </select>
                    </div>
                    
                    <!-- Experience Level Filter -->
                    <div class="mb-3">
                        <label class="form-label">Experience Level</label>
                        <select class="form-select" name="experience">
                            <option value="">Any Experience</option>
                            @foreach(['Fresher', '1-3 years', '3-5 years', '5+ years'] as $experience)
                                <option value="{{ $experience }}" {{ request('experience') == $experience ? 'selected' : '' }}>
                                    {{ $experience }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Education Level Filter -->
                    <div class="mb-3">
                        <label class="form-label">Education Level</label>
                        <select class="form-select" name="education">
                            <option value="">Select Education Level</option>
                            @foreach(['High School', 'Diploma', 'Bachelor\'s Degree', 'Master\'s Degree', 'Others'] as $education)
                                <option value="{{ $education }}" {{ request('education') == $education ? 'selected' : '' }}>
                                    {{ $education }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Date Range Filter -->
                    <div class="mb-3">
                        <label class="form-label">Date Range</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                            <span class="input-group-text">to</span>
                            <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                        </div>
                    </div>
                    
                    <!-- Hidden fields for sorting/pagination -->
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <input type="hidden" name="direction" value="{{ request('direction') }}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="resetFilters">
                    <i class="fas fa-undo me-1"></i> Reset
                </button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" form="filterForm">Apply Filters</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
<style>
    .card {
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .card-header {
        background: linear-gradient(135deg, #4e73df, #224abe);
        color: #fff;
        padding: 1.25rem 1.5rem;
        border-bottom: none;
    }

    .card-header h3 {
        font-size: 1.35rem;
    }

    .table th {
        text-transform: uppercase;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        background-color: #f9f9f9;
    }

    .table td {
        vertical-align: middle;
    }

    .badge {
        border-radius: 6px;
        font-size: 0.75rem;
        padding: 0.4em 0.6em;
    }

    .avatar {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        background-color: #e9ecef;
        color: #333;
        border-radius: 50%;
        font-size: 0.9rem;
    }

    .dropdown-menu {
        font-size: 0.9rem;
    }

    .dropdown-item i {
        width: 18px;
    }

    .offcanvas-header {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .offcanvas-body h6 {
        color: #4e73df;
    }

    .offcanvas-body label {
        font-weight: 500;
        color: #6c757d;
        font-size: 0.85rem;
    }

    .offcanvas-body p {
        font-weight: 500;
        color: #212529;
    }

    .btn-sm {
        font-size: 0.82rem;
        border-radius: 6px;
    }

    .offcanvas-large {
        width: 600px;
    }

    .modal-content {
        border-radius: 12px;
    }

    .modal-header {
        border-bottom: 1px solid #dee2e6;
        background: #f5f7fb;
    }

    .modal-title {
        font-weight: 600;
    }

    .form-select,
    .form-control {
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
    }

    #applicantTable_wrapper .top {
        margin-bottom: 1rem;
    }

    .dataTables_filter input {
        border-radius: 8px;
        padding: 0.45rem 0.75rem;
    }

    .offcanvas-large {
    width: 600px;
    max-width: 90%;
}

.timeline {
    position: relative;
    padding-left: 1rem;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.timeline-point {
    position: absolute;
    left: -1rem;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #4e73df;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #4e73df;
}

.timeline-content {
    padding-left: 1.5rem;
}

.btn-outline-purple {
    color: #6f42c1;
    border-color: #6f42c1;
}

.btn-outline-purple:hover {
    color: white;
    background-color: #6f42c1;
    border-color: #6f42c1;
}

.notes-section {
    background-color: #f9f9f9;
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1rem;
}

/* Add to your CSS section */
.dropdown-menu {
    min-width: 200px;
}
.dropdown-item i {
    width: 18px;
    text-align: center;
}

</style>

@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize DataTable
        const table = $('#applicantTable').DataTable({
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search applicants...",
            },
            initComplete: function () {
                // Style search input
                $('.dataTables_filter input').addClass('form-control form-control-sm');

            }
        });

        // Export button action
        $('#exportBtn').click(function () {
            alert('Export functionality will be implemented here');
        });

        // Apply filter modal action
        $('#applyFilter').click(function () {
            $('#filterModal').modal('hide');
            alert('Filters will be applied here');
        });
    });

    function toggleNotes(applicantId) {
        const notesForm = document.getElementById(`notesForm_${applicantId}`);
        notesForm.style.display = notesForm.style.display === 'none' ? 'block' : 'none';
    }

    // Initialize all notes forms to be hidden if they have existing notes
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($applicants as $applicant)
            @if($applicant->notes)
                document.getElementById('notesForm_{{ $applicant->id }}').style.display = 'none';
            @endif
        @endforeach
    });

    // Set status for an applicant via fetch API
    function setStatus(applicantId, status) {
        if (confirm("Are you sure you want to update this applicant's status?")) {
            fetch(`/career-applicants/${applicantId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(err => console.error('Status update failed:', err));
        }
    }




    function updateStatus(formId) {
    if (confirm('Are you sure you want to update this applicant\'s status?')) {
        const form = document.getElementById(formId);
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="button"]');
        
        // Store original button HTML
        const originalHtml = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
        submitBtn.disabled = true;
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
            } else {
                return response.json().then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        throw new Error(data.message || 'Failed to update status');
                    }
                });
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
            submitBtn.innerHTML = originalHtml;
            submitBtn.disabled = false;
        });
    }
}


$(document).ready(function() {
    // Handle form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get the form element
        const form = $(this);
        
        // Build the query string while excluding empty values
        const params = new URLSearchParams();
        
        form.find('select[name], input[name]').each(function() {
            const $el = $(this);
            const name = $el.attr('name');
            const value = $el.val();
            
            // Only include non-empty values (except for status which can be 0)
            if (value !== '' && value !== null && 
               (name !== 'status' || value !== '')) {
                params.append(name, value);
            }
        });
        
        // Get the base URL
        const url = form.attr('action');
        
        // Redirect with the query string
        window.location.href = url + '?' + params.toString();
    });
    
    // Reset all filters
    $('#resetFilters').click(function() {
        // Clear all form inputs
        $('#filterForm')[0].reset();
        
        // Redirect to base URL without any parameters
        window.location.href = "{{ route('career-applicants.index') }}";
    });
    
    // Initialize date pickers with current values from URL
    const urlParams = new URLSearchParams(window.location.search);
    $('input[name="start_date"]').val(urlParams.get('start_date') || '');
    $('input[name="end_date"]').val(urlParams.get('end_date') || '');
    
    // Close modal after form submission (if not redirecting)
    $('#filterModal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
    });
});


</script>

@endsection