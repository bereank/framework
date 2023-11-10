<?php

namespace Leysco100\Administration\Jobs;


use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Multitenancy\Jobs\TenantAware;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Leysco100\Administration\Jobs\SendAlertEmailJob;
use Leysco100\Shared\Models\Administration\Models\ALR2;
use Leysco100\Shared\Models\Administration\Models\ALR3;
use Leysco100\Shared\Models\Administration\Models\OALR;
use Leysco100\Shared\Models\Administration\Models\OALT;
use Leysco100\Administration\Services\AlertsManagerService;

class ProcessAlertsJob implements ShouldQueue, TenantAware
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $temp;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($temp)
    {
        $this->temp = $temp;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("_______________START____________________");
        Log::info('Template name:  ' . $this->temp->Name);
        $current_day = Carbon::today()->format('Y-m-d');
        $time =  Carbon::now()->format('H:i');

        $NextTime = Carbon::parse($this->temp->NextTime)->format('H:i');
        $NextDate =  Carbon::parse($this->temp->NextDate)->format('Y-m-d');

        $queryCount = 0;
        if ($NextDate == $current_day && $this->temp->Active) {
            Log::info('=============QUERY:' . $queryCount . '=========');
            if ($NextTime == $time) {
                Log::info("_________________READY__________________");
                $data = (new AlertsManagerService())->processPeriod(
                    $this->temp->FrqncyType,
                    $this->temp->FrqncyIntr,
                    $this->temp->ExecTime,
                    $this->temp->ExecDaY
                );
              
                OALT::where('id', $this->temp->id)->update([
                    'ExecDaY' =>  $data['ExecDay'],
                    'ExecTime' =>  $data['ExecTime'],
                    'NextDate' =>  $data['NextDate'],
                    'NextTime' =>  $data['NextTime'],
                ]);

                foreach ($this->temp->alt4 as $query) {
                    if (!isset($query->saved_query)) {
                        return;
                    } else {
                        Log::info('NO QUERY ISSET');
                    }
                    $result = DB::connection('tenant')->select($query->saved_query->QString);
                    Log::info('-----------------' . $query->saved_query->QName . '----------');
                    if (count($result) > 0) {

                        $headers = [];
                        if (is_array($result)) {
                            if (!empty($result)) {
                                $headers = array_keys((array)$result[0]);
                            }

                            $formattedResult = [];
                            foreach ($result as $row) {
                                $formattedResult[] = (array)$row;
                            }

                            $results = [
                                'headers' => $headers,
                                'data' => $formattedResult
                            ];
                        }

                        $alert =   $this->createAlert($results, $query);
                        dispatch(new SendAlertEmailJob($alert->id));
                    } else {
                        Log::info('NO QUERY RESULT');
                        return;
                    }
                }
            } else {
                Log::info("_______________ALERT NOT READY____________________");
            }
            $queryCount++;
        } else {
            Log::info("_______________ALERT NOT EXECUTING TODAY____________________");
        }
        Log::info("_______________END____________________");
    }
    public function createAlert($results, $query)
    {

        $alert = OALR::create([
            "Code" => $this->temp->Code,
            "TCode" => $this->temp->id,
            "Priority" => $this->temp->Priority,
            "UserText" =>  $this->temp->UserText
        ]);

        foreach ($results['headers'] as $key => $header) {
            ALR2::create(
                [
                    "Code" => $alert->id,
                    "Location" => $key,
                    "ColName" => $header,
                    "QueryId" => $query->saved_query->id,
                    "QName" => $query->saved_query->QName,
                ]
            );
        }


        foreach ($results['data'] as $key => $data) {
            $i = 0;
            foreach ($data as  $val) {
                ALR3::create(
                    [
                        "Code" => $alert->id,
                        "Location" => $i++,
                        "Line" => $key,
                        "Value" => $val,
                        "QueryId" => $query->saved_query->id,
                        "QName" => $query->saved_query->QName,
                    ]
                );
            }
        }
        return $alert;
    }
}
