<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Table Id</th>
                <th>Consultant</th>
                <th>Employee</th>
                <th>Date</th>
                <th>Time</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
                @if($type == 'delete')
                    <th>Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr class="
                @if($type == 'new')
                    table-success
                @elseif($type == 'delete')
                    table-danger
                @else
                    table-secondary
                @endif
            ">
                <td>{{ $record['id'] ?? '-' }}</td>
                <td>{{ $record['consultant'] ?? ($record['employee']['stage_name'] ?? 'N/A') }}</td>
                <td>{{ $record['employee_name'] ?? ($record['employee']['stage_name'] ?? 'N/A') }}</td>
                <td>{{ \Carbon\Carbon::parse($record['date'] ?? $record['date'])
                    ->timezone(config('app.timezone'))
                    ->format('Y-m-d') }}
                </td>
                <td>{{ \Carbon\Carbon::parse($record['time'] ?? $record['time'])
                    ->timezone(config('app.timezone'))
                    ->format('H:i:s') }}
                </td>
                <td>{{ $record['name'] ?? $record['name'] }}</td>
                <td>{{ $record['quantity'] ?? $record['quantity'] }}</td>
                <td>{{ number_format($record['price'] ?? $record['price'], 2) }}</td>
                <td>{{ number_format($record['total'] ?? $record['total'], 2) }}</td>
                @if($type == 'delete')
                    <td>
                        <div class="form-check">
                            <input class="form-check-input record-to-delete" type="checkbox" 
                                   name="records_to_delete[]" value="{{ $record['id'] ?? $record['id'] }}" checked>
                        </div>
                    </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
