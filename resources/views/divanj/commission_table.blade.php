@extends('layouts.app')

@section('title', 'Commission History For Agent')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0">My Sales Commission History</h4>
                    <p style="font-weight:400;"> Check your commission carefully. Check your sales table as well. If you have any pending/transfered sales, get the confirmation from the client and go to Sales Report and Import your sales.</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="commissionTable" class="table table-striped table-hover table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Week</th>
                                    <th>Target</th>
                                    <th>Achieved</th>
                                    <th>Weekday Sales</th>
                                    <th>Weekend Sales</th>
                                    <th>Commission Type</th>
                                    <th>Commission</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($commissions as $commission)
                                <tr>
                                    <td>
                                        {{ \Carbon\Carbon::parse($commission->start_date)->format('M d') }} - 
                                        {{ \Carbon\Carbon::parse($commission->end_date)->format('M d, Y') }}
                                    </td>
                                    <td class="text-center">{{ $commission->target }}</td>
                                    <td class="text-center">{{ $commission->achieved_qty }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $commission->weekday_sales_qty }} units</span><br>
                                        ${{ number_format($commission->weekday_sales_amount, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $commission->weekend_sales_qty }} units</span><br>
                                        ${{ number_format($commission->weekend_sales_amount, 2) }}
                                    </td>
                                    <td>
                                        @if($commission->commission_type == 'fixed')
                                            <span class="badge bg-success">Fixed + Bonus</span>
                                        @elseif($commission->commission_type == 'mixed')
                                            <span class="badge bg-primary">Mixed</span>
                                        @else
                                            <span class="badge bg-danger">Weekend Only</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold">${{ number_format($commission->commission_amount, 2) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary view-details" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailsModal"
                                                data-week="{{ \Carbon\Carbon::parse($commission->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($commission->end_date)->format('M d, Y') }}"
                                                data-target="{{ $commission->target }}"
                                                data-achieved="{{ $commission->achieved_qty }}"
                                                data-weekday="{{ $commission->weekday_sales_qty }} units (${{ number_format($commission->weekday_sales_amount, 2) }})"
                                                data-weekend="{{ $commission->weekend_sales_qty }} units (${{ number_format($commission->weekend_sales_amount, 2) }})"
                                                data-base="${{ number_format($commission->base_commission, 2) }}"
                                                data-optiona="${{ number_format($commission->option_a_amount, 2) }}"
                                                data-optionb="${{ number_format($commission->option_b_amount, 2) }}"
                                                data-optionc="${{ number_format($commission->option_c_amount, 2) }}"
                                                data-type="{{ $commission->commission_type }}"
                                                data-final="${{ number_format($commission->commission_amount, 2) }}">
                                            <i class="fas fa-eye"></i> View
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

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Commission Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Week:</strong> <span id="detail-week"></span>
                    </div>
                    <div class="col-md-3">
                        <strong>Target:</strong> <span id="detail-target"></span>
                    </div>
                    <div class="col-md-3">
                        <strong>Achieved:</strong> <span id="detail-achieved"></span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                Weekday Sales
                            </div>
                            <div class="card-body">
                                <p id="detail-weekday"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                Weekend Sales
                            </div>
                            <div class="card-body">
                                <p id="detail-weekend"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                Commission Calculation
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <p><strong>Base Commission:</strong><br>
                                        <span id="detail-base"></span></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>Option A (Fixed):</strong><br>
                                        <span id="detail-optiona"></span></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>Option B (Mixed):</strong><br>
                                        <span id="detail-optionb"></span></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>Option C (Weekend):</strong><br>
                                        <span id="detail-optionc"></span></p>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Selected Option:</strong><br>
                                        <span id="detail-type" class="badge"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Final Commission:</strong><br>
                                        <span id="detail-final" class="fw-bold"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .badge {
        font-size: 0.9em;
        padding: 5px 8px;
    }
    .card-header {
        font-weight: 600;
    }
    .table thead th {
        vertical-align: middle;
    }
</style>
@endsection

@section('js')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<!-- Bootstrap JS -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> -->
<script>
    $(document).ready(function() {
        $('#commissionTable').DataTable({
            "order": [[0, "desc"]],
            "responsive": true,
            "pageLength": 10
        });

        // Handle details modal
        $('.view-details').on('click', function() {
            const week = $(this).data('week');
            const target = $(this).data('target');
            const achieved = $(this).data('achieved');
            const weekday = $(this).data('weekday');
            const weekend = $(this).data('weekend');
            const base = $(this).data('base');
            const optiona = $(this).data('optiona');
            const optionb = $(this).data('optionb');
            const optionc = $(this).data('optionc');
            const type = $(this).data('type');
            const final = $(this).data('final');

            $('#detail-week').text(week);
            $('#detail-target').text(target);
            $('#detail-achieved').text(achieved);
            $('#detail-weekday').text(weekday);
            $('#detail-weekend').text(weekend);
            $('#detail-base').text(base);
            $('#detail-optiona').text(optiona);
            $('#detail-optionb').text(optionb);
            $('#detail-optionc').text(optionc);
            $('#detail-final').text(final);

            // Set badge color based on type
            const typeBadge = $('#detail-type');
            typeBadge.text(type.charAt(0).toUpperCase() + type.slice(1));
            
            if (type === 'fixed') {
                typeBadge.removeClass('bg-primary bg-danger').addClass('bg-success');
            } else if (type === 'mixed') {
                typeBadge.removeClass('bg-success bg-danger').addClass('bg-primary');
            } else {
                typeBadge.removeClass('bg-success bg-primary').addClass('bg-danger');
            }
        });
    });
</script>
@endsection