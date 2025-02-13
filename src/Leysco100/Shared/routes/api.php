<?php




use Illuminate\Support\Facades\Route;
use Leysco100\Shared\Http\Controllers\API\FormSettingsController;
use Leysco100\Shared\Http\Controllers\API\QueryManagerController;
use Leysco100\Shared\Http\Controllers\API\UserDefinedFieldsController;
use Leysco100\Administration\Http\Controllers\Setup\General\GUserController;
use Leysco100\Shared\Http\Controllers\API\RecurringTransactionsTempController;
use Leysco100\Administration\Http\Controllers\SystemInit\GeneralSettings\GeneralSettingsController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::get('form_settings/{ObjType}', [FormSettingsController::class, 'getFormSettings']);
Route::post('form_settings', [FormSettingsController::class, 'updateFormSettings']);
Route::get('form_settings_menu', [FormSettingsController::class, 'formSettingsMenu']);
Route::post('form_settings_menu', [FormSettingsController::class, 'updateFormSettingsMenu']);
Route::post('form_settings_menu/{ID}', [FormSettingsController::class, 'updateSingleMenu']);
Route::get('form_settings_menu/user/{ID}', [FormSettingsController::class, 'getUserMenuSettings']);



Route::post('/recurringtransaction/next-execution', [RecurringTransactionsTempController::class, 'getNextExec']);
Route::get('/recurringtransactions', [RecurringTransactionsTempController::class, 'getRecurTrans']);

Route::apiResources(['user_fields' => UserDefinedFieldsController::class]);
Route::apiResources(['users' => GUserController::class]);
Route::apiResources(['general_settings' => GeneralSettingsController::class]);
Route::apiResources(['query_manager' => QueryManagerController::class]);
Route::apiResources(['recurringtransactiontemplates' => RecurringTransactionsTempController::class]);
