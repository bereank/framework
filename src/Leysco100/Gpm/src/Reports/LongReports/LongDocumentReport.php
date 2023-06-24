<?php

namespace Leysco\GatePassManagementModule\Reports\LongReports;



use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Leysco\GatePassManagementModule\Services\ReportsService;
use Leysco\LS100SharedPackage\Models\Domains\Marketing\Models\GMS1;

class LongDocumentReport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
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
        $scan_log_report = (new ReportsService())->documentReport($this->from_date, $this->to_date);
        return $scan_log_report;
    }
    public function headings(): array
    {
        return [
            'Date',
            'Gate',
            'Document Type',
            'Document Number',
            'Generation Time',
            'Status',
            'Release Time',
        ];
    }
    public function map($scan_log): array
    {

        return [
            $scan_log->created_at,
            $scan_log->gate_name ?? "",
            $scan_log->DocumentName ?? "",
            $scan_log->document_number ?? "",
            $scan_log->gen_time ?? "",
            $scan_log->ReleaseDesc ?? "",
            $scan_log->updated_at ?? "N/A"

        ];
    }
}
