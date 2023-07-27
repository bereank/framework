<?php

namespace Leysco100\Administration\Http\Controllers\Setup\General;




use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Administration\Http\Controllers\Controller;
use Leysco100\Shared\Models\Administration\Models\UserGroup;

class UserGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = UserGroup::get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        try {
            $role = UserGroup::create([
                'name' => $request['name'],
            ]);
            if ($request['users']) {
                foreach ($request['users'] as $key => $value) {
                    $value->assignRole($role);
                }
            }
            return (new ApiResponseService())->apiSuccessResponseService("Created Successfullty");
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = UserGroup::where('id', $id)->first();

        if (!$role) {
            return "User Group doesnt exist";
        }

        $documents = APDI::select('id', 'DocumentName', 'ObjectID')->get();
        foreach ($documents as $key => $value) {
            $read = Permission::select('name')
                ->where('apdi_id', $value->id)
                ->where('Label', 'read')
                ->first();

            $write = Permission::select('name')
                ->where('apdi_id', $value->id)
                ->where('Label', 'write')
                ->first();
            $update = Permission::select('name')
                ->where('apdi_id', $value->id)
                ->where('Label', 'update')
                ->first();

            if ($read) {
                $value->read = $role->hasPermissionTo($read->name);
            }

            if ($write) {
                $value->write = $role->hasPermissionTo($write->name);
            }

            if ($update) {
                $value->update = $role->hasPermissionTo($update->name);
            }
        }
        $role->documents = $documents;
        return $role;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        $AllUsers = User::get();

        $users = $request['users'];

        foreach ($AllUsers as $key => $value) {
            if ($value->hasRole($role->name) && !in_array($value->id, $users)) {
                $value->removeRole($role->name);
            } elseif (in_array($value->id, $users)) {
                $value->assignRole($role);
            }
        }
        return (new ApiResponseService())->apiSuccessResponseService();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = UserGroup::findOrFail($id);
        $user->delete();
    }
}
