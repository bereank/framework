<?php

namespace Leysco100\Administration\Console;

use Illuminate\Console\Command;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OBFC;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;



class BinFieldActivationCommand extends Command
{

    use TenantAware;
    protected $signature = 'leysco100:administration:bin-location-fields {--tenant=*}';

    protected $description = 'Bin Location Field Activation';

    public function handle()
    {
        $data = [
            [
                'DispName' => 'Sublevel 1',
                'KeyName' => 'Warehouse Sublevel 1',
                'Activated' => 1,
                'FldType' => 'S'
            ],
            [
                'DispName' => 'Sublevel 2',
                'KeyName' => 'Warehouse Sublevel 2',
                'Activated' => 0,
                'FldType' => 'S'
            ],
            [
                'DispName' => 'Sublevel 3',
                'KeyName' => 'Warehouse Sublevel 3',
                'Activated' => 0,
                'FldType' => 'S'
            ],
            [
                'DispName' => 'Sublevel 4',
                'KeyName' => 'Warehouse Sublevel 4',
                'Activated' => 0,
                'FldType' => 'S'
            ],
            [
                'DispName' => 'Attribute 1',
                'KeyName' => 'Bin Location Attribute 1',
                'Activated' => 0,
                'FldType' => 'A'
            ],
            [
                'DispName' => 'Attribute 2',
                'KeyName' => 'Bin Location Attribute 2',
                'Activated' => 0,
                'FldType' => 'A'
            ],
            [
                'DispName' => 'Attribute 3',
                'KeyName' => 'Bin Location Attribute 3',
                'Activated' => 0,
                'FldType' => 'A'
            ],
            [
                'DispName' => 'Attribute 4',
                'KeyName' => 'Bin Location Attribute 4',
                'Activated' => 0,
                'FldType' => 'A'
            ],
            [
                'DispName' => 'Attribute 5',
                'KeyName' => 'Bin Location Attribute 5',
                'Activated' => 0,
                'FldType' => 'A'
            ],
            [
                'DispName' => 'Attribute 6',
                'KeyName' => 'Bin Location Attribute 6',
                'Activated' => 0,
                'FldType' => 'A'
            ],
            [
                'DispName' => 'Attribute 7',
                'KeyName' => 'Bin Location Attribute 7',
                'Activated' => 0,
                'FldType' => 'A'
            ],
            [
                'DispName' => 'Attribute 8',
                'KeyName' => 'Bin Location Attribute 8',
                'Activated' => 0,
                'FldType' => 'A'
            ],
            [
                'DispName' => 'Attribute 9',
                'KeyName' => 'Bin Location Attribute 9',
                'Activated' => 0,
                'FldType' => 'A'
            ],
            [
                'DispName' => 'Attribute 10',
                'KeyName' => 'Bin Location Attribute 10',
                'Activated' => 0,
                'FldType' => 'A'
            ]
        ];
        foreach ($data as $col) {
            OBFC::updateorcreate(
                [
                    'KeyName' => $col['KeyName'],
                    'Activated' => $col['Activated'],
                    'FldType' => $col['FldType']
                ],
                [
                    'DispName' => $col['DispName'],
                ]
            );
        }
    }
}
