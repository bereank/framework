<?php

namespace Leysco100\Gpm\Reports;

use Carbon\Carbon;
use Leysco\LS100SharedPackage\Models\Domains\Marketing\Models\GMS1;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ScanLogReport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public $date;

    public function __construct($date)
    {
        $this->date = $date;
    }
    public function collection()
    {
        $scan_log_report = GMS1::whereDate('created_at', $this->date)->with('objecttype', 'creator', 'gates')->orderBy('GateID')->get();

        //  check scan status
        foreach ($scan_log_report as $key => $value) {
            if ($value->Status == 0) {
                $value->ResultDesc = "Successfull";
                $value->ReleaseDesc = "Released";
            }
            if ($value->Status == 1) {
                $value->ResultDesc = "Does not Exist";
                $value->ReleaseDesc = "Not Released";
            }
            if ($value->Status == 2) {
                $value->ResultDesc = "Duplicate";
                $value->ReleaseDesc = "Not Released";
            }
            if ($value->Status == 3) {
                $value->ResultDesc = "Flagged";
                $value->ReleaseDesc = "Not Released";
            }

            if ($value->Released == 0) {
                $value->ReleaseDesc = "Not Released";
            }
            if ($value->Released == 1) {
                $value->ReleaseDesc = "Released";
            }
        }
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
            'Release Status'
        ];
    }
    public function map($scan_log): array
    {


        return [
            $this->date,
            $scan_log->gates->Name ?? "",
            $scan_log->creator->name ?? "",
            $scan_log->Phone ?? "N/A",
            $scan_log->objecttype->DocumentName,
            $scan_log->DocNum,
            $scan_log->ResultDesc,
            $scan_log->created_at,
            $scan_log->ReleaseDesc ?? ""
        ];
    }
}
