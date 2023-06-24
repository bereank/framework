<?php

namespace Leysco\GatePassManagementModule\Reports\LongReports;



use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Leysco\GatePassManagementModule\Services\ReportsService;
use Leysco\LS100SharedPackage\Models\Domains\Marketing\Models\GMS1;

class LongDoesNotExist implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public $from_date;
    public $to_date;

    public function __construct($from_date, $to_date)
    {
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }
    public function collection()
    {
        $scan_log_report = (new ReportsService())->doesNotExistReport($this->from_date, $this->to_date);
        return $scan_log_report;
    }
    public function headings(): array
    {
        return [
            'Date',
            'Gate',
            'User',
            'Phone Number',
            'Document Type',
            'Document Number',
            'Scan State',
            'Scan Time',
            'Sync Status',
            'Sync Time'
        ];
    }
    public function map($scan_log): array
    {
        return [
            $scan_log->created_at,
            $scan_log->gates->Name ?? "",
            $scan_log->creator->name ?? "",
            $scan_log->Phone ?? "N/A",
            $scan_log->objecttype->DocumentName ?? "",
            $scan_log->DocNum,
            $scan_log->ResultDesc ?? "",
            $scan_log->created_at,
            $scan_log->ResultExist,
            $scan_log->GenerationTime ?? "N/A"
        ];
    }
}
