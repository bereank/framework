<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\Expense;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;




class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $expenses = Expense::with('CreatedBy:id,name,account,email,phone_number')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return (new ApiResponseService())->apiSuccessResponseService($expenses);
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
        $user = Auth::user();
        try {
            $this->validate($request, [
                'ExpType' => 'required',
                'Amount' => 'required',
            ]);

            $expense = Expense::create([
                'ExpType' => $request['ExpType'],
                'Amount' => $request['Amount'],
                'Summary' => $request['Summary'],
                'UserSign' => $user->id,
            ]);

            return (new ApiResponseService())->apiSuccessResponseService("Expense Successfully Created");
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
}
