<?php

namespace Leysco100\Gpm\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Multitenancy\Jobs\TenantAware;
use Leysco100\Gpm\Jobs\BCPNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Leysco100\Gpm\Services\DocumentsService;
use Leysco100\Gpm\Services\NotificationsService;
use Leysco100\Shared\Models\Administration\Models\OADM;
use Leysco100\Shared\Models\MarketingDocuments\Models\GMS1;
use Leysco100\Shared\Models\MarketingDocuments\Models\OGMS;
use Leysco100\Shared\Models\MarketingDocuments\Models\BackUpModeLines;
use Leysco100\Shared\Models\MarketingDocuments\Models\BackUpModeSetup;
use Leysco100\Shared\Models\MarketingDocuments\Models\AutoBCModeSettings;




class GPMDocsSyncronizationJob  implements ShouldQueue, TenantAware
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
        Log::info("Check gpm doc last sync  job");

        $this->autoTurnOnBcp();
        $this->deactivateBackupProcess();

        DB::connection('tenant')->beginTransaction();
        try {
            $bcpDocs = BackUpModeLines::where('SyncStatus', 0)
            ->select('DocNum', 'ReleaseStatus', 'ObjType', 'id')->get();
         if(!$bcpDocs){
            return;
         }
            foreach ($bcpDocs as  $value) {
                // Check if the record exists
                $recordExists = OGMS::where('ObjType', $value->ObjType)->where('ExtRefDocNum', $value->DocNum)->exists();
                if ($recordExists) {
                    
                    BackUpModeLines::where('DocNum', $value->DocNum)
                        ->where('ObjType', $value->ObjType)
                        ->where('SyncStatus', 0)
                        ->update([
                            'SyncStatus' => 1,
                            'DocDate' => Carbon::now()
                        ]);
                    GMS1::where('id', $value->id)->update([
                        'Released' => 1,
                    ]);
                    if ($value->ReleaseStatus == 0) {
                        OGMS::where('ExtRefDocNum', $value->DocNum)
                            ->where('ObjType', $value->ObjType)->update([
                                'Status' => 1,
                            ]);
                    }
                    if ($value->ReleaseStatus == 1) {
                        OGMS::where('ExtRefDocNum', $value->DocNum)->where('ObjType', $value->ObjType)->update([
                            'Status' => 3,
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
            // $recipient = OADM::where('id', 2)->value("NotifEmail");
            
            // $message= 'An error occurred during the execution of Check if doc Synced Command:
            //      ' . $th->getMessage();
            // (new NotificationsService())->sendNotification($recipient,$message);
           
        }
    }
    public function deactivateBackupProcess()
    {
        $affectedRows =   BackUpModeSetup::where('activatable_type', 2)
            ->where('Enabled', 1)->where('EndTime', '<', Carbon::now())->update([
                'Enabled' => 0,
            ]);

        if ($affectedRows > 0) {
            Log::info("manual activation end");
            $emailString = OADM::where('id', 1)->value("NotifEmail");
            $emails = explode(';', $emailString);
            $id = BackUpModeSetup::where('activatable_type', 2)
                ->where('EndTime', '<', Carbon::now())
                ->select('id')->first();
            $this->sendReport($id);
        }

        $Duration = '';
        $ogms = OGMS::latest()->first();
        $settings = AutoBCModeSettings::where('Status', 1)->first();

        if(!$settings){
            return;
        }

        $timeDiff =  $ogms->created_at->diffInMinutes(now());

        if ($settings->LastSyncDurationType == 'hours') {
            $Duration = $settings->LastSyncDuration * 60;
        } else {
            $Duration = $settings->LastSyncDuration;
        }

        if ($timeDiff < $Duration) {
            Log::info("Auto activation end");

            $active = BackUpModeSetup::where('activatable_type', 1)->latest()
                ->where('Enabled', 1)->first();
                    if(!$active){
                        return;
                    }
            $StrtTime = Carbon::parse($active->StartDate . ' ' . $active->StartTime);

            $minutes =  $StrtTime->diffInMinutes(Carbon::now());

            $affectedRows = BackUpModeSetup::where('activatable_type', 1)
                ->latest()->where('Enabled', 1)->update([
                    'EndTime' =>  Carbon::now()->format('Y-m-d H:i:s'),
                    'Enabled' => 0,
                    'Hours' => floor($minutes / 60),
                    'Minutes' => $minutes % 60,
                ]);

            if ($affectedRows > 0) {
                // The update was successful
                $updatedModel  = BackUpModeSetup::where('activatable_type', 1)
                    ->latest()
                    ->where('Enabled', 0)
                    ->first();
              
                if ($updatedModel) {
                    $updatedId =  $updatedModel->id;
                    $emailString = OADM::where('id', 1)->value("NotifEmail");
                    $recipient = explode(';', $emailString);
                    $message = "Back-up mode Deactivated\n GPM Documents started syncing";

                    (new NotificationsService())->sendNotification($recipient,$message);
                
                    $this->sendReport($updatedId);
                }
            }
        }
    }

    public function autoTurnOnBcp()
    {

        $currentDateTime = Carbon::now()->format('H:i');

        DB::connection('tenant')->beginTransaction();
        try {
            $Duration = '';
            $ogms = OGMS::latest()->first();
            
            $settings = AutoBCModeSettings::where('Status', 1)->where('ActiveFrom', '<=', $currentDateTime)
                ->where('ActiveTo', '>=', $currentDateTime)->first();
            if(!$settings){
                        return;
                     }
            Log::info("Auto activate bcp");
            $timeDiff =  $ogms->created_at->diffInMinutes(now());

            if ($settings->DurationType == 'hours') {
                $Duration = $settings->LastSyncDuration * 60;
            } else {
                $Duration = $settings->LastSyncDuration;
            }
            $logCountQuery = GMS1::where('created_at', '>=', now()->subMinutes($Duration))
                ->where('created_at', '<=', now())
                ->where('Status', 1);

            if ($settings->isDistinctDocs) {
                $logCountQuery->distinct('DocNum');
            }

            $logCount = $logCountQuery->count();


            $logCount = GMS1::whereBetween('created_at', [now()->subMinutes($Duration), now()])->where('Status', 1)->count();

            if ($timeDiff >= $Duration && ($logCount > $settings->DoesNotExistCount)) {
                if (BackUpModeSetup::where('Enabled', true)->doesntExist()) {

                    BackUpModeSetup::create([
                        'UserSign' => $settings->UserSign,
                        'ObjectType' => 215,
                        'Enabled' => 1,
                        'activatable_type' => 1,
                        'StartDate' => Carbon::now()->format('Y-m-d'),
                        'StartTime' =>  Carbon::now()->format('H:i:m'),
                        'Hours' => 00,
                        'Type' => 1,
                        'Minutes' => 00,
                        'OwnerID' => $settings->UserSign,
                        'FieldsTemplate' => $settings->FieldsTemplate,
                    ]);

                    $emailString = OADM::where('id', 1)->value("NotifEmail");
                    $recipient = explode(';', $emailString);
                    $message = 'Documents not syncing for over '
                     . $settings->LastSyncDuration . ' ' . $settings->DurationType . " now. \n" . 
                     $logCount . " Scan logs does-not-exist recorded.\n Backup mode Activated !!";
                  
                    (new NotificationsService())->sendNotification($recipient,$message);
                
                }
            }
            DB::connection('tenant')->commit();
        } catch (\Throwable $th) {
            DB::connection('tenant')->rollback();
            // $recipient = OADM::where('id', 2)->value("NotifEmail");
            // $message= 'An error occured Turning on backup mode:
            //      ' . $th->getMessage();
            // (new NotificationsService())->sendNotification($recipient,$message);
        }
    }
    public function sendReport($id)
    {
        $settings = AutoBCModeSettings::where('Status', 1)->first();
        if(!$settings){
            return;
        }
        $notifyAfter = 1;
        if ($settings->NotifyType == 'hours') {
            $notifyAfter  = $settings->NotifyAfter * 60;
        } else {
            $notifyAfter  = $settings->NotifyAfter;
        }
        Log::info("send report");
        BCPNotificationJob::dispatch($id)
            ->delay(now()->addMinutes($notifyAfter));
    }
}