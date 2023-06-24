<?php

namespace Leysco100\Gpm\Reports;

use Carbon\Carbon;
use Leysco\LS100SharedPackage\Models\Domains\Marketing\Models\GMS1;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Leysco\LS100SharedPackage\Services\ApiResponseService;

class DublicateScanLogs implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public $date;
    public function __construct($date)
    {
        $this->date = $date;
    }
    public function collection()
    {
        try {


            $scan_log = GMS1::whereDate('created_at', $this->date)->where('Status', 2)->pluck('DocNum');

            $duplicate = GMS1::whereIn('DocNum', $scan_log)
                ->with('objecttype', 'creator', 'gates')
                ->orderBy('DocNum')
                ->get();

            $duplicate = $duplicate->map(function ($item, $key) {
                if ($item->Status === 0) {
                    $item->ResultDesc = "Successfull";
                } else if ($item->Status === 1) {
                    $item->ResultDesc = "Does not Exist";
                } else if ($item->Status === 2) {
                    $item->ResultDesc = "Duplicate";
                } else if ($item->Status === 3) {
                    $item->ResultDesc = "Flagged";
                }

                if ($item->Released === 0) {
                    $item->ReleaseDesc = "Not Released";
                } else if ($item->Released === 1) {
                    $item->ReleaseDesc = "Released";
                }

                return $item;
            });


            return $duplicate;
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
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
            'Comment'
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
            $scan_log->Comment ?? "N/A"
        ];
    }
}
