@extends('layouts.app')

@section('title', 'Payroll Deductions Breakdown')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center ">
                    <h3 class="mb-0">Deduction Breakdown for {{ $payroll->employee->user->name }}</h3>
                    <a href="{{ route('payrolls.index') }}" class="btn btn-light bg-dark text-white">
                        <i class="fas fa-arrow-left"></i> Back to Payroll
                    </a>
                </div>
                <p class="mb-0 mt-2">
                    Period: {{ $payroll->pay_period_start->format('d M Y') }} - {{ $payroll->pay_period_end->format('d M Y') }}
                </p>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <!-- Working Days Summary -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Working Days Summary</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Total Working Days
                                        <span class="badge bg-primary rounded-pill">{{ $workingDays }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Days Present
                                        <span class="badge bg-success rounded-pill">{{ $attendanceCount }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Paid Leave Days
                                        <span class="badge bg-info rounded-pill">{{ $paidLeaveDays }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        LWP (Unpaid Leave) Days
                                        <span class="badge bg-warning rounded-pill">{{ $lwpDays }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Other Absences
                                        <span class="badge bg-danger rounded-pill">{{ $absentDays }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Deduction Calculation -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Deduction Calculation</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Per Day Salary (Salary ÷ 30)
                                    <span>{{ number_format($perDaySalary, 2) }} BDT</span>
                                </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Deduction for LWP Days
                                        <span class="text-danger">- {{ number_format($deductionBreakdown['lwp_deduction'], 2) }} BDT</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Deduction for Absences
                                        <span class="text-danger">- {{ number_format($deductionBreakdown['absence_deduction'], 2) }} BDT</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                        <strong>Total Deduction</strong>
                                        <strong class="text-danger">- {{ number_format($deductionBreakdown['total_deduction'], 2) }} BDT</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Leave Details -->
                @if(!empty($leaveDetails))
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Leave Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Leave Type</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Days</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leaveDetails as $leave)
                                    <tr>
                                        <td>{{ $leave['type'] }}</td>
                                        <td>{{ $leave['start'] }}</td>
                                        <td>{{ $leave['end'] }}</td>
                                        <td>{{ $leave['days'] }}</td>
                                        <td>
                                            @if($leave['status'] === 'Unpaid')
                                                <span class="badge bg-warning">Unpaid</span>
                                            @else
                                                <span class="badge bg-success">Paid</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection