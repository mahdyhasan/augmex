<?php

namespace App\Imports;

use App\Models\DivanjCrmLead;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DivanjLeadImport implements ToModel, WithHeadingRow
{
    protected $agentId;

    public function __construct($agentId = null)
    {
        $this->agentId = $agentId ?? auth()->id();
    }

    public function model(array $row)
    {
        // Clean phone numbers
        $mobile = $this->cleanPhoneNumber($row['mobile'] ?? null);
        $landline = $this->cleanPhoneNumber($row['landline'] ?? null);

        return new DivanjCrmLead([
            'name'     => $row['name'],
            'email'    => $row['email'] ?? null,
            'mobile'   => $mobile,
            'landline' => $landline,
            'source'   => strtolower($row['source']) ?? 'other',
            'note'     => $row['notes'] ?? null,
            'agent_id' => $this->agentId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function cleanPhoneNumber($number)
    {
        if (empty($number)) return null;
        
        $number = preg_replace('/\D/', '', $number);
        
        // Remove country code (61) if present
        if (str_starts_with($number, '61')) {
            $number = substr($number, 2);
        }
        // Remove leading 0
        elseif (str_starts_with($number, '0')) {
            $number = substr($number, 1);
        }
        
        // Validate length
        if (preg_match('/^[1-9]\d{8,10}$/', $number)) {
            return $number;
        }
        
        return null;
    }
}