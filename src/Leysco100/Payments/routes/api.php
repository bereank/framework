
<?php


use Illuminate\Support\Facades\Route;
use Leysco100\Payments\Http\Controllers\API\PaymentsController;
use Leysco100\Payments\Http\Controllers\API\PaymentsIntegrations\AriPaymentsController;
use Leysco100\Payments\Http\Controllers\API\PaymentsProcessingController;
use Leysco100\Payments\Http\Controllers\API\PaymentsIntegrations\MpesaController;



Route::post('payments/incoming/third-party/kcb/notification', [PaymentsProcessingController::class, 'kcbPaymentNotification'])->withoutMiddleware(['auth:sanctum']);
Route::post('payments/incoming/third-party/kcb/query', [PaymentsProcessingController::class, 'kcbPaymentQuery'])->withoutMiddleware(['auth:sanctum']);

Route::post('payments/incoming/third-party/eqb/validation', [PaymentsProcessingController::class, 'eqbValidation'])->withoutMiddleware(['auth:sanctum']);
Route::post('payments/incoming/third-party/eqb/notification', [PaymentsProcessingController::class, 'eqbPaymentNotification'])->withoutMiddleware(['auth:sanctum']);
Route::post('payments/incoming/third-party/eqb/query', [PaymentsProcessingController::class, 'eqbPaymentQuery'])->withoutMiddleware(['auth:sanctum']);

Route::post('/payments/incoming/third-party-lines', [PaymentsController::class, 'incPayLineStore']);


Route::apiResources(['payments/incoming/third-party' => PaymentsController::class]);

//ARI PAYMENTS
Route::post('mpesa-callback', [AriPaymentsController::class, "mpesa_callback"])->withoutMiddleware(['auth:sanctum']);
Route::get('mpesa/transaction/data', [AriPaymentsController::class, "getTransData"])->withoutMiddleware(['auth:sanctum']);
Route::post('payments/incoming/third-party/ari/stkpush', [AriPaymentsController::class, 'PromptStkPush']);
Route::post('payments/incoming/third-party/ari/trans-query', [AriPaymentsController::class, 'transQuery']);



Route::post('access/token', [MpesaController::class, 'mpesaAccessToken']);
Route::post('password/generate', [MpesaController::class, 'lipaNaMpesaPassword']);
Route::post('payments/incoming/third-party/daraja/confirmation-url', [MpesaController::class, 'MpesaConfirmation']);
Route::post('payments/incoming/third-party/daraja/validation-url', [MpesaController::class, 'MpesaValidation']);
Route::post('payments/incoming/third-party/daraja/register-urls', [MpesaController::class, 'MpesaRegisterURLS']);
Route::post('payments/incoming/third-party/daraja/stkpush', [MpesaController::class, 'PromptStkPush']);
Route::post('payments/incoming/third-party/daraja/callback', [MpesaController::class, 'MpesaConfirmation']);
