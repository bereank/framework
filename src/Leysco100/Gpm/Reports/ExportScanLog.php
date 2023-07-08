<?php

namespace Leysco100\Gpm\Reports;

use Carbon\Carbon;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Leysco100\Shared\Models\Marketing\Models\GMS1;

class ExportScanLog implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {

        $scan_log_report = GMS1::with('objecttype', 'creator', 'gates')->get();

        //  check scan status
        foreach ($scan_log_report as $key => $value) {

            if ($value->Status == 0) {
                $value->ResultDesc = "Successfull";
            }
            if ($value->Status == 1) {
                $value->ResultDesc = "Does not Exist";
            }
            if ($value->Status == 2) {
                $value->ResultDesc = "Duplicate";
            }
            if ($value->Status == 3) {
                $value->ResultDesc = "Flagged";
            }
            if ($value->Status == 4) {
                $value->ResultDesc = "Released";
            }
        }
        return $scan_log_report;
    }
    public function headings(): array
    {
        return [
            'Gate',
            'User',
            'Phone Number',
            'Document Type',
            'Document Number',
            'Scan State',
            'Scan Time'
        ];
    }
    public function map($scan_log): array
    {
        $date =  Carbon::now()->subDays(1)->format('Y-m-d');

        return [
            $scan_log->gates->Name,
            $scan_log->creator->name ?? "",
            $scan_log->Phone ?? "N/A",
            $scan_log->objecttype->DocumentName,
            $scan_log->DocNum,
            $scan_log->ResultDesc,
            $scan_log->created_at,
        ];

    }
}
