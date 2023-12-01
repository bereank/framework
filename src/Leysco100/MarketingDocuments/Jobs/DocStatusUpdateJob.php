<?php

namespace Leysco100\MarketingDocuments\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Multitenancy\Jobs\TenantAware;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DocStatusUpdateJob implements ShouldQueue, TenantAware
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

    public $BaseTables;
    public $id;
    public $baseDocHeader;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($BaseTables, $id, $baseDocHeader)
    {
        $this->BaseTables = $BaseTables;
        $this->id = $id;
        $this->baseDocHeader = $baseDocHeader;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->BaseTables->pdi1[0]['ChildTable']::where('DocEntry', $this->id)
            ->where('OpenQty', 0)->where('LineStatus', "O")->update([
                'LineStatus' => "C",
            ]);

        if ($this->BaseTables->pdi1[0]['ChildTable']::where('DocEntry', $this->id)
            ->where('OpenQty', '>=', 1)
            ->doesntExist()
        ) {

            $this->baseDocHeader->update([
                'DocStatus' => "C",
            ]);

            $this->BaseTables->pdi1[0]['ChildTable']::where('DocEntry', $this->id)->update([
                'LineStatus' => "C",
            ]);
        }
    }
}
