<?php

namespace Leysco100\Administration\Http\Controllers\Setup\General;


use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Administration\Http\Controllers\Controller;
use Leysco100\Shared\Models\Administration\Models\Vehicle;


class VehicleController extends Controller
{
    /**
     * Display a listing of the vehicles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = Vehicle::get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Store a newly created vehicle in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

  
        try {
            $validatedData = $request->validate([
                'RegistrationNO' => 'required',
                'Make' => 'nullable|string',
                'Model' => 'nullable|string',
                'Brand' => 'nullable|string',
                'Year' => 'nullable|integer',
                'Color' => 'nullable|string',
                'Capacity' => 'nullable|integer',
                'Status' => 'nullable|boolean',
            ]);

            $vehicle = Vehicle::create($validatedData);

            return (new ApiResponseService())->apiSuccessResponseService("Created Successfullty");
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Display the specified vehicle.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);

            return (new ApiResponseService())->apiSuccessResponseService($vehicle);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Update the specified vehicle in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);

            $validatedData = $request->validate([
                'RegistrationNO' => [
                    'required',
                    Rule::unique('vehicles')->ignore($vehicle->id),
                ],
                'Make' => 'nullable|string',
                'Model' => 'nullable|string',
                'Brand' => 'nullable|string',
                'Year' => 'nullable|integer',
                'Color' => 'nullable|string',
                'Capacity' => 'nullable|integer',
                'Status' => 'nullable|boolean',
            ]);

            $vehicle->update($validatedData);

            return (new ApiResponseService())->apiSuccessResponseService("Updated Successfully");
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Remove the specified vehicle from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);

            $vehicle->delete();

            return (new ApiResponseService())->apiSuccessResponseService("Deleted Successfully");
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
