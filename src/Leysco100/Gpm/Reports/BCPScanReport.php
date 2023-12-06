<?php

namespace Leysco100\Gpm\Reports;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Gpm\Models\BackUpModeLines;
use Leysco100\Shared\Models\Gpm\Models\BackUpModeSetup;

class BCPScanReport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{

    public function __construct()
    {
    }
    public function collection()
    {
        try {

            $setup = BackUpModeSetup::latest()->first();

     $totalSynced=BackUpModeLines::with(['objecttype' => function ($query) {
                $query->select(["id", 'DocumentName', 'ObjectID']);
            }])
                ->with(['creator' => function ($query) {
                    $query->select(["id", 'name', 'account', 'email', 'phone_number']);
                }])
                ->with(['gates' => function ($query) {
                    $query->select(["id", 'Name', 'Longitude', 'Latitude', 'Address']);
                }])
                ->with('ordr')
                ->where('DocEntry',  $setup->id)
                ->latest()
                ->get();
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
Log::info([$totalSynced]);
            return  $totalSynced;
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function headings(): array
    {

        return [
            'Date',
            'Gate',
            'Creator',
            'Document Number',
            'Document Type',
            'Sync Status',
            'ReleaseStatus',
            'Scan Time',
            'Sync Time'
        ];
    }
    public function map($scan_log): array
    {
        return [
            now(),
            $scan_log->gates?->Name ?? 'n/a',
            $scan_log->creator?->name ?? 'n/a',
            $scan_log->DocNum ?? "",
            $scan_log->objecttype?->DocumentName?? "",
            $scan_log->SyncStatus ?? "",
            $scan_log->ReleaseStatus ?? "",
            $scan_log->created_at ?? "",
            $scan_log->DocDate,
        ];
    }
}