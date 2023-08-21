<?php

namespace Leysco100\Gpm\Reports;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\MarketingDocuments\Models\BackUpModeLines;
use Leysco100\Shared\Models\MarketingDocuments\Models\BackUpModeSetup;

class BCPScanReport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{

    public function __construct()
    {
    }
    public function collection()
    {
        try {

            $setup = BackUpModeSetup::latest()->first();

            $totalSynced = BackUpModeLines::where('DocEntry',  $setup->id)->get();

            foreach ($totalSynced as $key => $value) {

                if ($value->ReleaseStatus == 0) {
                    $value->ReleaseStatus = "Pending release";
                }
                if ($value->ReleaseStatus == 2) {
                    $value->ReleaseStatus = "Flagged";
                }
                if ($value->ReleaseStatus == 1) {
                    $value->ReleaseStatus = "Released";
                }
                if ($value->SyncStatus == 0) {
                    $value->SyncStatus = "Not synced";
                }
                if ($value->SyncStatus == 1) {
                    $value->SyncStatus = "Synced";
                }
            }

            return  $totalSynced;
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function headings(): array
    {

        return [
            'Date',
            'Document Number',
            'DocOrigin',
            'Sync Status',
            'ReleaseStatus',
            'Scan Time',


        ];
    }
    public function map($scan_log): array
    {
        return [
            now(),
            $scan_log->DocNum ?? "",
            $scan_log->DocOrigin ?? "",
            $scan_log->SyncStatus ?? "",
            $scan_log->ReleaseStatus ?? "",
            $scan_log->DocDate,
        ];
    }
}
