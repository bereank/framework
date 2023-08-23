
<?php

use Illuminate\Support\Facades\Route;
use Leysco100\Administration\Http\Controllers\Inventory\WarehouseController;
use Leysco100\Administration\Http\Controllers\Setup\Financials\TaxGroupController;


Route::get('activeGLaccounts', [ChartOfAccountController::class, 'fetchActiveAccounts']);
Route::get('getOutletsForRegions', [TerritoryController::class, 'getOutlets']);
Route::get('employee/territory/{TerritoryID}', [SalesEmployeeController::class, 'getForRegion']);
Route::put('employee/set_default/{EmployeeID}', [SalesEmployeeController::class, 'setDefault']);
Route::delete('territory/{TerritoryID}/{Employee}', [SalesEmployeeController::class, 'removeFromRegion']);
Route::post('employee/addNewRegion', [SalesEmployeeController::class, 'addNewRegion']);
Route::post('uploadGLAccounts', [ChartOfAccountController::class, 'importGLAccount']);
Route::post('documentnumbering/updateseries', [DocNumberingController::class, 'updatingSeries']);
Route::post('documentnumbering/createseries', [DocNumberingController::class, 'creatingSeries']);
Route::post('documentnumbering/set-default-current-user', [DocNumberingController::class, 'setDefaultCurrentUser']);
Route::post('documentnumbering/set-default-all-users', [DocNumberingController::class, 'setDefaultForAllUsers']);
Route::post('documentnumbering/set-default-selected-users', [DocNumberingController::class, 'setDefaultForSelectedUsers']);
Route::post('territory/{TerritoryID}/{EmployeeID}', [SalesEmployeeController::class, 'addEmployeeToRegion']);



   //Business Partnets
   Route::post('bp_properties_desc', [BPPropertiesController::class, 'propertDesc']);
   //Inventory
   Route::post('itemsproperty_desc', [ItemPropertyController::class, 'itemDesc']);
   // Permission
   Route::get('authorization/check-if-permitted/{ObjectCode}', [PermissionController::class, 'checkIfCurrentUserIsPermitted']);
   Route::post('authorization/assign-permission-to-user', [PermissionController::class, 'assignPermissionToUser']);
   Route::post('authorization/assign-permission-to-role', [PermissionController::class, 'assignPermissionToRole']);
   Route::get('users/{userID}/{ObjectType}', [UserController::class, 'fetchGroupPermission']);
   //Users
   Route::get('users/get-user-defaults', [UserController::class, 'fetchDefaultsForCurrentUser']);
   Route::get('users/inbox', [UserController::class, 'inbox']);
   Route::post('user-signature', [UserController::class, 'updateUserSignature']);
   Route::post('update-user-password', [UserController::class, 'userUpdatePassword']);

   //Open Documents
   Route::post('company_detail', [CompanyDetailsController::class, 'storeCompanyDetails']);
   Route::get('open-documents-fields/{ObjType}', [ExternalOpenDocumentsController::class, 'externalFieldDetails']);
   Route::apiResources(['open-documents' => ExternalOpenDocumentsController::class]);

   Route::get('gl_account-determination-category/{category}', [GLAccountDeterminationController::class, 'getAccountPerCategory']);
   Route::apiResources(['posting-periods' => PostingPeriodController::class]);
   Route::apiResources(['company_details' => CompanyDetailsController::class]);
   Route::apiResources(['general_settings' => GeneralSettingsController::class]);
   Route::apiResources(['permissions' => PermissionController::class]);
   Route::apiResources(['permissions' => PermissionController::class]);
   Route::apiResources(['users' => UserController::class]);
   Route::apiResources(['administration' => SystemSettingsController::class]);
   Route::apiResources(['gl_account_determination' => GLAccountDeterminationController::class]);
   Route::apiResources(['usergroup' => UserGroupController::class]);
   Route::apiResources(['user_defaults' => UserDefaultsController::class]);
   Route::apiResources(['departments' => DepartmentController::class]);
   Route::apiResources(['territories' => TerritoryController::class]);
   Route::apiResources(['commissiongroup' => CommissionGroupController::class]);
   Route::apiResources(['employee' => SalesEmployeeController::class]);
   Route::apiResources(['employee-master-data' => EmployeeController::class]);
   Route::apiResources(['drivers' => DriverController::class]);
   Route::apiResources(['itemgroup' => ItemGroupController::class]);
   Route::apiResources(['warehousetype' => WarehouseTypeConntroller::class]);
   Route::apiResources(['shippingtype' => ShippingTypeController::class]);
   Route::apiResources(['uom' => UoMController::class]);
   Route::apiResources(['manufacture' => ManufactureController::class]);
   Route::apiResources(['item-defaults' => ItemDefaultController::class]);
   Route::apiResources(['itemsproperty' => ItemPropertyController::class]);
   Route::apiResources(['currency' => CurrencyController::class]);
   Route::apiResources(['chartofaccounts' => ChartOfAccountController::class]);
   Route::apiResources(['taxgroup' => TaxGroupController::class]);
   Route::apiResources(['credit-card' => CreditCardController::class]);
   Route::apiResources(['country' => CountryController::class]);
   Route::apiResources(['bp_properties' => BPPropertiesController::class]);
   Route::apiResources(['paymentterm' => PaymentTermsController::class]);
   Route::apiResources(['bp_groups' => BPGroupController::class]);
   Route::apiResources(['bank' => BankController::class]);
   Route::apiResources(['house_bank' => HouseBankController::class]);
   Route::apiResources(['documentnumbering' => DocNumberingController::class]);
   Route::apiResources(['vehicles' => VehicleController::class]);