@extends('layouts.app')

@section('title', 'Income Statement')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Income Statement</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="table">
                            <tr>
                                <th>Description</th>
                                <th class="text-end">Amount (BDT)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Revenue Section -->
                            <tr class="table-primary">
                                <td colspan="2"><strong>Revenue</strong></td>
                            </tr>
                            <tr>
                                <td>Service Revenue</td>
                                <td class="text-end">1,500,000.00</td>
                            </tr>

                            <!-- Expenses Section -->
                            <tr class="table-danger">
                                <td colspan="2"><strong>Expenses</strong></td>
                            </tr>
                            <tr>
                                <td>Employee Salaries</td>
                                <td class="text-end">800,000.00</td>
                            </tr>
                            <tr>
                                <td>Rent & Utilities</td>
                                <td class="text-end">120,000.00</td>
                            </tr>
                            <tr>
                                <td>Office Supplies & Maintenance</td>
                                <td class="text-end">50,000.00</td>
                            </tr>
                            <tr>
                                <td>Internet & Communication</td>
                                <td class="text-end">30,000.00</td>
                            </tr>
                            <tr>
                                <td>Software & Licenses</td>
                                <td class="text-end">75,000.00</td>
                            </tr>
                            <tr>
                                <td>Marketing & Advertising</td>
                                <td class="text-end">60,000.00</td>
                            </tr>
                            <tr>
                                <td>Travel & Client Meetings</td>
                                <td class="text-end">40,000.00</td>
                            </tr>
                            <tr>
                                <td>Professional Fees (Legal, Audit, etc.)</td>
                                <td class="text-end">25,000.00</td>
                            </tr>
                            <tr>
                                <td>Insurance</td>
                                <td class="text-end">20,000.00</td>
                            </tr>
                            <tr>
                                <td>Depreciation & Amortization</td>
                                <td class="text-end">35,000.00</td>
                            </tr>
                            <tr>
                                <td>Miscellaneous Expenses</td>
                                <td class="text-end">20,000.00</td>
                            </tr>

                            <!-- Net Profit Calculation -->
                            <tr class="table-success">
                                <td><strong>Net Profit</strong></td>
                                <td class="text-end"><strong>
                                    {{ number_format(1500000 - (800000 + 120000 + 50000 + 30000 + 75000 + 60000 + 40000 + 25000 + 20000 + 35000 + 20000), 2) }}
                                </strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    // Any custom JS can go here
</script>
@endsection
