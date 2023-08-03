
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BusinessPartner\DealerController;
use App\Http\Controllers\API\BusinessPartner\BusinessPartnerController;



/*
|--------------------------------------------------------------------------
| BUSINESS MODULE
|--------------------------------------------------------------------------
 */

Route::post('bp_import', [BusinessPartnerController::class, 'importBusinessPartner']);
Route::get('dealer_branches', [DealerController::class, 'getBranches']);
Route::get('getVendors', [BusinessPartnerController::class, 'getVendors']);
Route::get('getCustomers', [BusinessPartnerController::class, 'getCustomers']);
Route::get('getDistributors', [BusinessPartnerController::class, 'getDistributors']);
Route::get('customer/{CustomerID}/{ObjType}', [BusinessPartnerController::class, 'getCustomerDocuments']);
Route::get('customer/{CustomerID}/{ObjType}/{DocStatus}', [BusinessPartnerController::class, 'getCustomerDocumentsStatus']);
Route::apiResources(['bp_masterdata' => BusinessPartnerController::class]);
Route::apiResources(['dealer_masterdata' => DealerController::class]);
    