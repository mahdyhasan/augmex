@extends('layouts.app')

@section('title', 'Employees')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Employees</h3>
                    <a href="" class="btn btn-primary">Add Employee</a>
                </div>
                <div class="card-body">

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#employeeTable').DataTable();
        });
    </script>
@endsection
