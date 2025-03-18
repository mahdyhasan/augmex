@extends('layouts.app')

@section('title', 'Generate Invoice')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3>Generate Invoice</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('invoices.generate.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="client_id" class="form-label">Client</label>
                        <select class="form-control" id="client_id" name="client_id" required>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->company }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>

                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Generate Invoice</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
