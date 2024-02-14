<?php

namespace Leysco100\Shared\Console\Setup\Payments;

use Illuminate\Console\Command;
use Leysco100\Shared\Models\Payments\Models\OTPP;
use Leysco100\Shared\Models\Payments\Models\TPP1;
use Leysco100\Shared\Models\Payments\Models\TPP2;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;

class CreateThirdPartyPayments extends Command
{
    use TenantAware;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leysco100:shared:initialize-payments-json {--tenant=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import payments config data';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        OTPP::query()->truncate();
        TPP1::query()->truncate();
        TPP2::query()->truncate();
        $data = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Payments.json'), true);

        foreach ($data as $item) {

            $otppData = $item['OTPP'];

            $otpp = OTPP::create([
                'Name' => $otppData['Name'] ?? null,
                'Description' => $otppData['Description'] ?? null,
                'Code' => $otppData['Code'] ?? null,
                'ObjCode' => $otppData['ObjCode'] ?? null,
                'Active' => $otppData['Active'] ?? null,
            ]);

            foreach ($otppData['children'] as $childData) {
                $child = TPP1::create([
                    'Name' => $childData['Name'] ?? null,
                    'Code' => $childData['Code'] ?? null,
                    'AuthMth' => $childData['AuthMth'] ?? null,
                    'HasStkPush' => $childData['HasStkPush'] ?? null,
                    'CallBackUrl' => $childData['CallBackUrl'] ?? null,
                    'ValidationUrl' => $childData['ValidationUrl'] ?? null,
                    'ConfirmUrl' => $childData['ConfirmUrl'] ?? null,
                    'Active' => $childData['Active'] ?? null,
                    'PassKey' => $childData['PassKey'] ?? null,
                    'PublicKeyPath' => $childData['PublicKeyPath'] ?? null,
                ]);

                if (isset($childData['TPP2'])) {
                    TPP2::create([
                        'child_id' => $child->id,
                        'Code' => $childData['TPP2']['Code'] ?? null,
                        'MobileActive' => $childData['TPP2']['MobileActive'] ?? null,
                        'Status' => $childData['TPP2']['Status'] ?? null,
                        'Shortcode' => $childData['TPP2']['Shortcode'] ?? null,
                        'UserName' => $childData['TPP2']['UserName'] ?? null,
                        'Password' => $childData['TPP2']['Password'] ?? null,
                    ]);
                }
            }
        }

        $this->info('Data imported successfully.');
    }
}
