<?php

namespace App\Imports;

use App\Models\DivanjSale;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;

class DivanjSalesImport implements ToModel
{
    protected $employeesByName;
    protected $unmatchedConsultants = [];
    protected $onRowCallback;

    public function __construct()
    {
        // Preload employees indexed by stage name
        $this->employeesByName = Employee::pluck('id', 'stage_name')->toArray();
    }

    public function model(array $row)
    {
        // Try to support both keyed rows and index-based rows
        $consultant = $row['consultant'] ?? $row[0] ?? null;
        $dateField  = $row['date_added'] ?? $row[1] ?? null;
        $timeField  = $row['time'] ?? $row[2] ?? '00:00:00';
        $productCode = $row['product_code'] ?? $row[3] ?? null;
        $name       = $row['name'] ?? $row[4] ?? null;
        $quantity   = $row['quantity'] ?? $row[5] ?? 0;
        $price      = $row['price'] ?? $row[6] ?? 0;
        $total      = $row['total'] ?? $row[7] ?? 0;

        if (empty($consultant) || empty($dateField) || empty($name)) {
            Log::warning('Skipping row - missing required fields', $row);
            return null;
        }

        try {
            $consultantName = trim($consultant);
            $date = Carbon::parse($dateField)
                ->timezone(config('app.timezone'))
                ->format('Y-m-d');
            $time = Carbon::parse($timeField)->format('H:i:s');
            $name = trim($name);
            $productCode = $productCode;
            $quantity = (float)$quantity;
            $price = (float)$price;
            $total = (float)$total;

            // Determine employee by matching the first word (case-insensitive)
            $employeeId = null;
            $firstWord = strtolower(explode(' ', $consultantName)[0]);
            foreach ($this->employeesByName as $stage_name => $id) {
                if (str_starts_with(strtolower($stage_name), $firstWord)) {
                    $employeeId = $id;
                    break;
                }
            }

            if (!$employeeId) {
                if (!in_array($consultantName, $this->unmatchedConsultants)) {
                    $this->unmatchedConsultants[] = $consultantName;
                    Log::warning("Unmatched consultant: {$consultantName}");
                }
                return null;
            }

            // Check for an existing identical record
            $existing = DivanjSale::where('employee_id', $employeeId)
                ->where('date', $date)
                ->where('time', $time)
                ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                ->first();

            if ($existing &&
                (float)$existing->quantity === $quantity &&
                (float)$existing->price === $price &&
                (float)$existing->total === $total) {
                Log::debug('Skipping existing identical record', [
                    'id' => $existing->id,
                    'row' => $row
                ]);
                return null;
            }

            Log::debug('Importing record', [
                'employee_id' => $employeeId,
                'date' => $date,
                'time' => $time,
                'product' => $name
            ]);

            if ($this->onRowCallback) {
                call_user_func($this->onRowCallback, $row);
            }

            return new DivanjSale([
                'employee_id'  => $employeeId,
                'date'         => $date,
                'time'         => $time,
                'name'         => $name,
                'product_code' => $productCode,
                'quantity'     => $quantity,
                'price'        => $price,
                'total'        => $total,
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing import row', [
                'row'   => $row,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function onRow(callable $callback)
    {
        $this->onRowCallback = $callback;
    }

    public function getUnmatchedConsultants(): array
    {
        return $this->unmatchedConsultants;
    }
}
