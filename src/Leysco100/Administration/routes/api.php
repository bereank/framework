
<?php


Route::post('documentnumbering/updateseries', [DocNumberingController::class, 'updatingSeries']);
Route::post('documentnumbering/createseries', [DocNumberingController::class, 'creatingSeries']);
Route::post('documentnumbering/set-default-current-user', [DocNumberingController::class, 'setDefaultCurrentUser']);
Route::post('documentnumbering/set-default-all-users', [DocNumberingController::class, 'setDefaultForAllUsers']);
Route::post('documentnumbering/set-default-selected-users', [DocNumberingController::class, 'setDefaultForSelectedUsers']);
Route::get('getOutletsForRegions', [TerritoryController::class, 'getOutlets']);
Route::get('employee/territory/{TerritoryID}', [SalesEmployeeController::class, 'getForRegion']);
Route::put('employee/set_default/{EmployeeID}', [SalesEmployeeController::class, 'setDefault']);
Route::delete('territory/{TerritoryID}/{Employee}', [SalesEmployeeController::class, 'removeFromRegion']);
Route::post('territory/{TerritoryID}/{EmployeeID}', [SalesEmployeeController::class, 'addEmployeeToRegion']);
Route::post('employee/addNewRegion', [SalesEmployeeController::class, 'addNewRegion']);
Route::get('activeGLaccounts', [ChartOfAccountController::class, 'fetchActiveAccounts']);
Route::post('uploadGLAccounts', [ChartOfAccountController::class, 'importGLAccount']);
Route::get('taxgroups/{Type}', [TaxGroupController::class, 'TaxGroupType']);
