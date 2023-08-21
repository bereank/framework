<?php

namespace Leysco100\Gpm\Reports;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\MarketingDocuments\Models\GMS1;
use Leysco100\Shared\Models\MarketingDocuments\Models\OGMS;

class DoesNotExist implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public $date;
    public function __construct($date)
    {
        $this->date = $date;
    }
    public function collection()
    {
        try {


            $scan_log = GMS1::whereDate('created_at', $this->date)->where('Status', 1)->select('DocNum')->get();

            $duplicate = GMS1::whereIn('DocNum', $scan_log->pluck('DocNum'))
                ->with('objecttype', 'creator', 'gates')
                ->orderBy('DocNum')
                ->get();
            foreach ($duplicate as $key => $value) {

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


                $lastPart = Str::afterLast($value->DocNum, '-');

                $item = OGMS::where('ExtRefDocNum', $lastPart)->first();

                if ($item) {
                    $value->ResultExist = "Synced Later";
                    $value->GenerationTime = $item->created_at;
                } else {
                    // the item does not exist
                    $value->ResultExist = "Not Synced";
                }
            }

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
            'Sync Status',
            'Sync Time'
        ];
    }
    public function map($scan_log): array
    {
        return [
            $this->date,
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
