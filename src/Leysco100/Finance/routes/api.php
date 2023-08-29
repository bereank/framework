
<?php

use Illuminate\Support\Facades\Route;
use Leysco100\BusinessPartner\Http\Controllers\API\CostAccounting\DimensionController;
use Leysco100\Finance\Http\Controllers\API\BranchesController;
use Leysco100\Finance\Http\Controllers\API\CostAccounting\DistributionRulesController;
use Leysco100\Finance\Http\Controllers\API\CostCenterController;
use Leysco100\Finance\Http\Controllers\API\LocationController;

/*
|--------------------------------------------------------------------------
| Finance MODULE
|--------------------------------------------------------------------------
|
 */
Route::apiResources(['dimensions' => DimensionController::class]);
Route::apiResources(['cost-centers' => CostCenterController::class]);
Route::apiResources(['distribution-rules' => DistributionRulesController::class]);
Route::apiResources(['branches' => BranchesController::class]);
Route::apiResources(['locations' => LocationController::class]);
