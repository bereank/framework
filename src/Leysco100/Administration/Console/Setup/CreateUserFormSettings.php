<?php

namespace Leysco100\Administration\Console\Setup;

use Illuminate\Console\Command;
use Leysco100\Shared\Models\Administration\Jobs\CreateMenuForUser;
use Leysco100\Shared\Models\Administration\Models\User;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;

class CreateUserFormSettings extends Command
{
    use TenantAware;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leysco100:administration:create-user-form-settings {users} {--tenant=*}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create User Form Settings';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = explode(',', $this->argument('users'));

        foreach ($users as $list){
            $user = User::find($list);
            if ($user){
                CreateMenuForUser::dispatch($user->id);
            }
        }
        return Command::SUCCESS;
    }
}
