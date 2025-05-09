@extends('layouts.app')

@section('title', 'Lead Details')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header py-3 px-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                <div>
                    <h3 class="mb-0 text-primary">Lead: {{ $lead->name }}</h3>
                    <span class="text-muted small">ID: {{ $lead->id }} | Agent: {{ $lead->agent->name ?? 'Unassigned' }}</span>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-lg rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addCallReportModal">
                        <i class="fas fa-plus me-2"></i>Add Call Report
                    </button>
                </div>
            </div>

            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">

                    <!-- Lead Details Card -->
                    <div class="col-md-4">
                        <!-- Contact Details Card -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Contact Details</h5>
                            </div>
                            <div class="card-body">
                                <!-- Mobile Section -->
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <p class="mb-0"><strong>Mobile:</strong> {{ $lead->mobile }}</p>
                                    <div class="d-flex gap-2">
                                        <a href="https://app.justcall.io/dialer?numbers=+<61><{{ $lead->mobile }}>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill">
                                            <i class="fas fa-phone me-2"></i>JustCall
                                        </a>
                                        <a href="https://www.oakroadestate.com.au/admin/index.php?route=customer/customer&user_token={{ $openCartToken[0]->open_cart_token ?? '' }}&filter_phone={{ $lead->mobile }}" target="_blank" class="btn btn-outline-success btn-sm rounded-pill">
                                            <i class="fas fa-shopping-cart me-2"></i>Open Cart
                                        </a>
                                    </div>
                                </div>

                                <!-- Landline Section -->
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <p class="mb-0"><strong>Landline:</strong> {{ $lead->landline ?? 'N/A' }}</p>
                                    @if($lead->landline)
                                        <div class="d-flex gap-2">
                                            <a href="https://app.justcall.io/dialer?numbers=+<61><{{ $lead->landline }}>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill">
                                                <i class="fas fa-phone me-2"></i>JustCall
                                            </a>
                                            <a href="https://www.oakroadestate.com.au/admin/index.php?route=customer/customer&user_token={{ $openCartToken[0]->open_cart_token ?? '' }}&filter_phone={{ $lead->landline }}" target="_blank" class="btn btn-outline-success btn-sm rounded-pill">
                                                <i class="fas fa-shopping-cart me-2"></i>Open Cart
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Lead Details</h5>
                                @if($lead->callBackSheets->count() > 0)
                                    <span class="badge bg-warning">In Callback Sheet</span>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <p><strong>Name:</strong> {{ $lead->name }}</p>
                                    <p><strong>Email:</strong> {{ $lead->email ?? 'N/A' }}</p>
                                </div>
                                
                                <div class="mb-3">
                                    <p><strong>Source:</strong> 
                                        <span class="badge {{ $lead->source === 'pm' ? 'bg-success' : ($lead->source === 'sam' ? 'bg-primary' : 'bg-secondary') }}">
                                            {{ strtoupper($lead->source) }}
                                        </span>
                                    </p>
                                    <p><strong>Agent:</strong> {{ $lead->agent->name ?? 'Unassigned' }}</p>
                                    <p><strong>Created:</strong> {{ $lead->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                                
                                <div class="mb-3">
                                    <p><strong>Last Call:</strong> 
                                        @if($callHistories->isNotEmpty())
                                            <strong>{{ $callHistories->last()->created_at->format('Y-m-d H:i') }}</strong>
                                        @else
                                            Never
                                        @endif
                                    </p>
                                    <p><strong>Notes:</strong> {{ $lead->note ?? 'None' }}</p>
                                </div>
                                <button class="btn btn-outline-primary btn-lg rounded-pill shadow-sm w-100" data-bs-toggle="offcanvas" data-bs-target="#editLeadOffcanvas">
                                    <i class="fas fa-edit me-2"></i>Edit Lead
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- Tabs Section -->
                    <div class="col-md-8">
                        <ul class="nav nav-tabs" id="leadTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="followups-tab" data-bs-toggle="tab" href="#followups" role="tab">
                                    <i class="fas fa-calendar-check me-1"></i>Today's Followups
                                </a>
                            </li>                            
                            <li class="nav-item">
                                <a class="nav-link" id="call-reports-tab" data-bs-toggle="tab" href="#call-reports" role="tab">
                                    <i class="fas fa-phone me-1"></i>Call Reports
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="orders-tab" data-bs-toggle="tab" href="#orders" role="tab">
                                    <i class="fas fa-receipt me-1"></i>Orders
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="payment-tab" data-bs-toggle="tab" href="#payment" role="tab">
                                    <i class="fas fa-dollar me-1"></i>Payment Details
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content mt-3" id="leadTabsContent">

                            <!-- Followups Tab -->
                            <div class="tab-pane fade show active" id="followups" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Comment</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $today = \Carbon\Carbon::today()->toDateString();
                                                $pendingFollowups = $followups->filter(function($followup) use ($today) {
                                                    return $followup->schedule_date === $today;
                                                });
                                            @endphp
                                            @if($pendingFollowups->isNotEmpty())
                                                @foreach($pendingFollowups as $followup)
                                                    <tr>
                                                        <td>{{ $followup->schedule_date }}</td>
                                                        <td>{{ $followup->schedule_time }}</td>
                                                        <td>{{ $followup->comment ?? 'N/A' }}</td>
                                                        <td>{{ $followup->callStatus->name ?? 'Pending' }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No pending followups for today.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- Call Reports Tab -->
                            <div class="tab-pane fade" id="call-reports" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Medium</th>
                                                <th>Status</th>
                                                <th>Comment</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($callHistories as $call)
                                                <tr>
                                                    <td>{{ $call->id }}</td>
                                                    <td>{{ ucfirst($call->medium) }}</td>
                                                    <td>{{ $call->callStatus->name ?? 'N/A' }}</td>
                                                    <td>{{ $call->comment ?? 'N/A' }}</td>
                                                    <td>{{ $call->created_at->format('Y-m-d H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Orders Tab -->
                            <div class="tab-pane fade" id="orders" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Order No</th>
                                                <th>Order Date</th>
                                                <th>Item</th>
                                                <th>Price</th>
                                                <th>Qty</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($orders as $order)
                                                <tr>
                                                    <td>{{ $order->id }}</td>
                                                    <td>{{ $order->order_no }}</td>
                                                    <td>{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('Y-m-d') : 'N/A' }}</td>
                                                    <td>{{ $order->item }}</td>
                                                    <td>${{ number_format($order->price, 2) }}</td>
                                                    <td>{{ $order->qty }}</td>
                                                    <td>${{ number_format($order->total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                            <!-- Payment Details Card -->
                            <div class="tab-pane fade" id="payment" role="tabpanel">

                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Payment Details</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($payment)
                                            <div class="mb-3">
                                                <p><strong>Card Number:</strong> **** **** **** {{ substr($payment->cardboard, -4) }}</p>
                                                <p><strong>Expiry:</strong> {{ $payment->expiry_month }}/{{ $payment->expiry_year }}</p>
                                                <p><strong>SIVIVI:</strong> ****</p>
                                                <p><strong>Updated:</strong> {{ $payment->updated_at->format('Y-m-d H:i') }}</p>
                                            </div>
                                            <button class="btn btn-outline-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#editPaymentModal">
                                                <i class="fas fa-info me-2"></i>Edit Payment
                                            </button>
                                        @else
                                            <p class="text-muted">No payment details available.</p>
                                            <button class="btn btn-outline-primary btn-lg rounded-pill shadow-sm w-100" data-bs-toggle="modal" data-bs-target="#editPaymentModal">
                                                <i class="fas fa-plus me-2"></i>Add Payment
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Lead Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="editLeadOffcanvas">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Edit Lead</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('divanj.crm.leads.update', $lead->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group mb-3">
                <label for="name" class="form-label">Name *</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="{{ old('name', $lead->name) }}" required>
            </div>
            
            <div class="form-group mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="{{ old('email', $lead->email) }}">
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="mobile" class="form-label">Mobile *</label>
                        <input type="tel" class="form-control" id="mobile" name="mobile" 
                            value="{{ old('mobile', $lead->mobile) }}" 
                            maxlength="20" 
                            pattern="[1-9]\d{8,10}" 
                            placeholder="Enter 9-11 digits (no country code)"
                            >
                        <div class="invalid-feedback">Please enter a valid 10-digit number (cannot start with 0)</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="landline" class="form-label">Landline</label>
                        <input type="tel" class="form-control" id="landline" name="landline" 
                            value="{{ old('landline', $lead->landline) }}"
                            maxlength="20"
                            pattern="[1-9]\d{8,10}" 
                            placeholder="Enter 9-11 digits (no country code)"
                            >
                        <div class="invalid-feedback">Please enter a valid 10-digit number (cannot start with 0)</div>
                    </div>
                </div>
            </div>
            
            <div class="form-group mb-3">
                <label for="source" class="form-label">Source *</label>
                <select class="form-control" id="source" name="source" required>
                    <option value="sam" {{ old('source', $lead->source) == 'sam' ? 'selected' : '' }}>SAM</option>
                    <option value="pm" {{ old('source', $lead->source) == 'pm' ? 'selected' : '' }}>PM</option>
                    <option value="other" {{ old('source', $lead->source) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            
            <div class="form-group mb-3">
                <label for="note" class="form-label">Notes</label>
                <textarea class="form-control" id="note" name="note" rows="3">{{ old('note', $lead->note) }}</textarea>
            </div>
            
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary btn-lg rounded-pill shadow-sm flex-grow-1">Update Lead</button>
                <button type="button" class="btn btn-outline-secondary btn-lg rounded-pill shadow-sm flex-grow-1" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Call Report Modal -->
<div class="modal fade" id="addCallReportModal" tabindex="-1" aria-labelledby="addCallReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCallReportModalLabel">Add Call Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('divanj.crm.call-report.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="medium" class="form-label">Medium *</label>
                                <select class="form-control" id="medium" name="medium" required>
                                    <option value="">Select Medium</option>
                                    <option value="call">Call</option>
                                    <option value="text">Text</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="call_status_id" class="form-label">Status *</label>
                                <select class="form-control" id="call_status_id" name="call_status_id" required>
                                    <option value="">Select Status</option>
                                    @foreach($callStatuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="schedule_date" class="form-label">Followup Date</label>
                                <input type="date" class="form-control" id="schedule_date" name="schedule_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="schedule_time" class="form-label">Followup Time</label>
                                <input type="text" class="form-control" id="schedule_time" name="schedule_time" placeholder="e.g., 14:30">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-lg rounded-pill shadow-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-outline-primary btn-lg rounded-pill shadow-sm">Save Call Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Payment Modal -->
<div class="modal fade" id="editPaymentModal" tabindex="-1" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPaymentModalLabel">{{ $payment ? 'Edit' : 'Add' }} Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('divanj.crm.payment.update', $lead->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="cardboard" class="form-label">Card Number *</label>
                        <input type="text" class="form-control" id="cardboard" name="cardboard" 
                               value="{{ old('cardboard', $payment->cardboard ?? '') }}" 
                               maxlength="20" required
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <small class="text-muted">Enter full card number (digits only, max 20)</small>
                        @error('cardboard')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="expiry_month" class="form-label">Expiry Month *</label>
                                <select class="form-control" id="expiry_month" name="expiry_month" required>
                                    <option value="">Month</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" 
                                            {{ old('expiry_month', $payment->expiry_month ?? '') == $i ? 'selected' : '' }}>
                                            {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                        </option>
                                    @endfor
                                </select>
                                @error('expiry_month')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="expiry_year" class="form-label">Expiry Year *</label>
                                <select class="form-control" id="expiry_year" name="expiry_year" required>
                                    <option value="">Year</option>
                                    @for($i = 25; $i <= 40; $i++)
                                        <option value="{{ $i }}" 
                                            {{ old('expiry_year', $payment->expiry_year ?? '') == $i ? 'selected' : '' }}>
                                            20{{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                @error('expiry_year')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="sivivi" class="form-label">SIVIVI</label>
                                <input type="text" class="form-control" id="sivivi" name="sivivi" 
                                    value="{{ old('sivivi', $payment->sivivi ?? '') }}" 
                                    maxlength="4"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <small class="text-muted">3 or 4 digits only</small>
                                @error('sivivi')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="card_type" class="form-label">Card Type</label>
                                <select class="form-control" id="card_type" name="card_type">
                                    <option value="">Select Card Type</option>
                                    <option value="Visa" {{ old('card_type', $payment->card_type ?? '') == 'Visa' ? 'selected' : '' }}>Visa</option>
                                    <option value="Master" {{ old('card_type', $payment->card_type ?? '') == 'Master' ? 'selected' : '' }}>Master</option>
                                    <option value="Amex" {{ old('card_type', $payment->card_type ?? '') == 'Amex' ? 'selected' : '' }}>American Express</option>
                                    <option value="Other" {{ old('card_type', $payment->card_type ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('card_type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-lg rounded-pill shadow-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-outline-primary btn-lg rounded-pill shadow-sm">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    .card-header {
        padding: 0.75rem 1.25rem;
    }
    .nav-tabs .nav-link {
        border-radius: 0;
        font-weight: 500;
        color: #495057;
        padding: 0.75rem 1.5rem;
    }
    .nav-tabs .nav-link.active {
        background-color: #f8f9fa;
        border-bottom: 3px solid #0d6efd;
        color: #0d6efd;
    }
    .offcanvas {
        max-width: 500px;
    }
    .table th {
        white-space: nowrap;
    }
    .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
    }
    .text-muted {
        font-size: 0.85rem;
    }
    .btn-lg {
        padding: 0.6rem 1.8rem;
        font-size: 1.1rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-sm {
        padding: 0.4rem 1.2rem;
        font-size: 0.95rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-outline-primary {
        border-color: #0d6efd;
        color: #0d6efd;
    }
    .btn-outline-primary:hover {
        background-color: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.2);
    }
    .btn-outline-success {
        border-color: #198754;
        color: #198754;
    }
    .btn-outline-success:hover {
        background-color: #198754;
        color: #fff;
        border-color: #198754;
        box-shadow: 0 4px 8px rgba(25, 135, 84, 0.2);
    }
    .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
    }
    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: #fff;
        border-color: #6c757d;
        box-shadow: 0 4px 8px rgba(108, 117, 125, 0.2);
    }
    .rounded-pill {
        border-radius: 50rem !important;
    }
    .shadow-sm {
        box-shadow: 0 2px 6px rgba(0,0,0,0.15) !important;
    }
    .gap-2 {
        gap: 0.75rem;
    }
    .btn i {
        vertical-align: middle;
    }
</style>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize tabs
        $('#leadTabs a').on('click', function(e) {
            e.preventDefault();
            $(this).tab('show');
        });
        
        // Initialize offcanvas
        var editLeadOffcanvas = new bootstrap.Offcanvas(document.getElementById('editLeadOffcanvas'));
    });


    // Format and validate phone inputs
    $('input[type="tel"]').on('input paste', function(e) {
        // Handle paste event
        if (e.type === 'paste') {
            setTimeout(() => {
                processPhoneNumber(this);
            }, 10);
        } else {
            processPhoneNumber(this);
        }
    }).on('blur', function() {
        validatePhoneNumber(this);
    });

    function processPhoneNumber(input) {
        // Get current cursor position
        const startPos = input.selectionStart;
        
        // Remove all non-digit characters
        let value = input.value.replace(/\D/g, '');
        
        // Remove country code (61) if present at start
        if (value.startsWith('61') && value.length > 9) {
            value = value.substring(2);
        }
        // Remove leading 0 if present
        else if (value.startsWith('0')) {
            value = value.substring(1);
        }
        
        // If still too long, take last 11 digits (handles cases where they paste long numbers)
        if (value.length > 11) {
            value = value.substring(value.length - 11);
        }
        
        // Update the input value
        input.value = value;
        
        // Restore cursor position (adjusted for any removed characters)
        const lengthDiff = input.value.length - value.length;
        input.setSelectionRange(
            Math.max(0, startPos - lengthDiff), 
            Math.max(0, startPos - lengthDiff)
        );
        
        // Validate immediately
        validatePhoneNumber(input);
    }

    function validatePhoneNumber(input) {
        const value = input.value;
        const isValid = /^[1-9]\d{8,10}$/.test(value); // 9-11 digits
        
        if (value && !isValid) {
            $(input).addClass('is-invalid');
            $(input).next('.invalid-feedback').remove();
            $(input).after('<div class="invalid-feedback">Please enter 9-11 digits (no country code, no spaces, cannot start with 0)</div>');
        } else {
            $(input).removeClass('is-invalid');
            $(input).next('.invalid-feedback').remove();
        }
    }


</script>
@endsection