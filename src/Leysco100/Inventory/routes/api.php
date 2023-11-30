
<?php

use Illuminate\Support\Facades\Route;
use Leysco100\Inventory\Http\Controllers\API\DiscountsContoller;
use Leysco100\Inventory\Http\Controllers\API\PriceListController;
use Leysco100\Inventory\Http\Controllers\API\ItemMasterController;
use Leysco100\Inventory\Http\Controllers\API\MInventoryController;
use Leysco100\Inventory\Http\Controllers\API\BinLocationsController;
use Leysco100\Inventory\Http\Controllers\API\PeriodDiscountsContoller;
use Leysco100\Inventory\Http\Controllers\API\VolumeDiscountsContoller;
use Leysco100\Inventory\Http\Controllers\API\InventoryTransactionsController;
use Leysco100\Administration\Http\Controllers\Setup\Inventory\WarehouseController;

/*
    |--------------------------------------------------------------------------
    | INVENTORY MODULE
    |--------------------------------------------------------------------------
     */

//Route::get('fetch-item-with-code/{ItemCode}', [ItemMasterController::class, 'getItemUsingItemCode']);
Route::any('fetch-item-with-code', [ItemMasterController::class, 'getItemUsingItemCode']);
Route::post('inventory-transactions-data/{ObjType}', [InventoryTransactionsController::class, 'getDocData']);
Route::get('inventory-transactions-data/{ObjType}/{DocEntry}', [InventoryTransactionsController::class, 'getSingleDocData']);

Route::get('price_lists/items/{id}', [PriceListController::class, 'getPriceListItems']);
Route::get('uom-prices/{id}', [PeriodDiscountsContoller::class, 'getUomPrices']);

Route::apiResources(['price_lists' => PriceListController::class]);
Route::apiResources(['item_masterdata' => ItemMasterController::class]);
Route::apiResources(['inventory-transactions' => InventoryTransactionsController::class]);

//Discounts

Route::get('item-discounts', [DiscountsContoller::class, 'getItemDiscount']);
Route::apiResources(['period-and-vol-discounts' => DiscountsContoller::class]);
Route::apiResources(['period-discounts' => PeriodDiscountsContoller::class]);
Route::apiResources(['volume-discounts' => VolumeDiscountsContoller::class]);

//Inventory Reports
Route::get('inventory/report', [ItemMasterController::class, "inventory_report"]);
Route::get('inventory/serial-numbers/reports', [ItemMasterController::class, "serials_report"]);
//Route::apiResources(['item_properties' => ItemPropertiesController::class]);

Route::get('get-my-stock', [MInventoryController::class, 'getMyStock']);
Route::apiResources(['warehouse' => WarehouseController::class]);

//Bin Locations
Route::get('bin-location/{id}/inventory', [BinLocationsController::class, "BinLocationItems"]);
Route::apiResources(['warehouses/bin-location' => BinLocationsController::class]);
