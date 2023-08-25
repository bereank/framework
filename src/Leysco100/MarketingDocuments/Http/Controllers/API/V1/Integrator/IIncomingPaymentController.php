<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1\Integrator;


use App\Models\THRDP;
use Illuminate\Http\Request;
use Leysco100\Shared\Models\Banking\Models\ORCT;
use Leysco100\Shared\Models\Banking\Models\RCT1;
use Leysco100\Shared\Models\Banking\Models\RCT2;
use Leysco100\Shared\Models\Banking\Models\RCT3;
use Leysco100\Shared\Models\Banking\Models\RCT4;
use Leysco100\Shared\Models\Administration\Models\NNM1;
use Leysco100\Shared\Models\Finance\Models\ChartOfAccount;
use Leysco100\Shared\Models\MarketingDocuments\Models\OINV;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;

class IIncomingPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $updated_at = \Request::get('updated_at');
        $paymentIDs = ORCT::with('rct2.invoice')
            ->whereHas('rct2.invoice', function ($q) {
                $q->whereNotNull('ExtRef');
            })
            ->whereNull('ExtRef')

            ->pluck('id');

        $data = ORCT::whereIn('id', $paymentIDs)->get();
        foreach ($data as $key => $value) {
            $value->DocType = "C";
            $value->DocNum = (int) $value->DocNum;
            $nnm1 = NNM1::where('id', $value->Series)->first();
            $value->Series = $nnm1 ? $nnm1->ExtRef : 289;

            $deals = RCT4::where('DocNum', $value->id)->get();
            foreach ($deals as $deal => $val) {
                $val->AcctCode = ChartOfAccount::where('id', $val->AcctCode)->value('AcctCode');
            }
            $value->paymentonAccountLines = $deals;
            $paymentInvoices = RCT2::where('DocNum', $value->id)->get();
            $syncInvoices = [];
            foreach ($paymentInvoices as $val => $invoice) {
                $invoice->DocLine = 1;
                $invoiceDocument = OINV::where('id', $invoice->DocEntry)->first();
                $invoice->DocEntry = (int) $invoiceDocument->ExtRef;
            }
            $value->paymentInvoices = $paymentInvoices;
            $value->paymentChecksLines = RCT1::where('DocNum', $value->id)->get();
            $value->paymentCreditCardLines = RCT3::where('DocNum', $value->id)->get();
        }

        return $data;
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
        $document = ORCT::where('id', $id)->first();
        $document->update([
            'ExtRef' => $request['ExtRef'],
            'ExtRefDocNum' => $request['ExtRefDocNum'],
        ]);

        $nnm1 = NNM1::where('id', $document->Series)->first();
        $document->Series = $nnm1 ? $nnm1->ExtRef : 289;
        return $document;
    }

    public function thirdPartyPayments(Request $request)
    {
        $data = $request['data'];
        foreach ($data as $key => $val) {
            $data = THRDP::updateOrCreate([
                'TransID' => $val['TransID'],
                'ExtRef' => $val['ExtRef'],
            ], [
                'ActCode' => $val['ActCode'],
                'CntName' => $val['CntName'],
                'CntPhone' => $val['CntPhone'],
                'TransAmount' => $val['TransAmount'],
                'TransTime' => $val['TransTime'],
                'Balance' => $val['Balance'],
            ]);
        }
    }
}
