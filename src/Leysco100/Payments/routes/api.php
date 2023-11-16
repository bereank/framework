
<?php


use Illuminate\Support\Facades\Route;
use Leysco100\Payments\Http\Controllers\API\PaymentsController;
use Leysco100\Payments\Http\Controllers\API\PaymentsProcessingController;



Route::post('payments/incoming/third-party/kcb/notification', [PaymentsProcessingController::class, 'kcbPaymentNotification'])->withoutMiddleware(['auth:sanctum']);
Route::post('payments/incoming/third-party/kcb/query', [PaymentsProcessingController::class, 'kcbPaymentQuery'])->withoutMiddleware(['auth:sanctum']);

Route::post('payments/incoming/third-party/eqb/validation', [PaymentsProcessingController::class, 'eqbValidation'])->withoutMiddleware(['auth:sanctum']);
Route::post('payments/incoming/third-party/eqb/notification', [PaymentsProcessingController::class, 'eqbPaymentNotification'])->withoutMiddleware(['auth:sanctum']);
Route::post('payments/incoming/third-party/eqb/query', [PaymentsProcessingController::class, 'eqbPaymentQuery'])->withoutMiddleware(['auth:sanctum']);

Route::post('/payments/incoming/third-party-lines', [PaymentsController::class, 'incPayLineStore']);


Route::apiResources(['payments/incoming/third-party' => PaymentsController::class]);
