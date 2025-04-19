@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Invoices</h3>
                <a href="{{ route('invoices.generate') }}" class="btn btn-primary">
                   <i class="ti ti-square-rounded-plus me-2"></i> Generate Invoice
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-bordered" id = "invoiceTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Invoice No</th>
                            <th>Client</th>
                            <th>Work Start</th>
                            <th>Work End</th>
                            <th>Invoice Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->id }}</td>
                                <td>{{ $invoice->invoice_no }}</td>
                                <td>{{ $invoice->client->company }}</td>
                                <td>{{ $invoice->work_start_date }}</td>
                                <td>{{ $invoice->work_end_date }}</td>
                                <td>{{ $invoice->invoice_date }}</td>
                                <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                <td>{{ $invoice->status }}</td>
                                <td>
                                    <div class="d-flex">
                                        @if($invoice->status == 'pending')
                                            <form action="{{ route('invoices.markInvoicePaid', $invoice->id) }}" method="POST" class="me-1">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" title="Mark as Paid">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="{{ route('invoices.view', $invoice->id) }}" class="btn btn-info btn-sm">View</a>
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
@endsection

@section('css')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
@endsection

@section('js')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#invoiceTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "order": [[0, 'desc']]  

        });
    });
</script>
@endsection