<?php

namespace Leysco100\Gpm\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Multitenancy\Jobs\TenantAware;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Leysco100\Gpm\Services\DocumentsService;
use Leysco100\Gpm\Services\NotificationsService;
use Leysco100\Shared\Models\Marketing\Models\OGMS;
use Leysco100\Shared\Models\Marketing\Models\BackUpModeEntries;




class GPMDocsSyncronizationJob  implements ShouldQueue,TenantAware
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::connection('tenant')->beginTransaction();
        try {
            $sycedLater = BackUpModeEntries::where('SyncStatus', 0)->select('DocNum', 'ReleaseStatus', 'ObjType')->get();
            foreach ($sycedLater as  $value) {
                // Check if the record exists
                $recordExists = OGMS::where('ObjType', $value->ObjType)->where('ExtRefDocNum', $value->DocNum)->exists();
                if ($recordExists) {
                    BackUpModeEntries::where('DocNum', $value->DocNum)
                        ->where('SyncStatus', 0)
                        ->update([
                            'SyncStatus' => 1,
                            'DocDate' => Carbon::now()
                        ]);
                    if ($value->ReleaseStatus == 0) {
                        OGMS::where('ExtRefDocNum', $value->DocNum)
                            ->where('ObjType', $value->ObjType)->update([
                                'Status' => 1,
                            ]);
                    }
                    if ($value->ReleaseStatus == 1) {
                        OGMS::where('ExtRefDocNum', $value->DocNum)->where('ObjType', $value->ObjType)->update([
                            'Status' => 2,
                        ]);
                        // Close base documents
                        $docToClose =  OGMS::where('ExtRefDocNum', $value->DocNum)
                            ->where('ObjType', $value->ObjType)->first();
                        if ($docToClose->BaseType && $docToClose->BaseEntry) {
                            (new  DocumentsService())->closeOtherDocuments($docToClose->ObjType,  $docToClose->DocEntry);
                        }
                    }
                }
            }
            DB::connection('tenant')->commit();
        } catch (\Throwable $th) {
            DB::connection('tenant')->rollback();
            $subject = 'Check if doc Sync Failure';
            (new NotificationsService())->sendFailureNotification($th, $subject);
            Log::info($th);
        }
    }
}
