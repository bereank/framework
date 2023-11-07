<?php

namespace Leysco100\Gpm\Reports;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\MarketingDocuments\Models\BackUpModeSetup;

class AlertScanReport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function collection()
    {
        return collect($this->data['data']);
    }
    public function headings(): array
    {
        return $this->data['headers'];
    }
    public function map($rows_data): array
    {
        return $this->data['data'];
    }
}
