<?php

namespace Leysco100\MarketingDocuments\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Multitenancy\Jobs\TenantAware;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Leysco100\Shared\Models\Shared\Models\APDI;

class OpenQtyUpdateJob implements ShouldQueue, TenantAware
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 360;

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public $failOnTimeout = true;

    public $BaseType;
    public  $Quantity;
    public $BaseEntry;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($BaseType, $Quantity, $BaseEntry)
    {
        $this->BaseType = $BaseType;
        $this->Quantity = $Quantity;
        $this->BaseEntry = $BaseEntry;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $BaseTable = APDI::with('pdi1')
            ->where('ObjectID', $this->BaseType)
            ->first();

        $rowData =  $BaseTable->pdi1[0]['ChildTable']::where('id', $this->BaseEntry)->firstOrfail();

        $details = [
            'OpenQty' =>  $rowData->OpenQty - $this->Quantity
        ];
        $BaseTable->pdi1[0]['ChildTable']::where('id', $this->BaseEntry)->update($details);
        return $rowData;
    }
}
