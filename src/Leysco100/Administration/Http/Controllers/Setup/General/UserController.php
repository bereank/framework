<?php

namespace App\Http\Controllers\API\Administration\Setup\General;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Domains\Shared\Models\APDI;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Mail\SystemNotificationMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Services\AuthorizationService;
use Spatie\Permission\Models\Permission;
use App\Mail\UserCredentialsNotification;
use App\Domains\Administration\Models\OAIB;
use App\Domains\Administration\Models\User;
use App\Domains\Administration\Models\USR1;
use App\Domains\BusinessPartner\Models\OBPL;
use App\Domains\Shared\Services\ApiResponseService;
use App\Domains\Administration\Jobs\CreateMenuForUser;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ObjType = 6;
        $TargetTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();
        (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'read');
        try {
            $data = User::select('id', 'name', 'email', 'phone_number', 'status', 'active_until')->get();
            //            $data = $TargetTables->select('id', 'name', 'email', 'phone_number', 'status', 'active_until')->get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
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
        $ObjType = 6;
        $TargetTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();
        (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'create');
        $user = User::where('email', $request['email'])->first();
        if ($user) {
            return (new ApiResponseService())->apiFailedResponseService("Email Taken");
        }

        DB::beginTransaction();
        try {
            $SUPERUSER = 0;
            if ($request['SUPERUSER'] == true) {
                $SUPERUSER = 1;
            }
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'account' => $request['account'],
                'DfltsGroup' => $request['DfltsGroup'],
                'phone_number' => $request['phone_number'],
                'SUPERUSER' => $SUPERUSER,
                'password' => Hash::make($request['password']),
                'all_Branches' => $request['BPLid'] == -1 ? 1 : 0,
                'ExtRef' => $request['ExtRef'],
                'type' => $request['type'],
                'status' => 1,
                'EmpID' => $request['EmpID'],
            ]);

            if ($request['usergroups']) {
                foreach ($request['usergroups'] as $key => $value) {
                    $user->assignRole($value);
                }
            }

            if ($request['BPLid'] == -1) {
                $branches = OBPL::pluck('id');
                $user->branches()->sync($branches);
            } else {
                $user->branches()->sync($request['branches']);
            }

            if ($request['password']) {
                $user->update([
                    'password' => Hash::make($request['password']),
                ]);
            }

            if ($request['SUPERUSER'] == true) {
                $user->getAllPermissions();
            }

            CreateMenuForUser::dispatch($user->id);
            DB::commit();
            return (new ApiResponseService())->apiSuccessResponseService("Created Successfully");
        } catch (\Throwable $th) {
            DB::rollback();
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
        $ObjType = 6;
        $TargetTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();
        (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'read');
        $user = User::where('id', $id)->first();

        if (!$user) {
            return (new ApiResponseService())->apiFailedResponseService("User not found");
        }

        $SUPERUSER = false;

        if ($user->SUPERUSER == 1) {
            $SUPERUSER = true;
            $user->getAllPermissions();
        }
        $user = User::where('id', $id)
            ->with('nnm2', 'oudg.warehouse', 'oudg.employee', 'oudg.driver')
            ->first();

        $user->usergroups = $user->roles()->pluck("id");
        $user->branches = $user->branches()->pluck('o_b_p_l_s.id')->toArray();

        if ($user->all_Branches == 0) {
            $branch = USR1::where('user_id', $user->id)->first();
            if ($branch) {
                $BPLid = $branch->id;

                if ($BPLid) {
                    $user->BPLid = $BPLid;
                }
            }
        }

        if ($user->all_Branches == 1) {
            $BPLid = -1;

            if ($BPLid) {
                $user->BPLid = $BPLid;
            }
        }

        $user->SUPERUSER = $SUPERUSER;
        //return $user->roles;
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
            //  dd([$read->name, $write->name, $update->name]);
            if ($read) {
                $value->read = $user->hasPermissionTo($read->name);
                // return  [$value->read, $read->name, $value->id];
            }
            if ($write) {
                $value->write = $user->hasPermissionTo($write->name);
            }
            if ($update) {
                $value->update = $user->hasPermissionTo($update->name);
            }
        }
        //  return response()->json($documents);
        $user->documents = $documents;
        return (new ApiResponseService())->apiSuccessResponseService($user);
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
        $ObjType = 6;
        $TargetTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();
        (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'update');
        $currenlyLoginUser = Auth::user();
        $oldUserData = User::where('id', $id)->first();

        $user = User::where('id', $id)->first();
        if (!$user) {
            return (new ApiResponseService())->apiFailedResponseService("User Does not exist");
        }

        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,' . $user->id,
        ]);

        if ($request['password']) {
            $this->validate($request, [
                'password' => 'sometimes|min:6',
            ]);
        }

        $SUPERUSER = 0;
        $status = 0;
        if ($request['SUPERUSER'] == true) {
            $SUPERUSER = 1;
        }
        if ($request['status'] == true) {
            $status = 1;
        }
        DB::beginTransaction();
        try {
            $user->update([
                'name' => $request['name'],
                'email' => $request['email'],
                'DfltsGroup' => $request['DfltsGroup'],
                'Department' => $request['Department'],
                'SUPERUSER' => $SUPERUSER,
                'ExtRef' => $request['ExtRef'],
                'EmpID' => $request['EmpID'],
                'type' => $request['type'],
                'status' => $status,
                'useLocalSearch' => $request['useLocalSearch'] == true ? 1 : 0,
                'localUrl' => $request['localUrl'],
            ]);

            if ($request['password']) {
                $user->update([
                    'password' => Hash::make($request['password']),
                ]);
            }

            $user->branches()->sync($request['branches']);
            if ($request['BPLid'] == -1) {
                $branches = OBPL::pluck('id');
                $user->branches()->sync($branches);
            }

            $user->roles()->detach();

            if ($request['usergroups']) {
                foreach ($request['usergroups'] as $key => $value) {
                    $user->assignRole($value);
                }
            }

            $newUserData = User::where('id', $id)->first();

            // $email = 'berean.kibet@leysco.co.ke';
            // Mail::to($email)
            //     ->cc("gilbert.mutai@cargen.com")
            //     ->send(new SystemNotificationMail($oldUserData, $newUserData, $currenlyLoginUser->id));

            if ($request['e_pass']) {
                $email = $request['email'];
                Mail::to($email)
                    ->cc("robert.kimaru@leysco.co.ke")
                    ->send(new UserCredentialsNotification($request, $currenlyLoginUser->id));
            }

            DB::commit();
            return (new ApiResponseService())->apiSuccessResponseService("Updated Successfully");
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::info($th);
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function fetchGroupPermission($userID, $ObjectType)
    {
        $user = User::select('id')->find($userID);

        $document = APDI::select('id', 'DocumentName', 'ObjectID')
            ->where('ObjectID', $ObjectType)
            ->first();
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
        foreach ($user->roles as $key => $val) {
            $val->read = $val->hasPermissionTo($read->name);
            $val->write = $val->hasPermissionTo($write->name);
            $val->update = $val->hasPermissionTo($update->name);
        }

        return $user->roles;
    }

    public function fetchDefaultsForCurrentUser()
    {
        $user = Auth::user();
        return $user->oudg;
    }

    public function inbox()
    {
        $user = Auth::user();
        try {
            $data = OAIB::with('oalr.odrf.objecttype', 'oalr.sendby')->where('UserSign', $user->id)
                ->get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Upload User Signature
     */
    public function updateUserSignature(Request $request)
    {
        $user = User::where('account', $request['username'])->first();

        if ($user) {
            $sigNaure = base64_encode(file_get_contents($request->signatureData));
            $user->update([
                "signaturePath" => $sigNaure,
            ]);
        }
        return "Done";
    }

    /**
     * Update User Password
     */

    public function userUpdatePassword(Request $request)
    {

        $user = Auth::user();

        if (!$request->old_password || !$user->password || !$request->new_confirm_password) {
            return (new ApiResponseService())->apiFailedResponseService("Fields required");
        }

        $check = !Hash::check($request->old_password, $user->password);
        if ($check) {
            return (new ApiResponseService())->apiFailedResponseService("Current Password is not valid");
        }

        $check = Hash::check($request->new_password, $request->new_confirm_password);
        if ($check) {
            return (new ApiResponseService())->apiFailedResponseService("Password doest not match");
        }

        $user->update([
            'password' => Hash::make($request['new_password']),
        ]);

        return (new ApiResponseService())->apiSuccessResponseService("Updated Successfully");
    }
}
