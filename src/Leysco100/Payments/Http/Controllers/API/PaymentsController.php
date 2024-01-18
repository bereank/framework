<?php

namespace Leysco100\Payments\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Leysco100\Shared\Models\Payments\Models\CRP1;
use Leysco100\Shared\Models\Payments\Models\OCRP;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Payments\Http\Controllers\Controller;

class PaymentsController extends Controller
{

    public function index(Request $request)
    {
        $startdate = request()->filled('startDateTime') ? Carbon::parse(request()->input('startDateTime')) : Carbon::now()->startOfDay();

        $endate = request()->filled('endDateTime') ? Carbon::parse(request()->input('endDateTime')) : Carbon::now()->endOfDay();

        $paginate = request()->filled('paginate') ? request()->input('paginate') : false;

        $searchTerm = $request->input('search') ? $request->input('search') : false;

        $source = \Request::has('Source') ? explode(",", \Request::get('Source')) : [];
        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 50);

            $data = OCRP::latest();
            $data = $data->whereBetween('created_at', [
                $startdate,
                $endate,
            ])->when(!empty($source), function ($query) use ($source) {
                $query->whereIn('Source', $source);
            });

            if ($searchTerm) {
                $data = $data->where(function ($query) use ($searchTerm) {
                    $query->orWhereDate('created_at', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('Balance', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('TransID', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('FirstName', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('TransAmount', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('MSISDN', 'LIKE', "%{$searchTerm}%");
                });
            }
            if ($paginate) {
                $data = $data->paginate($perPage, ['*'], 'page', $page);
            } else {
                $data =   $data->get();
            }

            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function show($id)
    {

        try {
            $data = OCRP::find($id);
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            Log::info($th);
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        try {

            $data = OCRP::where('id', $id)([
                'DocNum' => $request['Balance'] ?? 0
            ]);


            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            Log::info($th);
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function incPayLineStore(Request $request)
    {
        try {
            $request->validate([
                'TransID' => 'required',
                'AllocatedAmount' => 'required',
                'DocEntry' => 'required'
            ]);
            $DocDate = Carbon::parse($request['DocDate']);
            $data = CRP1::updateOrCreate([
                'DocNum' => $request['DocNum'] ?? null, // References ORCT
                'TransID' => $request['TransID'] ?? null,
                'DocEntry' => $request['DocEntry'] ?? null, // References ORCP
            ], [
                'AllocatedAmount' => $request['AllocatedAmount'] ?? null,
                'DocDate' =>   $DocDate,
                'CreditAcct' => $request['CreditAcct'] ?? null,
                'ObjType' => 218,
                'CrTypeCode' => $request['CrTypeCode'] ?? null,
                'CreditCur' => $request['CreditCur'] ?? null,
                'OwnerPhone' => $request['OwnerPhone'] ?? null,
                'CrCardNum' =>   $request['CrCardNum'] ?? null,
                'CreditCard' =>   $request['CreditCard'] ?? null
            ]);
            //UPDATE BALANCES
            $header = OCRP::find($request['DocEntry']);
            if ($header) {
                $header->update(
                    [
                        'Balance' => $header->Balance - $request['AllocatedAmount']
                    ]
                );
            }
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            Log::info($th);
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
