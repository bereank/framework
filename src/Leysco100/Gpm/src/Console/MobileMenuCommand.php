<?php

namespace Leysco\Gpm\Console;



use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;




class MobileMenuCommand extends Command
{
    // command definition: php artisan insert:mobile-menu
    protected $signature = 'insert:mobile-menu';

    protected $description = 'Insert the mobile menu data into the database';

    public function handle()
    {
        $data = [
            [
                "title" => "Release Goods",
                "key" => "home",
                "status" => 1,
            ],
            [
                "title" => "Pending Documents",
                "key" => "pendingdocuments",
                "status" => 1,
            ],
            [
                "title" => "Scan Logs",
                "key" => "scanlogs",
                "status" => 1,
            ],
            [
                "title" => "Gates",
                "key" => "gates",
                "status" => 1,
            ]
        ];


        foreach ($data as $item) {
            DB::table('mobile_nav_bars')->insert($item);
        }

        $this->info('data inserted successfully.');
    }
}
