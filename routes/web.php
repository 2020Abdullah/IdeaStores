<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CategoryTypeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\SkillsController;
use App\Http\Controllers\StoreHouseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WelcomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [WelcomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function(){
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // warehouse 
    Route::get('warehouse/index', [WarehouseController::class, 'index'])->name('warehouse.index');
    Route::get('warehouse/add', [WarehouseController::class, 'add'])->name('warehouse.add');
    Route::post('warehouse/store', [WarehouseController::class, 'store'])->name('warehouse.store');
    Route::post('getWallets', [WarehouseController::class, 'getWalletByWarhouse'])->name('getWallets');
    
    // wallets
    Route::get('warehouse/{id}/wallets', [WarehouseController::class, 'walletsIndex'])->name('wallets.index');
    Route::post('warehouse/wallet/store', [WarehouseController::class, 'walletStore'])->name('wallet.store');
    Route::post('warehouse/wallet/update', [WarehouseController::class, 'walletUpdate'])->name('wallet.update');
    Route::get('warehouse/wallet/{id}', [WarehouseController::class, 'walletShow'])->name('wallet.show');
    Route::post('wallet/balance/add', [WarehouseController::class, 'addBalance'])->name('wallet.balance.add');
    
    // Accounts
    Route::get('accounts/index', [AccountController::class, 'index'])->name('account.index');
    Route::get('account/add', [AccountController::class, 'add'])->name('account.add');
    Route::get('account/store', [AccountController::class, 'store'])->name('account.store');
    Route::get('account/{id}/show', [AccountController::class, 'show'])->name('account.show');

    // stores 
    Route::get('storesHouse/index', [StoreHouseController::class, 'index'])->name('storesHouse.index');
    Route::get('storesHouse/product/{id}/show', [StoreHouseController::class, 'show'])->name('storesHouse.product.show');
    Route::get('storesHouse/add', [StoreHouseController::class, 'add'])->name('storesHouse.add');
    Route::post('storesHouse/store', [StoreHouseController::class, 'store'])->name('storesHouse.store');

    // add product to stores
    Route::post('stock/store', [StoreHouseController::class, 'addProduct'])->name('addProduct');

    // units
    Route::get('units/index', [UnitController::class, 'index'])->name('units.index');
    Route::post('units/store', [UnitController::class, 'store'])->name('units.store');
    Route::post('units/update', [UnitController::class, 'update'])->name('units.update');
    Route::post('units/delete', [UnitController::class, 'delete'])->name('units.delete');
    Route::get('getUnits', [UnitController::class, 'getUnits'])->name('getUnits');
    
    // category
    Route::get('category/list', [CategoryController::class, 'index'])->name('category.index');
    Route::post('category/store', [CategoryController::class, 'store'])->name('category.store');
    Route::post('category/update', [CategoryController::class, 'update'])->name('category.update');
    Route::post('category/delete', [CategoryController::class, 'delete'])->name('category.delete');
    Route::post('get-subcategories', [CategoryController::class, 'getSubcategories'])->name('getSubcategories');
    Route::get('/get-all-hierarchical-categories', [CategoryController::class, 'getAllHierarchicalCategories'])->name('getAllHierarchicalCategories');

    // products
    Route::get('products/index', [ProductController::class, 'index'])->name('product.index');
    Route::get('product/add', [ProductController::class, 'add'])->name('product.add');
    Route::get('product/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
    Route::post('product/store', [ProductController::class, 'store'])->name('product.store');
    Route::post('product/update', [ProductController::class, 'update'])->name('product.update');
    Route::post('product/delete', [ProductController::class, 'delete'])->name('product.delete');
    Route::post('getProducts', [ProductController::class, 'getProducts'])->name('getProducts');
    
    // suppliers
    Route::get('suppliers/list', [SupplierController::class, 'index'])->name('supplier.index');
    Route::get('suppliers/{id}/profile', [SupplierController::class, 'profileShow'])->name('supplier.show');
    Route::get('supplier/add', [SupplierController::class, 'add'])->name('supplier.add');
    Route::get('supplier/edit/{id}', [SupplierController::class, 'edit'])->name('supplier.edit');
    Route::post('supplier/update', [SupplierController::class, 'update'])->name('supplier.update');
    Route::post('supplier/store', [SupplierController::class, 'store'])->name('supplier.store');
    Route::get('/suppliers/template', [SupplierController::class, 'downloadTemplate'])->name('download.supplier.Template');
    Route::post('/suppliers/import', [SupplierController::class, 'importSuppliers'])->name('import.supplier');
    Route::post('suppliers/Data/export', [SupplierController::class, 'exportData'])->name('supplier.data.export');

    Route::get('suppliers/invoices', [SupplierController::class, 'InvoiceIndex'])->name('supplier.invoice.index');
    Route::get('suppliers/invoices/add', [SupplierController::class, 'InvoiceAdd'])->name('supplier.invoice.add');
    Route::get('supplier/{id}/invoice/add', [SupplierController::class, 'InvoiceAdd'])->name('supplier.target.invoice.add');
    Route::post('suppliers/invoices/store', [SupplierController::class, 'InvoiceStore'])->name('supplier.invoice.store');
    Route::get('supplier/invoice/{id}/edit', [SupplierController::class, 'InvoiceEdit'])->name('supplier.invoice.edit');
    Route::post('suppliers/invoices/update', [SupplierController::class, 'InvoiceUpdate'])->name('supplier.invoice.update');
    Route::get('supplier/invoice/{code}/show', [SupplierController::class, 'InvoiceShow'])->name('supplier.invoice.show');
    Route::post('supplier/invoice/delete', [SupplierController::class, 'InvoiceDelete'])->name('supplier.invoice.delete');
    Route::get('supplier/invoice/{id}/download', [SupplierController::class, 'InvoiceDownload'])->name('supplier.invoice.download');
    Route::post('supplier/invoice/payment', [SupplierController::class, 'paymentInvoice'])->name('supplier.invoice.payment');
    Route::post('supplier/invoice/filter', [SupplierController::class, 'filterInvoices'])->name('supplier.invoice.filter');

    // setting 
    Route::get('setting', [SettingController::class, 'setting'])->name('setting.show');
    Route::post('Profile/update', [SettingController::class, 'updateProfile'])->name('setting.update');


    // sizes
    Route::get('sizes/index', [SizeController::class, 'index'])->name('size.index');
    Route::post('sizes/store', [SizeController::class, 'store'])->name('size.store');
    Route::post('sizes/update', [SizeController::class, 'update'])->name('size.update');
    Route::post('sizes/delete', [SizeController::class, 'delete'])->name('size.delete');
    Route::get('getSizes', [SizeController::class, 'getSizes'])->name('getSizes');

    // backup
    Route::get('/backup', fn() => view('backup'))->name('backup.view');
    Route::post('/backup/create', [BackupController::class, 'createBackup'])->name('backup.create');
    Route::post('/backup/restore', [BackupController::class, 'restoreBackup'])->name('backup.restore');

});

require __DIR__.'/auth.php';
