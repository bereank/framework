<?php

namespace Leysco100\Administration\Console;

use Illuminate\Console\Command;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;
use Leysco100\Shared\Models\Administration\Models\Role;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\Administration\Models\Permission;


class CreatePermissions extends Command
{
    use TenantAware;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:create-permission-and-roles {--tenant=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creating Default Permission';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        // Default Roles
        $roles = ['Finance', 'Sales', 'Purchase', 'Inventory','Gate Pass'];

        //Creating Roles
        foreach ($roles as $key => $role) {
            $role = Role::updateOrcreate(['name' => $role]);
        }

        // Default Permissions
        $actions = ['read', 'write', 'update'];

        //Get Models
        $models = APDI::get();
        foreach ($models as $key => $model) {
            $pname = $model->PermissionName;
            //creating Permission
            foreach ($actions as $key => $val) {
                $fullPName = $pname . '_' . $val;
                $fPermissionName = ucwords($val . ' ' . str_replace('_', ' ', $pname));
                $permission = Permission::updateOrcreate(
                    ['name' => $fullPName],
                    [
                        'apdi_id' => $model->id,
                        'PermissionName' => $fPermissionName,
                        'Label' => $val,
                    ]
                );
                //Giving Permission to default user
                $user = User::find(1);
                $user->givePermissionTo($permission->id);
                // $user->assignRole(1);

                // $user2 = User::find(2);
                // $user2->givePermissionTo($permission->id);
                // $user2->assignRole(2);
            }
        }

        // $role = Role::find(1);
        // $allPermissions = Permission::pluck('name');
        // $role->givePermissionTo($allPermissions);

        // $role2 = Role::find(2);
        // $allPermissions = Permission::pluck('name');
        // $role2->givePermissionTo($allPermissions);
        $this->info("Create Permissions Complete");
    }
}
