
<?php

use Illuminate\Support\Facades\Route;
use Leysco100\Inventory\Http\Controllers\API\PriceListController;
use Leysco100\Inventory\Http\Controllers\API\ItemMasterController;
use Leysco100\Inventory\Http\Controllers\API\MInventoryController;
use Leysco100\Inventory\Http\Controllers\API\BinLocationsController;
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
Route::apiResources(['price_lists' => PriceListController::class]);
Route::apiResources(['item_masterdata' => ItemMasterController::class]);
Route::apiResources(['inventory-transactions' => InventoryTransactionsController::class]);

//Inventory Reports
Route::get('inventory/report', [ItemMasterController::class, "inventory_report"]);
Route::get('inventory/serial-numbers/reports', [ItemMasterController::class, "serials_report"]);
//Route::apiResources(['item_properties' => ItemPropertiesController::class]);

Route::get('get-my-stock', [MInventoryController::class, 'getMyStock']);
Route::apiResources(['warehouse' => WarehouseController::class]);

//Bin Locations
Route::apiResources(['warehouses/bin-location' => BinLocationsController::class]);