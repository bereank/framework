<?php

namespace Leysco100\Administration\Http\Controllers\Setup\General;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Administration\Models\OADM;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Administration\Http\Controllers\Controller;


class GUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            $data = User::with('gates')->get();
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

        // $this->validate($request, [
        //     'name' => 'required|string|max:191',
        //     'phone_number' => 'required|unique:users',
        //     'email' => 'required|string|email|max:191|unique:users',
        //     'password' => 'required|string|min:6',
        // ]);

        $rules = [
            'name' => 'required|string|max:191',
            'phone_number' => 'required|unique:users',
            'email' => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:6',
        ];


        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $message = $validator->errors()->first();
            return (new ApiResponseService())->apiFailedResponseService($message);
        }

        try {

            $SUPERUSER = 0;
            if ($request['SUPERUSER'] == true) {
                $SUPERUSER = 1;
            }

            $data =  User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'account' => $request['name'],
                'DfltsGroup' => $request['DfltsGroup'] ?? 1,
                'phone_number' => $request['phone_number'],
                'SUPERUSER' => $SUPERUSER,
                'password' => Hash::make($request['password']),
                'ExtRef' => $request['ExtRef'],
                'type' => $request['type'],
                'gate_id' => $request['gate_id'],
                'status' => 1,
                'EmpID' => $request['EmpID'] ?? null,

            ]);
            return (new ApiResponseService())->apiSuccessResponseService("Created Successfully");
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollback();
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
        try {
            $data = User::where('id', $id)->first();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            DB::connection("tenant")->rollback();
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
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

        try {
            $settings = OADM::first();

            $password_changed = true;
            if ($settings->PswdChangeOnReset) {
                $password_changed = false;
            }
            $user = User::where('id', $id)->first();
            $user->update(array_filter([
                'name' => $request['name'],
                'email' => $request['email'],
                'account' => $request['name'],
                'DfltsGroup' => $request['DfltsGroup'] ?? 1,
                'phone_number' => $request['phone_number'],
//                                'SUPERUSER' => $SUPERUSER,
                'password' => $request["password"] ? Hash::make($request['password']) : $user->password,
                'ExtRef' => $request['ExtRef'],
                'type' => $request['type'],
                'gate_id' => $request['gate_id'],
//                                'status' => $STATUS,
                'EmpID' => $request['EmpID'] ?? null,
                'localUrl' => $request['localUrl']
            ]));
            $user->update([
                'useLocalSearch' => $request['useLocalSearch'] == true ? 1 : 0,
                'SUPERUSER' => $request['SUPERUSER'] == true ? 1 : 0,
                'status' => $request['status'] == true ? 1 : 0,
                'password_changed' =>   $password_changed,
            ]);
            return (new ApiResponseService())->apiSuccessResponseService("Created Successfully");
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if (!$user) {
            return (new ApiResponseService())->apiFailedResponseService("User $id not found");
        }
        $user->delete();
        return (new ApiResponseService())->apiSuccessResponseService("User $id deleted successfully");
    }
}
