<?php

namespace App\Http\Controllers\API\Administration\Setup\SystemInit\Authorization;

use App\Domains\Administration\Models\User;
use App\Domains\Shared\Models\APDI;
use App\Domains\Shared\Services\ApiResponseService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Permission::get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Users Permission Revoke or Assign
     *
     * @param int $id User ID
     * @param {Object[]} models - models are the value from apdi.
     * @param int models[].id - APDI ID
     * @param int models[].Permission - 0:Not No Authorization, 1: Read Only , 2:Full Authorization
     */
    public function assignPermissionToUser(Request $request)
    {
        $user = User::find($request['id']);
        if (!$user) {
            return (new ApiResponseService())->apiFailedResponseService("User Not Found");
        }
        foreach ($request['documents'] as $key => $value) {
            $read = Permission::where('apdi_id', $value['id'])
                ->where('Label', 'read')
                ->first();
            $write = Permission::where('apdi_id', $value['id'])
                ->where('Label', 'write')
                ->first();
            $update = Permission::where('apdi_id', $value['id'])
                ->where('Label', 'update')
                ->first();
            if ($read) {
                $newRead = $value['read'] ? $user->givePermissionTo($read->id) :
                $user->revokePermissionTo($read->id);
            }
            if ($write) {
                $newWrite = $value['write'] ? $user->givePermissionTo($write->id) :
                $user->revokePermissionTo($write->id);
            }

            if ($update) {
                $newUpdarte = $value['update'] ? $user->givePermissionTo($update->id) :
                $user->revokePermissionTo($update->id);
            }
        }
        return (new ApiResponseService())->apiSuccessResponseService();
    }

    /**
     *  Assing Role Permissions
     *  @param  \Illuminate\Http\Request  $request
     *  @param int $request.id
     * @param int models[].id - APDI ID
     * @param int models[].Permission - 0:Not No Authorization, 1: Read Only , 2:Full Authorization
     */
    public function assignPermissionToRole(Request $request)
    {
        $role = Role::where('id', $request['id'])->first();

        if (!$role) {
            return (new ApiResponseService())->apiFailedResponseService("Role Not Found");
        }
        foreach ($request['documents'] as $key => $value) {
            $read = Permission::where('apdi_id', $value['id'])
                ->where('Label', 'read')
                ->first();
            $write = Permission::where('apdi_id', $value['id'])
                ->where('Label', 'write')
                ->first();
            $update = Permission::where('apdi_id', $value['id'])
                ->where('Label', 'update')
                ->first();
            $newRead = $value['read'] ? $role->givePermissionTo($read->name) :
            $role->revokePermissionTo($read->name);
            $newWrite = $value['write'] ? $role->givePermissionTo($write->id) :
            $role->revokePermissionTo($write->id);
            $newUpdarte = $value['update'] ? $role->givePermissionTo($update->id) :
            $role->revokePermissionTo($update->id);
        }
        return (new ApiResponseService())->apiSuccessResponseService();
    }

    /**
     * Checking if login in user is Authorized to access document.
     */
    public function checkIfCurrentUserIsPermitted($ObjType)
    {
        try {
            $user = Auth::user();
            $document = APDI::where('ObjectID', $ObjType)
                ->first();
            if (!$user) {
                return (new ApiResponseService())->apiFailedResponseService("User Not Found");
            }

            if (!$document) {
                return (new ApiResponseService())->apiFailedResponseService("Document Not Found");
            }

            $docPermissions = Permission::select('name')
                ->where('apdi_id', $document->id)
                ->first();
            if (!$docPermissions) {
                return (new ApiResponseService())->apiFailedResponseService("Permissions Not Found");
            }
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }

        // return $user->roles;

        $read = Permission::select('name')
            ->where('apdi_id', $document->id)
            ->where('Label', 'read')
            ->first();

        $write = Permission::select('name')
            ->where('apdi_id', $document->id)
            ->where('Label', 'write')
            ->first();
        $update = Permission::select('name')
            ->where('apdi_id', $document->id)
            ->where('Label', 'update')
            ->first();
        $document->read = true;
        $document->write = true;
        $document->update = true;

        return $document;
    }
}
