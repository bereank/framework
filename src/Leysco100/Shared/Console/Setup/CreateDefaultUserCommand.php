<?php

namespace Leysco100\Shared\Console\Setup;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Hash;
use Leysco100\Shared\Models\Administration\Models\User;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;


class CreateDefaultUserCommand extends Command
{
    use TenantAware;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leysco100:shared:create-default-user {--tenant=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Default User';

    public function handle()
    {

        User::firstOrCreate([
            'email' => 'manager@leysco100.com',
        ],[
            'account' => 'manager',
            'account_type' => 'POS',
            'name' => 'Administrator',
            'DfltsGroup' => 1,
            'SUPERUSER' => 1,
            'gate_id' => 1,
            'password' => Hash::make('manager123'),
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),

        ]);
    }
}
