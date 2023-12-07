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
use Leysco100\Shared\Models\Gpm\Models\OGMS;



class ExtDocsSyncJob  implements ShouldQueue, TenantAware
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

            $dataArray = $this->newRecords;
            foreach ($dataArray as $key => $value) {

                if (!$value['LineDetails']) {

                    continue;
                }

                $data = OGMS::where([
                    'ObjType' => $value['ObjType'],
                    'ExtRef' => $value['ExtRef'],
                    'BaseEntry' => $value['BaseEntry'],
                    'BaseType' => $value['BaseType'],
                ])->first();

                if ($data) {
                    // Update the existing record
                    $lineOld = explode('|', $data->LineDetails);
                    $lineNew = explode('|', $value['LineDetails']);

                    $difference = array_diff($lineNew, $lineOld);

                    if (!empty($difference)) {
                        $res = collect($difference)->values()->join('|');

                        $result = $data->LineDetails . '|' . $res;

                        $data->update([
                            'DocDate' => $value['DocDate'],
                            'LineDetails' => $result,
                        ]);
                    }

                    // rest of the existing logic for updating
                } else {
                    // Create a new record
                    $newRecord = OGMS::create([
                        'ObjType' => $value['ObjType'],
                        'ExtRef' => $value['ExtRef'],
                        'BaseEntry' => $value['BaseEntry'],
                        'BaseType' => $value['BaseType'],
                        'ExtRefDocNum' => $value['ExtRefDocNum'],
                        'DocDate' => $value['DocDate'],
                        'GenerationDateTime' => Carbon::parse($value['GenerationDateTime'])->format('Y-m-d H:i:s'),
                        'DocTotal' => $value['DocTotal'],
                        'LineDetails' => $value['LineDetails'],
                        'OwnerCode' => $value['OwnerCode'] ?? null,
                        'BPLId' => $value['BPLId'] ?? null,
                    ]);

                    Log::info('A new record was created ' . $newRecord->ObjType);

                    if ($newRecord->ObjType == "DISPNOT" || $newRecord->ObjType == "DS") {

                        if ($newRecord->BaseType && $newRecord->BaseEntry) {

                            $base =  OGMS::where('ObjType', $newRecord->BaseType)->where('ExtRef', $newRecord->BaseEntry)->first();
                        }
                        if ($base) {
                            $newRecord->update([
                                'BPLId' => $base->BPLId,
                            ]);
                        }
                    }
                }
            }
            DB::connection('tenant')->commit();
        } catch (\Throwable $th) {
            DB::connection('tenant')->rollback();
            Log::info($th->getMessage());
        }
    }
}
