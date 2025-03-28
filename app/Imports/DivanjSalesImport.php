<?php

namespace App\Imports;

use App\Models\DivanjSale;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Carbon;

class DivanjSalesImport implements ToModel, WithHeadingRow
{
    protected $employeeId;

    public function __construct($employeeId)
    {
        $this->employeeId = $employeeId;
    }

    /**
     * @param array $row
     */
    public function model(array $row)
    {
        // Example columns (from heading row):
        // $row['date_added'], $row['time'], $row['name'], 
        // $row['quantity'], $row['price'], $row['total']

        // Convert date/time to MySQL-friendly formats (if needed)
        $date = null;
        if (!empty($row['date_added'])) {
            // Attempt to parse; fallback to null if invalid
            $date = Carbon::parse($row['date_added'])->format('Y-m-d');
        }

        $time = null;
        if (!empty($row['time'])) {
            $time = Carbon::parse($row['time'])->format('H:i:s');
        }

        // Grab other columns (use defaults if missing)
        $name     = $row['name']      ?? null;
        $quantity = $row['quantity']  ?? 0;
        $price    = $row['price']     ?? 0;
        $total    = $row['total']     ?? 0;

        // Upsert (update or create) logic:
        // "Unique" match: same employee, date, time, name
        return DivanjSale::updateOrCreate(
            [
                'employee_id' => $this->employeeId,
                'date'        => $date,
                'time'        => $time,
                'name'        => $name,
            ],
            [
                'quantity' => $quantity,
                'price'    => $price,
                'total'    => $total,
            ]
        );
    }
}
