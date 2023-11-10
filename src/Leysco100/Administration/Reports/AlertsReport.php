<?php

namespace Leysco100\Administration\Reports;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AlertsReport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
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
        return $rows_data;
    }
}
