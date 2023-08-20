
<?php

use Illuminate\Support\Facades\Route;


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
