<?php

use Illuminate\Support\Facades\Route;
use Leysco100\MarketingDocuments\Http\Controllers\API\DispatchController;
use Leysco100\MarketingDocuments\Http\Controllers\API\DocModelController;
use Leysco100\MarketingDocuments\Http\Controllers\API\DocumentController;
use Leysco100\MarketingDocuments\Http\Controllers\API\MpesaCallbackController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\Integrator\IDraftController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\MCallController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\MItemController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\MOrderController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\OutletController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\TargetController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\ApiAuthController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\ExpenseController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\MDashboardController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\MInventoryController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\MPricelistController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\GpsLocationController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V2\DocumentControllerPOC;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\RouteActionsController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\RoutePlanningController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\Integrator\ISharedController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\Integrator\ProductController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\Integrator\CustomerController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\Integrator\IInventoryController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\Integrator\ITerritoryController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\Integrator\ITransactionController;
use Leysco100\MarketingDocuments\Http\Controllers\API\V1\Integrator\IIncomingPaymentController;


/*
    |--------------------------------------------------------------------------
    | SALES MODULE
    |--------------------------------------------------------------------------
    |
     */
// Route::get('pending_recur_trans', [RecurringTransactionsTempController::class, 'pendingTransactions']);
Route::post('documents', [DocumentController::class, 'store']);
 Route::put('documents', [DocumentController::class, 'updateSingleDocument']);
 Route::put('attachments', [DocumentController::class, 'upload']);
Route::post('documents/{ObjType}', [DocumentController::class, 'getDocData']);
 Route::post('marketing-doc-approvers/{ObjType}/{DocEntry}', [DocumentController::class, 'getDocumentApprovalStatus']);
// Route::post('marketing-doc-close/{ObjType}/{DocEntry}', [DocumentController::class, 'closeSingleDocument']);
// Route::post('marketing-doc-printed/{ObjType}/{DocEntry}', [DocumentController::class, 'markDocumentPrinted']);
Route::get('documents/{ObjType}/{DocEntry}', [DocumentController::class, 'getSingleDocData']);
// //update Transferred to no after api for direct posting to sap fails
// Route::post('sales_doc_update/{ObjType}/{docEntry}', [DocumentController::class, 'updateSingleDocData']);
// Route::get('customer_sales_doc/{ObjType}', [DocumentController::class, 'getCustomerDocData']);
 Route::apiResources(['drafts' => DraftController::class]);
Route::apiResources(['doc_model' => DocModelController::class]);
// Route::apiResources(['blanketagreement' => BlanketAgreementController::class]);
// Route::apiResources(['recurringtransactiontemplates' => RecurringTransactionsTempController::class]);
// //Mpesa Callback
 Route::post('mpesa-callback', [MpesaCallbackController::class, "mpesa_callback"])->withoutMiddleware(['auth:sanctum']);
 Route::get('mpesa/transaction/data', [MpesaCallbackController::class, "getTransData"])->withoutMiddleware(['auth:sanctum']);

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
//Route::apiResources(['tiers' => TierController::class]);
//Route::apiResources(['channels' => ChannelController::class]);
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


/**
 * ----------------------------------------------------------------------------------------------
 *
 *                          MOBILE APIS
 *
 * ----------------------------------------------------------------------------------------------
 */
Route::post('password-change', [ApiAuthController::class, 'promptPasswordChange']);
       
    //        Route::post('login', [ApiAuthController::class, 'login']);
    Route::group(['middleware' => ['auth:sanctum']], function () {
        //OpenCall

        Route::post('openCall/{callID}', [MCallController::class, 'openCall']);
        Route::post('closeCall/{callID}', [MCallController::class, 'closeCall']);

        Route::post('osa', [RouteActionsController::class, 'OnShelfAvailabilty']);
        // Route::post('pricetracking', 'API\V1\RouteActionsController@PriceTracking');
        // Route::post('shareofshelf', 'API\V1\RouteActionsController@ShareOfShelf');
        // Route::post('productplacement', 'API\V1\RouteActionsController@ProductPlacement');
        // Route::post('callobjective', 'API\V1\RouteActionsController@CallObjective');
        // Route::post('contactperson', 'API\V1\OutletController@CreateContactPerson');

        //DifferentOrders
        Route::post('CashOrder', [MOrderController::class, 'CashOrder']);

        //Payment
        //         Route::post('stk-push', [PaymentController::class,"stkPush"]);

        //  Route::post('CashOrderAndPayment', [PaymentController::class, "CashOrderAndPayment"]);
        // Route::post('AdvancePayment', 'API\V1\PaymentController@AdvancePayment');
        // Route::post('AllInvoicesPayment', 'API\V1\PaymentController@AllInvoicesPayment');
        // Route::get('getAllPayments', 'API\V1\PaymentController@getAllPayments');

        //outletprofile
        Route::get('outlet/{OutletID}/{Type}', [OutletController::class, 'SingleOutlet']);
        Route::get('customerMapFilter', [OutletController::class, 'customerMapFilter']);
        Route::get('outletsCategory', [OutletController::class, 'outletCategory']);

        //Get Product Unit of Measure
        Route::get('item-uom/{ougpID}', [MItemController::class, 'getUnitOfMeasure']);
        Route::post('item-price', [MItemController::class, 'getDefaultPrice']);
        Route::get('productcategory', [MInventoryController::class, 'getProductCategory']);
        Route::get('unitofmeasure', [MItemController::class, 'getAllUnitOfMeasure']);

        //Inventory
        Route::get('get-my-stock', [MInventoryController::class, 'getMyStock']);
        Route::get('warehouse', [MInventoryController::class, 'getWarehouse']);

        //Orders
        Route::get('order-types', [MOrderController::class, 'getOrderTypes']);

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
        //Get All Prices:
        Route::get('all-item-prices', [MPricelistController::class, 'itemPrices']);

        //Company
        Route::get('settings', [ApiAuthController::class, 'companySetupData']);

        Route::apiResources(['expense' => ExpenseController::class]);
        Route::apiResources(['dashboard' => MDashboardController::class]);
         Route::apiResources(['outlet' => OutletController::class]);
        Route::apiResources(['item' => MItemController::class]);
        Route::apiResources(['call' => MCallController::class]);
        Route::apiResources(['order' => MOrderController::class]);
        // Route::apiResources(['invoice' => 'API\V1\InvoiceController']);
        // Route::apiResources(['CashOrder' => 'API\V1\OrderController']);
        // Route::apiResources(['delivery' => 'API\V1\DeliveryController']);
        // Route::apiResources(['ARinvoice' => 'API\V1\InvoiceController']);
        //   Route::apiResources(['assettracking' => AssetTrackingController::class]);
        // Route::apiResources(['banks' => 'API\V1\BankController']);
        //  Route::apiResources(['territory' => MTerritoryController::class]);
        Route::apiResources(['pricelist' => MPricelistController::class]);
        Route::apiResources(['stock_request' => MInventoryController::class]);
    });


/**
 * ----------------------------------------------------------------------------------------------
 *
 *                          INTEGRATOT APIS
 *
 * ----------------------------------------------------------------------------------------------
 */

//Route::group(
//    [
//        'prefix' => 'integrator',
//    ],
//    function () {
        Route::group(['middleware' => ['auth:sanctum']], function () {
            Route::get('/enabled-pricelist', [ProductController::class, 'getEnabledPriceList']);
            Route::get('/object-update-status', [ProductController::class, 'getObjectUpdateStatus']);

            Route::get('/retailers/search', [CustomerController::class, 'searchRetailer']);
            Route::get('/regions/search', [ITerritoryController::class, 'searchRegion']);
            Route::get('/customergroups/search', [CustomerController::class, 'searchCustomerGroup']);
            Route::post('/customergroups/create', [CustomerController::class, 'createCustomerGroup']);

            Route::get('/products/search', [ProductController::class, 'searchProduct']);

            Route::put('/addOrUpdateItemGroup', [ProductController::class, 'addOrUpdateItemGroup']);

            Route::get('/pricelist/search', [ProductController::class, 'searchPriceList']);
            Route::post('/pricelist/create', [ProductController::class, 'createPriceList']);

            Route::get('/product/pricelist/search', [ProductController::class, 'searchProductPriceList']);
            Route::post('/product/pricelist/create', [ProductController::class, 'createProductPriceList']);
            Route::put('/product/serial_number', [ProductController::class, 'createProductSerialNumber']);
            Route::put('/product/prices', [ProductController::class, 'updateProductPrices']);
            Route::put('/product/uomprices', [ProductController::class, 'updateProductUomPrices']);

            //Route::get('/uom/search', [ProductController::class, 'searchUOM']);
            Route::post('/uom/create', [ProductController::class, 'createUOM']);

            Route::get('/uomgroup', [ProductController::class, 'getAllUomGroup']);
            Route::get('/uoms', [ProductController::class, 'getAllUoms']);
            Route::get('/uomgroup/search', [ProductController::class, 'searchUOMGroup']);
            Route::post('/uomgroup/create', [ProductController::class, 'createUOMGroup']);

            // Route::put('/purchase-requests/{purchaserRequestID}', [IPurchaeRequestController::class, 'updateExtRef']);
            // Route::put('/outgoing-payments/{outgoingPaymentID}', [IOutgoingPaymentController::class, 'updateExtRef']);
            Route::put('/inventory_costcentrequantities', [IInventoryController::class, 'updateCostCentreQuantities']);
            Route::put('/inventory/inventory_contents', [IInventoryController::class, 'update']);

            //Documents
             Route::post('/drafts/{draftKey}/{ObjType}', [IDraftController::class, 'createDocumentFromDraft']);

            Route::post('/third-party-payments', [IIncomingPaymentController::class, 'thirdPartyPayments']);

            Route::get('/transactions/{ObjType}', [ITransactionController::class, 'getTransactions']);
            Route::put('/transactions/{ObjType}/{DocEntry}', [ITransactionController::class, 'updateTransactions']);
            Route::post('/transactions/error-log/{ObjType}', [ITransactionController::class, 'postTransactionErrorLog']);

            Route::post('/transactions_create', [ITransactionController::class, 'createOpeningBalanceTransaction']);
            Route::get('/transactions_search/{ObjType}', [ITransactionController::class, 'searchOpeningBalanceTransaction']);
            Route::get('/transactions_open/{ObjType}', [ITransactionController::class, 'getOpeningBalanceTransaction']);

            Route::put('/cost_center_type', [ISharedController::class, 'createOrUpdareCostCenterType']);
            Route::put('/cost_centers', [ISharedController::class, 'createOrUpdareCostCenters']);
            Route::put('/distribution_rules', [ISharedController::class, 'createOrUpdateDistributionRules']);

            Route::put('/aprrovals/approval_lines', [ITransactionController::class, 'addApprovalDetails']);

            //Service Call
            Route::get('/service-calls', [ITransactionController::class, 'getServiceCall']);
            Route::put('/solutions', [ITransactionController::class, 'createOrUpdateSolutions']);
            Route::put('/equipment-cards', [ITransactionController::class, 'createOrUpdateEquipmentCard']);
            Route::get('/equipment-cards', [ITransactionController::class, 'getEquipmentCard']);

            //POST INTEGRATOR
            Route::get('/service-calls', [ITransactionController::class, 'getServiceCall']);

            // Dispathc Process
            Route::put('/routes', [ITerritoryController::class, 'createOrUpdateRoutes']);


            Route::apiResources(['inventory' => IInventoryController::class]);
            Route::apiResources(['retailers' => CustomerController::class]);
            Route::apiResources(['regions' => ITerritoryController::class]);
            Route::apiResources(['products' => ProductController::class]);
            //   Route::apiResources(['iusers' => IUsersController::class]);
            //  Route::apiResources(['purchase-requests' => IPurchaeRequestController::class]);
            // Route::apiResources(['outgoing-payments' => IOutgoingPaymentController::class]);
            Route::apiResources(['incoming-payments' => IIncomingPaymentController::class]);

            //ogms create
            Route::put('/gms_docs_create', [ITransactionController::class, 'createDocumentForGateManagementModule']);
            //        Route::put('/gms_docs_create', GMPDocumentController::class);
        });
//    }
//);

Route::post('v2-create-document', [DocumentControllerPOC::class, 'store']);