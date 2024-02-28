<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1\Integrator;


use App\Models\THRDP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Leysco100\Shared\Models\Banking\Models\ORCT;
use Leysco100\Shared\Models\Banking\Models\RCT1;
use Leysco100\Shared\Models\Banking\Models\RCT2;
use Leysco100\Shared\Models\Banking\Models\RCT3;
use Leysco100\Shared\Models\Banking\Models\RCT4;
use Leysco100\Shared\Models\Administration\Models\NNM1;
use Leysco100\Shared\Models\Finance\Models\ChartOfAccount;
use Leysco100\Shared\Models\MarketingDocuments\Models\OINV;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\Shared\Models\Payments\Models\OCRP;

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
        Log::info("__________________PAYMENTS ID'S____________");
        Log::info([$paymentIDs]);
        Log::info("__________________PAYMENTS ID'S END____________");
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
        Log::info("__________________PAYMENTS DATA____________");
        Log::info([$data]);
        Log::info("__________________PAYMENTS DATA END____________");
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


        if ($document) {
            $payment = RCT3::where('DocNum', $id)->first();

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://cargen.pit.co.ke/api/v1/pit/updatepayresp',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode(array(
                    "TransID" => $payment->U_MpesaTxnNo,
                    "Amount" => $payment->CreditSum,
                )),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            Log::info(["PAYMENT UPDATED" => $response]);
        }


        return $document;
    }

    public function thirdPartyPayments(Request $request)
    {
        $data = $request['data'];
        foreach ($data as $key => $val) {
            $data = OCRP::updateOrCreate([
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
