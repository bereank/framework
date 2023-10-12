<?php


use Illuminate\Support\Facades\Route;
use Leysco100\Gpm\Http\Controllers\GPMReports;
use Leysco100\Gpm\Http\Controllers\GPMController;
use Leysco100\Gpm\Http\Controllers\GateController;
use Leysco100\Gpm\Http\Controllers\AppSettingsController;
use Leysco100\Gpm\Http\Controllers\GMPDocumentController;
use Leysco100\Gpm\Http\Controllers\GPMFormFieldsController;
use Leysco100\Gpm\Http\Controllers\API\GpmReportsController;
use Leysco100\Gpm\Http\Controllers\GPMMobileAPPApiController;
use Leysco100\Gpm\Http\Controllers\OtpVerificationController;
use Leysco100\Finance\Http\Controllers\API\LocationController;
use Leysco100\Gpm\Http\Controllers\API\GPMDashboardController;
use Leysco100\Gpm\Http\Controllers\API\FieldsTemplateController;
use Leysco100\Gpm\Http\Controllers\API\ErrorLogController;
use Leysco100\Gpm\Http\Controllers\API\GPMBackUpModeApiController;
use Leysco100\Gpm\Http\Controllers\API\BackupModeProcessController;
use Leysco100\Gpm\Http\Controllers\API\BackupModeSettingsController;



/*
|--------------------------------------------------------------------------
| GATE PASS MANAGEMENT
|--------------------------------------------------------------------------
|
 */

Route::put('/mark-not-released/{id}', [GPMMobileAPPApiController::class, 'doNotReleaseGoods']);

Route::get('app-form-fields', [GPMMobileAPPApiController::class, 'mobileAppFields']);
Route::get('user_scan_logs', [GPMMobileAPPApiController::class, 'getScanLogs']);
Route::put('/gms_docs_create', GMPDocumentController::class);

Route::get('web-gpm-documents', [GPMController::class, 'getGPMDocuments']);
Route::get('web-gpm-scan-logs', [GPMController::class, 'getScanLogs']);

Route::get('web-gpm-scan-logs/{id}', [GPMController::class, 'getSingleScanLogs']);

Route::get('/settings', AppSettingsController::class);
Route::put('ext_bucket/settings', [AppSettingsController::class, 'updateExtBucket']);

Route::post('/scan-details', [GPMMobileAPPApiController::class, 'saveScanLogDetails']);
Route::put('/scan-detail/{id}', [GPMMobileAPPApiController::class, 'updateScanLogDetails']);
Route::apiResources(['gpm_documents' => GPMMobileAPPApiController::class]);
Route::apiResources(['locations' => LocationController::class]);
Route::apiResources(['gates' => GateController::class]);
//filter unreleased docs

Route::get('filter_gpm_documents', [GPMMobileAPPApiController::class, 'filterScanLogs']);

// OTP Verification

Route::post('send_otp', [OtpVerificationController::class, 'SendOTPVerificationCode']);
Route::post('verify_otp', [OtpVerificationController::class, 'VerifyOTP']);

//scan logs export
Route::get('export-scan-logs', [GMPDocumentController::class, 'export_scan_logs']);
Route::get('export-sap-documents', [GMPDocumentController::class, 'export_sap_documents']);
//form fields

Route::get('field_types', [GPMFormFieldsController::class, 'getFieldTypes']);
Route::get('get_mobile_nav', [GPMFormFieldsController::class, 'getMobileNav']);
Route::put('update_mobile_nav', [GPMFormFieldsController::class, 'updateMobileNav']);
Route::get('gpm_reports', [GPMReports::class, 'getScanLogsByDate']);
Route::apiResources(['form_fields' => GPMFormFieldsController::class]);
Route::apiResources(['fields_template' => FieldsTemplateController::class]);


Route::get('gpm/dashboard', [GPMDashboardController::class, 'index']);
/*
|--------------------------------------------------------------------------
| Reports API's
|--------------------------------------------------------------------------
|
 */
Route::get('scanLogsReport', [GpmReportsController::class, 'ScanLogReport']);
Route::get('duplicateLogsReport', [GpmReportsController::class, 'DuplicateScanLogs']);
Route::get('documentReport', [GpmReportsController::class, 'DocumentReport']);
Route::get('doesNotExistReport', [GpmReportsController::class, 'DoesNotExistReport']);
Route::post('export_scan_report', [GpmReportsController::class, 'ExportScanLogReport']);


/*
|--------------------------------------------------------------------------
| GATE PASS BACKUP PROCESS
|--------------------------------------------------------------------------
|
 */
Route::get('bcp_doc_report', [GpmReportsController::class, 'BCPDocReport']);
Route::resource('bcm_settings', BackupModeSettingsController::class);
Route::apiResources(['back_up_mode' => BackupModeProcessController::class]);
// Route::apiResources(['back_up_mode/gpm_documents' => GPMBackUpModeApiController::class]);

Route::apiResources(['error_log' => ErrorLogController::class]);
