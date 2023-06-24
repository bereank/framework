<?php

use Illuminate\Support\Facades\Route;
use Leysco100\Gpm\Http\Controllers\GPMController;
use Leysco100\Gpm\Http\Controllers\GateController;
use Leysco100\Gpm\Http\Controllers\LocationController;
use Leysco100\Gpm\Http\Controllers\AppSettingsController;
use Leysco100\Gpm\Http\Controllers\GMPDocumentController;
use Leysco100\Gpm\Http\Controllers\GPMFormFieldsController;
use Leysco100\Gpm\Http\Controllers\GPMMobileAPPApiController;
use Leysco100\Gpm\Http\Controllers\OtpVerificationController;


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
Route::put('/general_settings/ext_bucket', [AppSettingsController::class, 'updateExtBucket']);

Route::post('/scan-details', [GPMMobileAPPApiController::class, 'saveScanLogDetails']);
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
Route::apiResources(['form_fields' => GPMFormFieldsController::class]);
Route::get('field_types', [GPMFormFieldsController::class, 'getFieldTypes']);
Route::get('get_mobile_nav', [GPMFormFieldsController::class, 'getMobileNav']);
Route::put('update_mobile_nav', [GPMFormFieldsController::class, 'updateMobileNav']);


Route::get('gpm_reports', [GPMReports::class, 'getScanLogsByDate']);



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

/*
|--------------------------------------------------------------------------
| GATE PASS BACKUP PROCESS
|--------------------------------------------------------------------------
|
 */