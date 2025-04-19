@extends('layouts.app')

@section ('title', 'Incentives and Fines')
@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Incentives and Fines</h3>
            </div>
            <div class="card-body">
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <select name="employee_id" class="form-control">
                                <option value="">All Employees</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->stage_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-control">
                                <option value="">All Types</option>
                                <option value="incentive" {{ request('type') == 'incentive' ? 'selected' : '' }}>Incentives</option>
                                <option value="fine" {{ request('type') == 'fine' ? 'selected' : '' }}>Fines</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <button class="btn btn-success" type="button" data-bs-toggle="offcanvas" data-bs-target="#createTransactionOffcanvas" aria-controls="createTransactionOffcanvas">
                                Add New
                            </button>
                            <button type="button" class="btn btn-info" onclick="loadSummary()">Show Summary</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id = "incentiveFineTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->date }}</td>
                            <td>{{ $transaction->employee->stage_name }}</td>
                            <td>{{ ucfirst($transaction->type->type) }}</td>
                            <td>{{ $transaction->type->name }}</td>
                            <td class="{{ $transaction->type->type == 'incentive' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->amount }} BDT
                            </td>
                            <td>{{ $transaction->notes }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Offcanvas Component -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="createTransactionOffcanvas" aria-labelledby="createTransactionOffcanvasLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="createTransactionOffcanvasLabel">Add New Transaction</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <form method="POST" action="{{ route('employees.incentives.fines.store') }}">
        @csrf

        <div class="form-group mb-3">
            <label for="employee_id">Employee</label>
            <select name="employee_id" class="form-control" required>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->stage_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="transaction_type_id">Transaction Type</label>
            <select name="transaction_type_id" class="form-control" required>
                <option value="" > </option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" data-type="{{ $type->type }}" data-amount="{{ $type->amount }}">
                        {{ $type->name }} ({{ ucfirst($type->type) }} - {{ $type->amount }} BDT)
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="date">Date</label>
            <input type="date" name="date" class="form-control" required value="{{ date('Y-m-d') }}">
        </div>

        <div class="form-group mb-3">
            <label for="amount">Amount</label>
            <input type="number" name="amount" class="form-control" required step="0.01">
        </div>

        <div class="form-group mb-3">
            <label for="notes">Notes</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Transaction</button>
    </form>
  </div>
</div>


<!-- Summary Modal -->
<div class="modal fade" id="summaryModal" tabindex="-1" aria-labelledby="summaryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Transaction Summary</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Total Incentives:</strong> <span id="summaryIncentives">-</span> BDT</p>
        <p><strong>Total Fines:</strong> <span id="summaryFines">-</span> BDT</p>
        <p><strong>Net Balance:</strong> <span id="summaryNet">-</span> BDT</p>
      </div>
    </div>
  </div>
</div>




@endsection

@section('js')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#incentiveFineTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "order": [[0, 'desc']]  

        });
    });




    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.querySelector('select[name="transaction_type_id"]');
        const amountInput = document.querySelector('input[name="amount"]');

        typeSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const amount = selectedOption.getAttribute('data-amount');
            amountInput.value = amount;
        });
    });


    function loadSummary() {
        const employeeId = document.querySelector('select[name="employee_id"]').value;
        const startDate = document.querySelector('input[name="date"]').value || '{{ date('Y-m-d') }}';
        const endDate = startDate;

        if (!startDate) {
            alert('Please select a date to show summary.');
            return;
        }

        fetch(`{{ route('employees.incentives.fines.summary') }}?employee_id=${employeeId}&start_date=${startDate}&end_date=${endDate}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('summaryIncentives').innerText = data.incentives;
                document.getElementById('summaryFines').innerText = data.fines;
                document.getElementById('summaryNet').innerText = data.net;

                new bootstrap.Modal(document.getElementById('summaryModal')).show();
            })
            .catch(err => {
                console.error(err);
                alert('Failed to fetch summary.');
            });
    }

</script>
@endsection
