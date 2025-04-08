@extends('layouts.app')

@section('title', 'Commission History For Agent')

@section('content')

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header bg-white border-bottom-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">My Commission History</h4>
                            <div class="badge bg-light text-dark">
                                {{ $commissions->count() }} records
                            </div>
                        </div>
                        <p class="text-muted mt-2 mb-0">
                            Review your commission details below. Verify your sales records and report any discrepancies.
                        </p>
                    </div>
                    
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="commissionTable" class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Week</th>
                                        <th class="text-end">Target</th>
                                        <th class="text-end">Achieved</th>
                                        <th class="text-end">Weekday</th>
                                        <th class="text-end">Weekend</th>
                                        <th>Type</th>
                                        <th class="text-end">Commission</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($commissions as $commission)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{ $commission->start_date }} to</span>
                                                <small class="text-muted">{{ $commission->end_date }}</small>
                                            </div>
                                        </td>
                                        <td class="text-end">{{ $commission->target }}</td>
                                        <td class="text-end {{ $commission->achieved_qty >= $commission->target ? 'text-success' : 'text-danger' }}">
                                            {{ $commission->achieved_qty }}
                                            @if($commission->achieved_qty >= $commission->target)
                                                <i class="ti ti-check ms-1"></i>
                                            @else
                                                <i class="ti ti-x ms-1"></i>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <span class="d-block">{{ $commission->weekday_sales_qty }}</span>
                                            <small class="text-muted">{{ number_format($commission->weekday_sales_amount, 2) }}</small>
                                        </td>
                                        <td class="text-end">
                                            <span class="d-block">{{ $commission->weekend_sales_qty }}</span>
                                            <small class="text-muted">{{ number_format($commission->weekend_sales_amount, 2) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{
                                                $commission->commission_type == 'a' ? 'primary' : 
                                                ($commission->commission_type == 'b' ? 'info' : 'success')
                                            }}">
                                                {{ strtoupper($commission->commission_type) }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($commission->commission_amount, 2) }}</td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-secondary view-details" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#detailsModal"
                                                    data-week="{{ $commission->start_date }} to {{ $commission->end_date }}"
                                                    data-target="{{ $commission->target }}"
                                                    data-achieved="{{ $commission->achieved_qty }}"
                                                    data-weekday="{{ $commission->weekday_sales_qty }} ({{ number_format($commission->weekday_sales_amount, 2) }})"
                                                    data-weekend="{{ $commission->weekend_sales_qty }} ({{ number_format($commission->weekend_sales_amount, 2) }})"
                                                    data-base="{{ number_format($commission->base_commission, 2) }}"
                                                    data-optiona="{{ number_format($commission->option_a_amount, 2) }}"
                                                    data-optionb="{{ number_format($commission->option_b_amount, 2) }}"
                                                    data-optionc="{{ number_format($commission->option_c_amount, 2) }}"
                                                    data-type="{{ $commission->commission_type }}"
                                                    data-final="{{ number_format($commission->commission_amount, 2) }}">
                                                Details
                                            </button>
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
    </div>
</div>


<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Commission Breakdown</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <h6 class="mb-3 text-muted" id="detail-week"></h6>
                    <div class="row">
                        <div class="col-6">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Target:</span>
                                <strong id="detail-target"></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Achieved:</span>
                                <strong id="detail-achieved"></strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-light">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between">
                                        <small>Weekday:</small>
                                        <small id="detail-weekday"></small>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small>Weekend:</small>
                                        <small id="detail-weekend"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-top pt-3">
                    <h6 class="mb-3">Commission Calculation</h6>
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="p-2 border rounded text-center">
                                <small class="d-block text-muted">Base</small>
                                <strong id="detail-base"></strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 border rounded text-center">
                                <small class="d-block text-muted">Option A</small>
                                <strong id="detail-optiona"></strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 border rounded text-center">
                                <small class="d-block text-muted">Option B</small>
                                <strong id="detail-optionb"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="p-2 border rounded text-center">
                                <small class="d-block text-muted">Option C</small>
                                <strong id="detail-optionc"></strong>
                            </div>
                        </div>
                        <div class="col-8">
                            <div class="p-2 border rounded">
                                <div class="d-flex justify-content-between">
                                    <span>Selected:</span>
                                    <span id="detail-type" class="badge"></span>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <strong>Final Commission:</strong>
                                    <strong id="detail-final"></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .table thead th {
        border-bottom: 0;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .table > :not(:first-child) {
        border-top: 1px solid #ececec;
    }
    .badge {
        font-size: 0.7rem;
        padding: 0.35rem 0.5rem;
        font-weight: 500;
    }
    .view-details {
        min-width: 80px;
    }
</style>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#commissionTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 12,
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
            },
            dom: '<"top"f>rt<"bottom"ip><"clear">'
        });

        // Handle details modal
        $('.view-details').on('click', function() {
            const data = $(this).data();
            $('#detail-week').text(data.week);
            $('#detail-target').text(data.target);
            $('#detail-achieved').text(data.achieved);
            $('#detail-weekday').text(data.weekday);
            $('#detail-weekend').text(data.weekend);
            $('#detail-base').text(data.base);
            $('#detail-optiona').text(data.optiona);
            $('#detail-optionb').text(data.optionb);
            $('#detail-optionc').text(data.optionc);
            $('#detail-final').text(data.final);

            const typeBadge = $('#detail-type');
            typeBadge.text('Option ' + data.type.toUpperCase());
            typeBadge.removeClass().addClass('badge bg-' + 
                (data.type === 'a' ? 'primary' : (data.type === 'b' ? 'info' : 'success')));
        });
    });
</script>
@endsection