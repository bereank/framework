<?php

namespace Leysco100\Administration\Console\Setup;

use App\Domains\Administration\Jobs\CreateMenuForUser;
use App\Domains\Administration\Models\User;
use Illuminate\Console\Command;

class CreateUserFormSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:user-form-settings {users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
