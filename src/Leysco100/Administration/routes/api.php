<?php

use Illuminate\Support\Facades\Route;
use Leysco100\Administration\Http\Controllers\Approvals\ApprovalStagesControlller;
use Leysco100\Administration\Http\Controllers\Approvals\ApprovalTemplatesControlller;
use Leysco100\Administration\Http\Controllers\Setup\Banking\BankController;
use Leysco100\Administration\Http\Controllers\Setup\General\InitialSetUpController;
use Leysco100\Administration\Http\Controllers\Setup\General\UserController;
use Leysco100\Administration\Http\Controllers\Setup\Inventory\UoMController;
use Leysco100\Administration\Http\Controllers\Setup\General\DriverController;
use Leysco100\Administration\Http\Controllers\Setup\General\VehicleController;
use Leysco100\Administration\Http\Controllers\Setup\General\EmployeeController;
use Leysco100\Administration\Http\Controllers\Setup\Banking\HouseBankController;
use Leysco100\Administration\Http\Controllers\Setup\General\TerritoryController;
use Leysco100\Administration\Http\Controllers\Setup\General\UserGroupController;
use Leysco100\Administration\Http\Controllers\SystemInit\DocNumberingController;
use Leysco100\Administration\Http\Controllers\Setup\General\DepartmentController;
use Leysco100\Administration\Http\Controllers\Setup\Inventory\UoMGroupController;
use Leysco100\Administration\Http\Controllers\Setup\Financials\CurrencyController;
use Leysco100\Administration\Http\Controllers\Setup\Financials\TaxGroupController;
use Leysco100\Administration\Http\Controllers\Setup\Inventory\ItemGroupController;
use Leysco100\Administration\Http\Controllers\Setup\Inventory\WarehouseController;
use Leysco100\Administration\Http\Controllers\Setup\General\UserDefaultsController;
use Leysco100\Administration\Http\Controllers\Setup\Financials\CreditCardController;
use Leysco100\Administration\Http\Controllers\Setup\General\SalesEmployeeController;
use Leysco100\Administration\Http\Controllers\Setup\Inventory\ItemDefaultController;
use Leysco100\Administration\Http\Controllers\Setup\Inventory\ManufactureController;
use Leysco100\Administration\Http\Controllers\Setup\Inventory\ItemPropertyController;
use Leysco100\Administration\Http\Controllers\Setup\Inventory\ShippingTypeController;
use Leysco100\Administration\Http\Controllers\Setup\BusinessPartners\BPGroupController;
use Leysco100\Administration\Http\Controllers\Setup\BusinessPartners\CountryController;
use Leysco100\Administration\Http\Controllers\Setup\General\AlertsManagementController;
use Leysco100\Administration\Http\Controllers\Setup\Financials\ChartOfAccountController;
use Leysco100\Administration\Http\Controllers\Setup\BusinessPartners\BPPropertiesController;
use Leysco100\Administration\Http\Controllers\SystemInit\Authorization\PermissionController;
use Leysco100\Administration\Http\Controllers\SystemInit\Authorization\DataOwnershipController;
use Leysco100\Administration\Http\Controllers\SystemInit\CompanyDetails\CompanyDetailsController;
use Leysco100\Administration\Http\Controllers\SystemInit\GeneralSettings\GeneralSettingsController;
use Leysco100\Administration\Http\Controllers\Setup\Financials\GLDetermination\GLAccountDeterminationController;
use Leysco100\Administration\Http\Controllers\Setup\General\TimeSheetsMasterController;
use Leysco100\Administration\Http\Controllers\Setup\Inventory\BinLocationsController;

Route::get('activeGLaccounts', [ChartOfAccountController::class, 'fetchActiveAccounts']);
Route::get('taxgroups/{Type}', [TaxGroupController::class, 'TaxGroupType']);
// Route::get('getOutletsForRegions', [TerritoryController::class, 'getOutlets']);
// Route::get('employee/territory/{TerritoryID}', [SalesEmployeeController::class, 'getForRegion']);
// Route::put('employee/set_default/{EmployeeID}', [SalesEmployeeController::class, 'setDefault']);
// Route::delete('territory/{TerritoryID}/{Employee}', [SalesEmployeeController::class, 'removeFromRegion']);
// Route::post('employee/addNewRegion', [SalesEmployeeController::class, 'addNewRegion']);
// Route::post('uploadGLAccounts', [ChartOfAccountController::class, 'importGLAccount']);
Route::post('documentnumbering/updateseries', [DocNumberingController::class, 'updatingSeries']);
Route::post('documentnumbering/createseries', [DocNumberingController::class, 'creatingSeries']);
Route::post('documentnumbering/set-default-current-user', [DocNumberingController::class, 'setDefaultCurrentUser']);
Route::post('documentnumbering/set-default-all-users', [DocNumberingController::class, 'setDefaultForAllUsers']);
Route::post('documentnumbering/set-default-selected-users', [DocNumberingController::class, 'setDefaultForSelectedUsers']);
// Route::post('territory/{TerritoryID}/{EmployeeID}', [SalesEmployeeController::class, 'addEmployeeToRegion']);


// //Business Partnets
// Route::post('bp_properties_desc', [BPPropertiesController::class, 'propertDesc']);
// //Inventory
// Route::post('itemsproperty_desc', [ItemPropertyController::class, 'itemDesc']);
// // Permission
Route::get('authorization/check-if-permitted/{ObjectCode}', [PermissionController::class, 'checkIfCurrentUserIsPermitted']);
Route::post('authorization/assign-permission-to-user', [PermissionController::class, 'assignPermissionToUser']);
Route::post('authorization/assign-permission-to-role', [PermissionController::class, 'assignPermissionToRole']);
Route::get('users/auth/{userID}', [UserController::class, 'show']);
Route::get('users/{userID}/{ObjectType}', [UserController::class, 'fetchGroupPermission']);
Route::apiResources(['permissions' => PermissionController::class]);

//alerts
Route::get('getAlert/{id}', [AlertsManagementController::class, 'getAlert']);

Route::get('alert/variables', [AlertsManagementController::class, 'getAlertVariables']);
Route::post('alert/variable/create', [AlertsManagementController::class, 'createAlertVariables']);
Route::get('alert/variable/show/{id}', [AlertsManagementController::class, 'showAlertVariable']);
Route::put('alert/variable/edit/{id}', [AlertsManagementController::class, 'editAlertVariable']);

Route::get('alerts/mail_template/show/{id}', [AlertsManagementController::class, 'AlertTemplate']);

// BIN LOCATIONS
Route::get('bin-locations/fields', [BinLocationsController::class, 'getBinLocFields']);
Route::post('bin-locations/fields/create', [BinLocationsController::class, 'storeBinLocFields']);

Route::get('bin-locations/sublevel_code', [BinLocationsController::class, 'subLevelsIndex']);
Route::get('bin-locations/sublevel_code/{id}', [BinLocationsController::class, 'getSubLevel']);
Route::post('bin-locations/sublevel_code/create', [BinLocationsController::class, 'storeSubLevels']);
Route::put('bin-locations/sublevel_code/{id}', [BinLocationsController::class, 'editSubLevels']);

Route::get('bin-locations/atrributes', [BinLocationsController::class, 'attributesIndex']);
Route::get('bin-locations/atrributes/{id}', [BinLocationsController::class, 'getAttribute']);
Route::post('bin-locations/atrributes/create', [BinLocationsController::class, 'storeAttributes']);
Route::put('bin-locations/atrributes/{id}', [BinLocationsController::class, 'editAttributes']);


// //Users
// Route::get('users/get-user-defaults', [UserController::class, 'fetchDefaultsForCurrentUser']);
// Route::get('users/inbox', [UserController::class, 'inbox']);
// Route::post('user-signature', [UserController::class, 'updateUserSignature']);
// Route::post('update-user-password', [UserController::class, 'userUpdatePassword']);

// //Open Documents
// Route::post('company_detail', [CompanyDetailsController::class, 'storeCompanyDetails']);
// Route::get('open-documents-fields/{ObjType}', [ExternalOpenDocumentsController::class, 'externalFieldDetails']);
// Route::apiResources(['open-documents' => ExternalOpenDocumentsController::class]);

// Route::get('gl_account-determination-category/{category}', [GLAccountDeterminationController::class, 'getAccountPerCategory']);
//Route::apiResources(['posting-periods' => PostingPeriodController::class]);
Route::apiResources(['company_details' => CompanyDetailsController::class]);
Route::apiResources(['general_settings' => GeneralSettingsController::class]);
//Route::apiResources(['users' => UserController::class]);
// Route::apiResources(['administration' => SystemSettingsController::class]);
Route::apiResources(['gl_account_determination' => GLAccountDeterminationController::class]);
Route::apiResources(['usergroup' => UserGroupController::class]);
Route::apiResources(['user_defaults' => UserDefaultsController::class]);
Route::apiResources(['departments' => DepartmentController::class]);
Route::apiResources(['territories' => TerritoryController::class]);
// Route::apiResources(['commissiongroup' => CommissionGroupController::class]);
Route::apiResources(['employee' => SalesEmployeeController::class]);
Route::apiResources(['employee-master-data' => EmployeeController::class]);
Route::apiResources(['drivers' => DriverController::class]);
Route::apiResources(['itemgroup' => ItemGroupController::class]);
// Route::apiResources(['warehousetype' => WarehouseTypeConntroller::class]);
Route::apiResources(['warehouse' => WarehouseController::class]);
Route::apiResources(['shippingtype' => ShippingTypeController::class]);
Route::apiResources(['uomgroup' => UoMGroupController::class]);
Route::apiResources(['uom' => UoMController::class]);
Route::apiResources(['manufacture' => ManufactureController::class]);
Route::apiResources(['item-defaults' => ItemDefaultController::class]);
Route::apiResources(['itemsproperty' => ItemPropertyController::class]);
Route::apiResources(['currency' => CurrencyController::class]);
Route::apiResources(['chartofaccounts' => ChartOfAccountController::class]);
Route::apiResources(['taxgroup' => TaxGroupController::class]);
Route::apiResources(['credit-card' => CreditCardController::class]);
Route::apiResources(['payment-methods' => CreditCardController::class]);
Route::apiResources(['country' => CountryController::class]);
Route::apiResources(['bp_properties' => BPPropertiesController::class]);
// Route::apiResources(['paymentterm' => PaymentTermsController::class]);
Route::apiResources(['bp_groups' => BPGroupController::class]);
Route::apiResources(['bank' => BankController::class]);
Route::apiResources(['house_bank' => HouseBankController::class]);
Route::apiResources(['documentnumbering' => DocNumberingController::class]);
Route::apiResources(['vehicles' => VehicleController::class]);
Route::put('/settings/password_rest_change', [GeneralSettingsController::class, 'updatePswdChangOnReset']);
Route::apiResources(['alerts' => AlertsManagementController::class]);
Route::apiResources(['data-ownerships' => DataOwnershipController::class]);

Route::apiResources(['approval_stages' => ApprovalStagesControlller::class]);
Route::apiResources(['approval_templates' => ApprovalTemplatesControlller::class]);
Route::apiResources(['timesheets-master-data' => TimeSheetsMasterController::class]);




Route::apiResources(['initial_setup' => InitialSetUpController::class]);



