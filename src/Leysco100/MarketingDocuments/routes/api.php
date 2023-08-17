
<?php

use Illuminate\Support\Facades\Route;
use Leysco100\MarketingDocuments\Http\Controllers\API\DocumentController;


    /*
    |--------------------------------------------------------------------------
    | SALES MODULE
    |--------------------------------------------------------------------------
    |
     */
    // Route::get('pending_recur_trans', [RecurringTransactionsTempController::class, 'pendingTransactions']);
    // Route::post('documents', [DocumentController::class, 'store']);
    // Route::put('documents', [DocumentController::class, 'updateSingleDocument']);
    // Route::put('attachments', [DocumentController::class, 'upload']);
    Route::post('documents/{ObjType}', [DocumentController::class, 'getDocData']);
    // Route::post('marketing-doc-approvers/{ObjType}/{DocEntry}', [DocumentController::class, 'getDocumentApprovalStatus']);
    // Route::post('marketing-doc-close/{ObjType}/{DocEntry}', [DocumentController::class, 'closeSingleDocument']);
    // Route::post('marketing-doc-printed/{ObjType}/{DocEntry}', [DocumentController::class, 'markDocumentPrinted']);
    // Route::get('documents/{ObjType}/{DocEntry}', [DocumentController::class, 'getSingleDocData']);
    // //update Transferred to no after api for direct posting to sap fails
    // Route::post('sales_doc_update/{ObjType}/{docEntry}', [DocumentController::class, 'updateSingleDocData']);
    // Route::get('customer_sales_doc/{ObjType}', [DocumentController::class, 'getCustomerDocData']);
    // Route::get('form_settings/{ObjType}', [FormSettingsController::class, 'getFormSettings']);
    // Route::post('form_settings', [FormSettingsController::class, 'updateFormSettings']);
    // Route::get('form_settings_menu', [FormSettingsController::class, 'formSettingsMenu']);
    // Route::post('form_settings_menu', [FormSettingsController::class, 'updateFormSettingsMenu']);
    // Route::post('form_settings_menu/{ID}', [FormSettingsController::class, 'updateSingleMenu']);
    // Route::get('form_settings_menu/user/{ID}', [FormSettingsController::class, 'getUserMenuSettings']);
    // Route::apiResources(['drafts' => DraftController::class]);
    // Route::apiResources(['doc_model' => DocModelController::class]);
    // Route::apiResources(['blanketagreement' => BlanketAgreementController::class]);
    // Route::apiResources(['recurringtransactiontemplates' => RecurringTransactionsTempController::class]);
    // //Mpesa Callback
    // Route::post('mpesa-callback', [MpesaCallbackController::class, "mpesa_callback"])->withoutMiddleware(['auth:sanctum']);
    // Route::get('mpesa/transaction/data', [MpesaCallbackController::class, "getTransData"])->withoutMiddleware(['auth:sanctum']);
