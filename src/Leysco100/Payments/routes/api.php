
<?php


use Illuminate\Support\Facades\Route;
use Leysco100\Payments\Http\Controllers\API\PaymentsController;





Route::post('kcb/payments/notification', [PaymentsController::class, 'paymentNotification'])->withoutMiddleware(['auth:sanctum']);
Route::post('kcb/payments/query', [PaymentsController::class, 'paymentQuery'])->withoutMiddleware(['auth:sanctum']);
Route::apiResources(['payments' => PaymentsController::class]);
