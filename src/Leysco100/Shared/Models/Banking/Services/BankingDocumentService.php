<?php

namespace Leysco100\Shared\Models\Banking\Services;

use Leysco100\MarketingDocuments\Jobs\NumberingSeries;
use Leysco100\Shared\Models\Administration\Models\NNM1;
use Leysco100\Shared\Models\Banking\Models\OPDF;
use Leysco100\Shared\Models\Banking\Models\ORCT;
use Leysco100\Shared\Models\Banking\Models\PDF1;
use Leysco100\Shared\Models\Banking\Models\PDF2;
use Leysco100\Shared\Models\Banking\Models\PDF3;
use Leysco100\Shared\Models\Banking\Models\RCT1;
use Leysco100\Shared\Models\Banking\Models\RCT2;
use Leysco100\Shared\Models\Banking\Models\RCT3;
use Leysco100\Shared\Models\Banking\Models\RCT4;
use Leysco100\Shared\Models\Finance\Models\ChartOfAccount;
use Leysco100\Shared\Services\CommonService;

/**
 * Process Banking Document
 */
class BankingDocumentService
{
    /**
     * Process Incoming Payment
     */
    /**
     * Process Incoming Payment
     */
    public function processIncomingPayment($document, $request)
    {

        $numberingDetails = (new CommonService())->gettingObjectNumberingSeries(24);

        $IncomingPaymentDetails = [
            'CardCode' => $document->CardCode, //Customer/Vendor Code
            'CardName' => $document->CardName, //Customer/Vendor Name
            'DocNum' => $numberingDetails['DocNum'], //DocNUm
            'Series' => $numberingDetails['Series'],
            'DocType' => "C", // Document Type A=Account, C=Customer, D=TDS, P=P.L.A, S=Vendor, T=Tax
            'DocDate' => $document->DocDate, //PostingDate
            'TaxDate' => $document->TaxDate, //Document Date
            'DocDueDate' => $document->DocDueDate, // Delivery Date
            'DocTotal' => $request['TotalPaid'], //Document Total
            'CashAcct' => $request['CashAcct'] ?? null,
            'CashSum' => $request['CashSum'], // Cash Amount
            'CheckAcct' => $request['CheckAcct'] ?? null,
            'CheckSum' => $request['CheckSum'], //Check Amount
            'TrsfrAcct' => $request['TrsfrAcct'] ?? null,
            'TrsfrSum' => $request['TrsfrSum'], //Transfer Amount
            'TrsfrDate' => $request['TrsfrDate'] ?? now(), // Transfer Date
            'Ref1' => $request['Ref1'] ?? null, // ORCT reference
            'TrsfrRef' => $request['TrsfrRef'] ?? null, // Reference for Back Transfer
            'Comments' => $request['Comments'] ?? "Testing", // Remarks
            "U_Mpesa" => $request['U_Mpesa'] ?? null,
            "U_MRef" => $request['U_MRef'] ?? null,
            'UserSign' => $document->UserSign,
        ];

        $newPayment = new ORCT($IncomingPaymentDetails);

        $newPayment->save();

        //Saving RCT2

        $PaymentInvoiceDetails = [
            'InvType' => 13,
            "InvoiceId" => $document->DocNum,
            'DocNum' => $newPayment->id,
            'DocEntry' => $document->id,
            'SumApplied' => $request['TotalPaid'],
            'BfDcntSum' => $request['TotalPaid'],
        ];

        $newInvoiceDetails = new RCT2($PaymentInvoiceDetails);
        $newInvoiceDetails->save();

        // $invoice = OINV::where('id', $document->id)->first();
//        $invoiceBalance = $document->DocTotal - ($document->PaidToDate + $request['TotalPaid']);
        $InvoiceDetails = [
            'PaidToDate' => $document->PaidToDate ? $document->PaidToDate : $request['TotalPaid'],
        ];

        dd($InvoiceDetails);

        $document->update($InvoiceDetails);
        if (count($request['cheques']) > 0) {
            //Saving RCT1  Chekc Details
            foreach ($request['cheques'] as $key => $value) {
                $AllCheckDetails = [
                    'DocNum' => $newPayment->id,
                    'DueDate' => $value['DueDate'] ?? now(), //Check Date
                    'CheckSum' => $value['CheckSum'], //Check Amount
                    'CheckNum' => $value['CheckNum'], //Check Number
                    'CountryCod' => $value['CountryCod'] ?? null,
                    'BankCode' => $value['BankCode'] ?? null,
                    'Branch' => $value['Branch'] ?? null,
                    'AcctNum' => $value['AcctNum'] ?? null,
                    'Endorse' => $value['Endorse'] ?? null,
                ];
                $checkDetails = new RCT1($AllCheckDetails);
                $checkDetails->save();
            }
        }

        if (count($request['rct3']) > 0) {
            foreach ($request['rct3'] as $key => $value) {
                $AllCheckDetails = [
                    'DocNum' => $newPayment->id,
                    'LineID' => $key,
                    'CreditSum' => $value['CreditSum'],
                    'U_MpesaRef' => $value['U_MpesaRef'], //Mpesa Ref
                    'U_MpesaTxnNo' => $value['U_MpesaTxnNo'], //Mpesa Trans ID
                    'CreditCard' => $value['CreditCard'], //Credit Card Number
                    'CrCardNum' => $value['CrCardNum'] ?? null,
                    'CreditAcct' => $value['CreditAcct'],
                    'NumOfPmnts' => $value['NumOfPmnts'] ?? 1,
                    'CardValid' => "2025-12-31",
                    'VoucherNum' => $value['VoucherNum'] ?? "000",
                ];
                $checkDetails = new RCT3($AllCheckDetails);
                $checkDetails->save();
            }
        }

        NumberingSeries::dispatch($numberingDetails['Series']);

        return $newPayment;
    }
    public function processDraftIncomingPayment($document, $request)
    {

        $numberingDetails = (new CommonService())->gettingObjectNumberingSeries(140);

        $IncomingPaymentDetails = [
            'CardCode' => $document->CardCode, //Customer/Vendor Code
            'CardName' => $document->CardName, //Customer/Vendor Name
            'DocNum' => $numberingDetails['DocNum'], //DocNUm
            'Series' => $numberingDetails['Series'],
            'DocType' => "C", // Document Type A=Account, C=Customer, D=TDS, P=P.L.A, S=Vendor, T=Tax
            'DocDate' => $document->DocDate, //PostingDate
            'TaxDate' => $document->TaxDate, //Document Date
            'DocDueDate' => $document->DocDueDate, // Delivery Date
            'DocTotal' => $request['TotalPaid'], //Document Total
            'CashAcct' => $request['CashAcct'] ?? null,
            'CashSum' => $request['CashSum'], // Cash Amount
            'CheckAcct' => $request['CheckAcct'] ?? null,
            'CheckSum' => $request['CheckSum'], //Check Amount
            'TrsfrAcct' => $request['TrsfrAcct'] ?? null,
            'TrsfrSum' => $request['TrsfrSum'], //Transfer Amount
            'TrsfrDate' => $request['TrsfrDate'] ?? now(), // Transfer Date
            'Ref1' => $request['Ref1'] ?? null, // ORCT reference
            'TrsfrRef' => $request['TrsfrRef'] ?? null, // Reference for Back Transfer
            'Comments' => $request['Comments'] ?? "Testing", // Remarks
            "U_Mpesa" => $request['U_Mpesa'] ?? null,
            "U_MRef" => $request['U_MRef'] ?? null,
            'UserSign' => $document->UserSign,
        ];
        $newPayment = new OPDF($IncomingPaymentDetails);

        $newPayment->save();
        $prevPaymentInvoiceDetails  = PDF2::where("DocNum",$newPayment->id)
            ->get();

        //Saving RCT2
        $PaymentInvoiceDetails = [
            'InvType' => 13,
            "InvoiceId" => count($prevPaymentInvoiceDetails),
            "InvoiceDraftKey" => $document->id,
            'DocNum' => $newPayment->id,
            'DocEntry' => $document->id,
            'SumApplied' => $request['TotalPaid'],
            'BfDcntSum' => $request['TotalPaid'],
        ];

        $newInvoiceDetails = new PDF2($PaymentInvoiceDetails);
        $newInvoiceDetails->save();

        // $invoice = OINV::where('id', $document->id)->first();
//        $invoiceBalance = $document->DocTotal - ($document->PaidToDate + $request['TotalPaid']);
        $InvoiceDetails = [
            'PaidToDate' => $document->PaidToDate ? $document->PaidToDate : $request['TotalPaid'],
        ];

        $document->update($InvoiceDetails);
        if (count($request['cheques']) > 0) {
            //Saving RCT1  Chekc Details
            foreach ($request['cheques'] as $key => $value) {
                $AllCheckDetails = [
                    'DocNum' => $newPayment->id,
                    'DueDate' => $value['DueDate'] ?? now(), //Check Date
                    'CheckSum' => $value['CheckSum'], //Check Amount
                    'CheckNum' => $value['CheckNum'], //Check Number
                    'CountryCod' => $value['CountryCod'] ?? null,
                    'BankCode' => $value['BankCode'] ?? null,
                    'Branch' => $value['Branch'] ?? null,
                    'AcctNum' => $value['AcctNum'] ?? null,
                    'Endorse' => $value['Endorse'] ?? null,
                ];
                $checkDetails = new PDF1($AllCheckDetails);
                $checkDetails->save();
            }
        }

        if (count($request['rct3']) > 0) {
            foreach ($request['rct3'] as $key => $value) {
                $AllCheckDetails = [
                    'DocNum' => $newPayment->id,
                    'LineID' => $key,
                    'CreditSum' => $value['CreditSum'],
                    'U_MpesaRef' => $value['U_MpesaRef'], //Mpesa Ref
                    'U_MpesaTxnNo' => $value['U_MpesaTxnNo'], //Mpesa Trans ID
                    'CreditCard' => $value['CreditCard'], //Credit Card Number
                    'CrCardNum' => $value['CrCardNum'] ?? null,
                    'CreditAcct' => $value['CreditAcct'],
                    'NumOfPmnts' => $value['NumOfPmnts'] ?? 1,
                    'CardValid' => "2025-12-31",
                    'VoucherNum' => $value['VoucherNum'] ?? "000",
                ];
                $checkDetails = new PDF3($AllCheckDetails);
                $checkDetails->save();
            }
        }

        NumberingSeries::dispatch($numberingDetails['Series']);

        return $newPayment;
    }

    public function getInvoicePayment(int $invoiceDocEntry)
    {


        $paymentData = RCT2::where('DocEntry', $invoiceDocEntry)->first();

        if (!$paymentData) {
            return null;
        }
        $value = ORCT::where('id', $paymentData->DocNum)->first();
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
        }
        $value->paymentInvoices = $paymentInvoices;

        $value->paymentChecksLines = RCT1::where('DocNum', $value->id)->get();
        $value->paymentCreditCardLines = RCT3::where('DocNum', $value->id)->get();

        return $value;
    }

    public function getInvoicePaymentOld(int $invoiceDocEntry)
    {
        $paymentData = RCT2::where('DocEntry', $invoiceDocEntry)->first();
        if (!$paymentData) {
            return null;
        }
        $value = ORCT::where('id', $paymentData->DocNum)->first();
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
        }
        $value->paymentInvoices = $paymentInvoices;

        $value->paymentChecksLines = RCT1::where('DocNum', $value->id)->get();
        $value->paymentCreditCardLines = RCT3::where('DocNum', $value->id)->get();

        return $value;
    }
}
