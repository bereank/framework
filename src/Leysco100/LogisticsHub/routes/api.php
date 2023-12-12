
<?php


use Illuminate\Support\Facades\Route;
use Leysco100\LogisticsHub\Http\Controllers\API\V1\MCallController;
use Leysco100\LogisticsHub\Http\Controllers\API\V1\TargetController;
use Leysco100\LogisticsHub\Http\Controllers\API\V1\ExpenseController;
use Leysco100\MarketingDocuments\Http\Controllers\API\TierController;
use Leysco100\LogisticsHub\Http\Controllers\API\V1\DispatchController;
use Leysco100\LogisticsHub\Http\Controllers\API\V1\ITerritoryController;
use Leysco100\MarketingDocuments\Http\Controllers\API\ChannelController;
use Leysco100\LogisticsHub\Http\Controllers\API\V1\GpsLocationController;
use Leysco100\LogisticsHub\Http\Controllers\API\V1\RouteActionsController;
use Leysco100\LogisticsHub\Http\Controllers\API\V1\RoutePlanningController;
use Leysco100\LogisticsHub\Http\Controllers\API\V1\EmployeeTimeSheetController;


/*
|--------------------------------------------------------------------------
|  SALES DISTRIBUTION
|--------------------------------------------------------------------------
*/
//Route::post('weekly_calls', [CallsController::class, 'weeklyCallsReport']);
//Route::post('get_calls_reports', [CallsController::class, 'getCallsReports']);
//Route::get('getrules/{id}', [SurveyController::class, 'getRules']);
//Route::get('getschedules/{id}', [SurveyController::class, 'getSchedules']);
//Route::get('getchoices/{id}', [RulesController::class, 'getChoices']);
//Route::post('calls/filterCalls', [CallsController::class, 'filterCalls']);


Route::get('items_data', [TargetController::class, 'ItemsData']);
//Route::apiResources(['calls' => CallsController::class]);
Route::apiResources(['tiers' => TierController::class]);
Route::apiResources(['channels' => ChannelController::class]);
//Route::apiResources(['assets' => AssetsController::class]);
//Route::apiResources(['surveys' => SurveyController::class]);
//Route::apiResources(['schedules' => ScheduleController::class]);
//Route::apiResources(['rules' => RulesController::class]);

// Route Planning
Route::post('route_outlets', [RoutePlanningController::class, 'createRouteOutlets']);
Route::post('route_calls', [RoutePlanningController::class, 'createRouteCalls']);
Route::apiResources(['routes' => RoutePlanningController::class]);

// GPS LOCATIONS
Route::get('getWorkDays', [GpsLocationController::class, 'getWorkDays']);
Route::resource('gps-locations', GpsLocationController::class);

/*

/*
|--------------------------------------------------------------------------
| Dispatch
|--------------------------------------------------------------------------
|
*/
Route::put('document/cancellation/{ObjType}/{id}', [DispatchController::class, 'documentCancellation']);
Route::get('/dispatch/summary-reports', [DispatchController::class, 'getSummaries']);
Route::apiResources(['/dispatch/documents' => DispatchController::class]);


//Sales Targets
Route::get('emp-targets', [TargetController::class, 'getEmpTargets']);
Route::get('sales_reps/targets', [TargetController::class, 'salesRepsTargets']);
Route::get('target/items/{id}', [TargetController::class, 'getTargetItems']);
Route::get('target_employeese/{id}', [TargetController::class, 'getTargetEmployeese']);
Route::put('remove_target_slp/{id}', [TargetController::class, 'removeTargetSlp']);
Route::get('target_rows/{id}', [TargetController::class, 'getEmployeesTargets']);
Route::post('add_slp_to_target', [TargetController::class, 'addSlpToTarget']);
Route::get('getTargetsVsPerfomance', [TargetController::class, 'getTargetsVsPerfomance']);
Route::apiResources(['targets' => TargetController::class]);
Route::get('get_targets_skus/{id}', [TargetController::class, 'showSkus']);
Route::get('open_employee_timesheet', [EmployeeTimeSheetController::class, 'getClocInDetails']);

  //OpenCall

  Route::post('openCall/{callID}', [MCallController::class, 'openCall']);
  Route::post('closeCall/{callID}', [MCallController::class, 'closeCall']);

  Route::post('osa', [RouteActionsController::class, 'OnShelfAvailabilty']);
  // Route::post('pricetracking', 'API\V1\RouteActionsController@PriceTracking');
  // Route::post('shareofshelf', 'API\V1\RouteActionsController@ShareOfShelf');
  // Route::post('productplacement', 'API\V1\RouteActionsController@ProductPlacement');
  // Route::post('callobjective', 'API\V1\RouteActionsController@CallObjective');
  // Route::post('contactperson', 'API\V1\OutletController@CreateContactPerson');
  Route::apiResources(['expense' => ExpenseController::class]);
  Route::apiResources(['call' => MCallController::class]);

  Route::put('/routes', [ITerritoryController::class, 'createOrUpdateRoutes']);
  Route::apiResources(['regions' => ITerritoryController::class]);
  
Route::apiResources(['employee-timesheet' => EmployeeTimeSheetController::class]);