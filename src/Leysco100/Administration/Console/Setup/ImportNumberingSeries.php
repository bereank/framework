<?php

namespace Leysco100\Administration\Console\Setup;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Leysco100\Shared\Models\Administration\Models\NNM1;
use Leysco100\Shared\Models\Administration\Models\ONNM;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;

class ImportNumberingSeries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    use TenantAware;

    protected $signature = 'leysco100:administration:import-numbering-series {--tenant=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Numbering Series';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $documents = [17, 15, 16, 13, 14, 23, 24, 1470000113, 1250000001, 191, 66, 67];

//        $userSeriesName = $this->ask('Enter Series Name:');
        $seriesJsonString = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'numberingseries.json');;
        $series = json_decode($seriesJsonString, true);
        foreach ($series as $key => $value) {
             DB::connection("tenant")->beginTransaction();
            try {
                if (!isset($value['SeriesName'])) {
                    continue;
                }

                if (!isset($value['Document'])) {
                    continue;
                }

                $document = $value['Document'];
                $SeriesName = $value['SeriesName'];
                if (!in_array($document, $documents)) {
                    continue;
                }
//                if ($userSeriesName != $SeriesName) {
//                    continue;
//                }

                $details = $this->getObjectDetails($document);
                if (!$details) {
                    continue;
                }

                $this->info("Creating Series" . $value['SeriesName']);

                $nnm1 = NNM1::updateOrCreate([
                    'ExtRef' => $value['Series'],
                ], [
                    'SeriesName' => $SeriesName, //Series Name
                    'ObjectCode' => $details->id, // ID FROM ONNM
                    'InitialNum' => $value['InitialNum'], //Initial Number
                    'NextNumber' => $value['InitialNum'], // NextNumber
                    'LastNum' => $value['LastNum'], //LastNum
                    'BeginStr' => $value['BeginStr'],
                    'Remark' => $value['Remarks'],
                    'Locked' => $value['Locked'] ?? "N", //Locked
                    'IsForCncl' => $value['Is Series for Cancelation'] ?? "N",
                    'GroupCode' => $value['Group'] ?? 1,
                ]);

//                $this->comment("CREATED SERIES ID: " . $nnm1->id);
                 DB::connection("tenant")->commit();
            } catch (\Throwable $th) {
                Log::info($th);
                 DB::connection("tenant")->rollback();
            }
        }
    }

    public function getObjectDetails($ObjType)
    {
        if ($ObjType == 1470000113) {
            $ObjType = 205;
        }

        if ($ObjType == 1250000001) {
            $ObjType = 66;
        }
        $apdi = APDI::where('ObjectID', $ObjType)->first();
        $onnm = ONNM::where('ObjectCode', $apdi->id)->first();
        return $onnm;
    }
}
