<?php

namespace Leysco100\Shared\Console\Setup;


use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\Shared\Models\ORCL;
use Leysco100\Shared\Models\Shared\Models\ORCP;
use Leysco100\Shared\Services\RecurrPeriodsService;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;
use Leysco100\Shared\Models\MarketingDocuments\Models\ODRF;

class RecurringTransactionCommand extends Command
{
    use TenantAware;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leysco100:shared:Process-recurring-transaction {--tenant=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Recurring Transactions';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $currentDay = Carbon::today()->format('Y-m-d');
        $timeRange1 = Carbon::now()->format('H:i');
        $timeRange2 = Carbon::now()->addMinutes(10)->format('H:i');

        $recurr_postings = ORCP::where('ExecDay', $currentDay)
            ->whereBetween('ExecTime', [$timeRange1, $timeRange2])
            ->get();


        foreach ($recurr_postings as $recurr_posting) {

            Log::info("RECURRING POSTINGS CREATE");
            $TargetTable = APDI::with('pdi1')->where('ObjectID', $recurr_posting->DocObjType)->first();

            $DraftTable = APDI::with('pdi1')->where('ObjectID', $TargetTable->DrftObj)->first();

            if ($TargetTable && $DraftTable) {
                $draft = $DraftTable->ObjectHeaderTable::where('id', $recurr_posting->DraftEntry)
                ->with('document_lines')->first();

                if ($draft) {
                    $destinationData = $draft->replicate()->toArray();


                    unset($destinationData['id']);
                    unset($destinationData['created_at']);
                    unset($destinationData['updated_at']);

                    $newDoc = new $TargetTable->ObjectHeaderTable($destinationData);

                    $newDoc->save();

                    foreach ($draft->document_lines as $document_line) {
                        $childTable = $TargetTable->pdi1[0]['ChildTable'];
                        $rowItems = new $childTable($document_line->toArray());
                        $rowItems['DocEntry'] =  $newDoc->id;
                        unset($rowItems['id']);
                        unset($rowItems['created_at']);
                        unset($rowItems['updated_at']);

                        $rowItems->save();
                    }
                } else {
                    Log::info("draft is not found");
                }
            } else {
                Log::info("target table is not found");
            }



            Log::info("Data replicated successfully!");

            $ExecutionTime = Carbon::now()->subMinutes(1)->format('H:i:s');
            $recurrPeriodsService = new RecurrPeriodsService();
            $NextExecution = $recurrPeriodsService
                ->processPeriod(
                    Frequency: $recurr_posting->Frequency,
                    Remind: $recurr_posting->Remind - 1000,
                    ExecTime: $ExecutionTime,
                    StartDate: $recurr_posting->StartDate,
                    EndDate: $recurr_posting->EndDate,
                );

            Log::info("Next Execution  " . $NextExecution);

            $data = ORCP::where('id', $recurr_posting->id)->update([
                'ExecDay' =>  $NextExecution
            ]);

            $newAgrrement = ORCL::create([
                'RcpEntry' => $recurr_posting->id,
                'PlanDate' => $recurr_posting->ExecDay . ' ' . $recurr_posting->ExecTime,
                'DocObjType' => $recurr_posting->DocObjType,
                'Instance' =>  1,
            ]);
        }
    }
}
