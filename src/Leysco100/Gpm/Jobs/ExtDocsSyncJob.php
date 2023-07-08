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
use Leysco100\Gpm\Services\NotificationsService;
use Leysco100\Shared\Models\Marketing\Models\OGMS;



class ExtDocsSyncJob  implements ShouldQueue,TenantAware
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $newRecords;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($newRecords)
    {

        $this->newRecords = $newRecords;
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
            $data = $this->newRecords;
            foreach ($data as $key => $value) {

                if (!$value['LineDetails']) {
            
                    continue;
                }

                $data =  OGMS::firstOrCreate(
                    [
                        'ObjType' => $value['ObjType'],
                        'ExtRef' => $value['ExtRef'],
                        'BaseEntry' => $value['BaseEntry'],
                        'BaseType' => $value['BaseType'],
                    ],
                    [
                        'ExtRefDocNum' => $value['ExtRefDocNum'],
                        'DocDate' => $value['DocDate'],
                        'GenerationDateTime' => Carbon::parse($value['GenerationDateTime'])->format('Y-m-d H:i:s'),
                        'DocTotal' => $value['DocTotal'],
                        'LineDetails' => $value['LineDetails'],
                    ]
                );
    
            }
            DB::connection('tenant')->commit();
        } catch (\Throwable $th) {
            DB::connection('tenant')->rollback();
            $subject = 'EXT Syncing Failure';
            (new NotificationsService())->sendFailureNotification($th, $subject);
            Log::info($th);
        }
    }
}
