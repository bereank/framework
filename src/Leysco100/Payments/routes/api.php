
<?php


use Illuminate\Support\Facades\Route;
use Leysco100\Payments\Http\Controllers\API\PaymentsController;
use Leysco100\Payments\Http\Controllers\API\PaymentsProcessingController;





Route::post('kcb/payments/notification', [PaymentsProcessingController::class, 'kcbPaymentNotification'])->withoutMiddleware(['auth:sanctum']);
Route::post('kcb/payments/query', [PaymentsProcessingController::class, 'kcbPaymentQuery'])->withoutMiddleware(['auth:sanctum']);
Route::post('eqb/payments/validation', [PaymentsProcessingController::class, 'eqbValidation'])->withoutMiddleware(['auth:sanctum']);
Route::apiResources(['payments' => PaymentsController::class]);
