
<?php


use Illuminate\Support\Facades\Route;
use Leysco100\Payments\Http\Controllers\API\PaymentsController;
use Leysco100\Payments\Http\Controllers\API\PaymentsProcessingController;



Route::post('payments/incoming/third-party/kcb/notification', [PaymentsProcessingController::class, 'kcbPaymentNotification'])->withoutMiddleware(['auth:sanctum']);
Route::post('payments/incoming/third-party/kcb/query', [PaymentsProcessingController::class, 'kcbPaymentQuery'])->withoutMiddleware(['auth:sanctum']);

Route::post('payments/third-party/incoming/eqb/validation', [PaymentsProcessingController::class, 'eqbValidation'])->withoutMiddleware(['auth:sanctum']);

Route::apiResources(['payments/incoming/third-party' => PaymentsController::class]);
