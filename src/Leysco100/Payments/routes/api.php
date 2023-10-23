
<?php


use Illuminate\Support\Facades\Route;
use Leysco100\Payments\Http\Controllers\API\PaymentsController;


Route::apiResources(['payments' => PaymentsController::class]);
