@extends('layouts.app')

@section('title', 'Order History')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header py-3 px-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                <h3 class="mb-0 text-primary">Order History</h3>
            </div>

            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table id="orderHistoryTable" class="table table-hover table-bordered align-middle">
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
                                    <td>{{ $order->order_date ? $order->order_date : 'N/A' }}</td>
                                    <td>{{ $order->item }}</td>
                                    <td>{{ number_format($order->price, 2) }}</td>
                                    <td>{{ $order->qty }}</td>
                                    <td>{{ number_format($order->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<style>
    .table th, .table td {
        vertical-align: middle;
    }
</style>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#orderHistoryTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true,
            pageLength: 10,
            order: [[1, 'desc']]
        });
    });
</script>
@endsection