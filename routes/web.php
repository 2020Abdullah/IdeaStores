<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\customer\SalesController;
use App\Http\Controllers\DueController;
use App\Http\Controllers\ExponseItemController;
use App\Http\Controllers\ExternalDebtsController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StoreHouseController;
use App\Http\Controllers\Supplier\InvoicePurchaseController;
use App\Http\Controllers\Supplier\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\WalletsController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WelcomeController;
use App\Models\App;

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

Route::group(['middleware' => ['auth', 'CheckAppActive']], function(){
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/sales-chart', [DashboardController::class, 'salesChart'])->name('dashboard.sales.chart');
     
    // warehouse 
    Route::get('warehouse/index', [WarehouseController::class, 'index'])->name('warehouse.index');
    Route::get('warehouse/add', [WarehouseController::class, 'add'])->name('warehouse.add');
    Route::post('warehouse/store', [WarehouseController::class, 'store'])->name('warehouse.store');
    Route::post('warehouse/sync', [WarehouseController::class, 'walltetsSync'])->name('wallets.sync');
    Route::get('warehouse/{id}/transactions/show', [WarehouseController::class, 'showTransactions'])->name('warehouse.transactions');
    Route::post('warehouse/transfer', [WarehouseController::class, 'transfer'])->name('warehouse.transfer');
    Route::post('getWallets', [WarehouseController::class, 'getWalletByWarhouse'])->name('getWallets');
    
    // wallets
    Route::get('wallets/index', [WalletsController::class, 'index'])->name('wallets.index');
    Route::post('wallet/store', [WalletsController::class, 'store'])->name('wallet.store');
    Route::post('wallet/update', [WalletsController::class, 'update'])->name('wallet.update');
    Route::get('warehouse/wallets/sync', [WalletsController::class, 'sync'])->name('wallet.sync');
    Route::post('warehouse/wallets/sync', [WalletsController::class, 'syncStore'])->name('wallet.sync.store');
    Route::post('wallet/balance/add', [WalletsController::class, 'addBalance'])->name('wallet.balance.store');
    Route::get('wallet/{id}/trnsactions/show', [WalletsController::class, 'transactions'])->name('wallet.transactions.show');
    Route::post('getWalletBalance', [WalletsController::class, 'getWalletBalance'])->name('getWalletBalance');
    Route::post('wallets/transfer', [WalletsController::class, 'transfer'])->name('wallet.transfer');
    
    // stores 
    Route::get('storesHouse/index', [StoreHouseController::class, 'index'])->name('storesHouse.index');
    Route::get('storesHouse/add', [StoreHouseController::class, 'add'])->name('storesHouse.add');
    Route::post('storesHouse/store', [StoreHouseController::class, 'store'])->name('storesHouse.store');
    
    // stocks
    Route::get('mainStore/stocks/index', [StockController::class, 'index'])->name('stock.index');
    Route::get('mainStore/stock/{id}', [StockController::class, 'show'])->name('stock.show');
    Route::post('stock/transction/filter', [StockController::class, 'transctionFilter'])->name('transction.filter');
    Route::get('getStockProducts', [StockController::class, 'getStockProducts'])->name('getStockProducts');
    Route::post('getStocks', [StockController::class, 'getStocks'])->name('getStocks');

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
    Route::get('products/price/show', [ProductController::class, 'showPrice'])->name('product.Price.show');
    Route::post('product/price/update', [ProductController::class, 'updatePrice'])->name('product.Price.update');
    
    // suppliers
    Route::get('suppliers/list', [SupplierController::class, 'index'])->name('supplier.index');
    Route::get('suppliers/{id}/Account', [SupplierController::class, 'showAccount'])->name('supplier.account.show');
    Route::get('suppliers/{id}/profile', [SupplierController::class, 'profile'])->name('supplier.profile');
    Route::get('supplier/add', [SupplierController::class, 'add'])->name('supplier.add');
    Route::get('supplier/edit/{id}', [SupplierController::class, 'edit'])->name('supplier.edit');
    Route::post('supplier/update', [SupplierController::class, 'update'])->name('supplier.update');
    Route::post('supplier/store', [SupplierController::class, 'store'])->name('supplier.store');
    Route::get('/suppliers/template', [SupplierController::class, 'downloadTemplate'])->name('download.supplier.Template');
    Route::post('/suppliers/import', [SupplierController::class, 'importSuppliers'])->name('import.supplier');
    Route::post('suppliers/Data/export', [SupplierController::class, 'exportData'])->name('supplier.data.export');
    Route::post('supplier/account/export', [SupplierController::class, 'exportAccount'])->name('supplier.account.export');

    // invoices purchases
    Route::get('suppliers/invoices', [InvoicePurchaseController::class, 'index'])->name('supplier.invoice.index');
    Route::get('suppliers/invoices/add', [InvoicePurchaseController::class, 'add'])->name('supplier.invoice.add');
    Route::get('supplier/{id}/invoice/add', [InvoicePurchaseController::class, 'add'])->name('supplier.target.invoice.add');
    Route::post('suppliers/invoices/store', [InvoicePurchaseController::class, 'store'])->name('supplier.invoice.store');
    Route::get('supplier/invoice/edit/{id}', [InvoicePurchaseController::class, 'edit'])->name('supplier.invoice.edit');
    Route::post('suppliers/invoices/update', [InvoicePurchaseController::class, 'update'])->name('supplier.invoice.update');
    Route::get('supplier/invoice/{code}/show', [InvoicePurchaseController::class, 'show'])->name('supplier.invoice.show');
    Route::post('supplier/invoice/delete', [InvoicePurchaseController::class, 'deleteInv'])->name('supplier.invoice.delete');
    Route::get('supplier/invoice/{id}/download', [InvoicePurchaseController::class, 'download'])->name('supplier.invoice.download');
    Route::post('supplier/invoice/payment', [InvoicePurchaseController::class, 'payment'])->name('supplier.invoice.payment');
    Route::post('supplier/invoice/filter', [InvoicePurchaseController::class, 'filter'])->name('supplier.invoice.filter');
    Route::post('supplier/invoices/filterBy', [InvoicePurchaseController::class, 'filterBySupplier'])->name('filterBySupplier');

    // Invoices Return 
    Route::get('supplier/returned-invoices', [InvoicePurchaseController::class, 'returnedInvoices'])->name('supplier.returned.invoices');
    Route::post('supplier/returned-invoices/filter', [InvoicePurchaseController::class, 'filterReturn'])->name('supplier.returned_invoices.filter');


    // setting 
    Route::get('setting', [SettingController::class, 'setting'])->name('setting.show');
    Route::post('Profile/update', [SettingController::class, 'updateProfile'])->name('setting.update');

    // sizes
    Route::get('sizes/index', [SizeController::class, 'index'])->name('size.index');
    Route::post('sizes/store', [SizeController::class, 'store'])->name('size.store');
    Route::post('sizes/update', [SizeController::class, 'update'])->name('size.update');
    Route::post('sizes/delete', [SizeController::class, 'delete'])->name('size.delete');
    Route::get('getSizes', [SizeController::class, 'getSizes'])->name('getSizes');

    // Expenses
    Route::get('Expenses/items', [ExponseItemController::class, 'index'])->name('expenses.items');
    Route::get('Expenses/item/add', [ExponseItemController::class, 'add'])->name('expenses.item.add');
    Route::get('Expenses/item/{id}/edit', [ExponseItemController::class, 'edit'])->name('expenses.item.edit');
    Route::post('Expenses/item/store', [ExponseItemController::class, 'store'])->name('expenses.item.store');
    Route::post('Expenses/item/update', [ExponseItemController::class, 'update'])->name('expenses.item.update');
    Route::get('Expenses/item/{id}/show', [ExponseItemController::class, 'show'])->name('expenses.item.show');
    Route::post('Expenses/item/payment', [ExponseItemController::class, 'payment'])->name('expenses.item.payment');

    // external debts
    Route::get('external/debts/show', [ExternalDebtsController::class, 'index'])->name('external.debts');
    
    // dues
    Route::get('dues/show', [DueController::class, 'index'])->name('dues.debts');

    // customers
    Route::get('customers/index', [CustomerController::class, 'index'])->name('customer.index');
    Route::get('customer/add', [CustomerController::class, 'add'])->name('customer.add');
    Route::post('customer/store', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('customer/edit/{id}', [CustomerController::class, 'edit'])->name('customer.edit');
    Route::post('customer/update', [CustomerController::class, 'update'])->name('customer.update');
    Route::get('customer/account/show/{id}', [CustomerController::class, 'showAccount'])->name('customer.account.show');
    Route::post('customer/account/export', [CustomerController::class, 'exportAccount'])->name('customer.account.export');
    Route::get('customer/template', [CustomerController::class, 'downloadTemplate'])->name('download.customer.Template');
    Route::post('customer/import', [CustomerController::class, 'importData'])->name('import.customer');
   
    // customer invoices
    Route::post('customer/payment', [SalesController::class, 'payment'])->name('customer.payment');
    Route::get('customer/invoices', [SalesController::class, 'index'])->name('customer.invoice.index');
    Route::get('customer/{id}/invoice/add', [SalesController::class, 'add'])->name('customer.target.invoice.add');
    Route::get('customer/invoice/add', [SalesController::class, 'add'])->name('customer.invoice.add');
    Route::post('customer/invoice/store', [SalesController::class, 'store'])->name('customer.invoice.store');
    Route::get('customer/invoice/edit/{id}', [SalesController::class, 'edit'])->name('customer.invoice.edit');
    Route::post('customer/invoice/update', [SalesController::class, 'update'])->name('customer.invoice.update');
    Route::post('customer/invoice/filter', [SalesController::class, 'filter'])->name('customer.invoice.filter');
    Route::post('customer/invoices/filterBy', [SalesController::class, 'filterByCustomer'])->name('filterByCustomer');
    Route::get('customer/invoice/{code}/show', [SalesController::class, 'show'])->name('customer.invoice.show');
    Route::post('customer/invoice/delete', [SalesController::class, 'deleteInv'])->name('customer.invoice.delete');
    Route::get('customer/returned-invoices', [SalesController::class, 'returnedInvoices'])->name('customer.returned.invoices');
    Route::post('customer/returned-invoices/filter', [SalesController::class, 'filterReturn'])->name('customer.returned_invoices.filter');
    Route::get('customer/invoice/{id}/download', [SalesController::class, 'download'])->name('customer.invoice.download');

    // backup
    Route::get('/backup', fn() => view('backup'))->name('backup.view');
    Route::post('/backup/create', [BackupController::class, 'downloadBackup'])->name('backup.create');
    Route::post('/backup/restore', [BackupController::class, 'restoreBackupFlexible'])->name('backup.restore');
    
});

Route::get('support', function () {
    return view('inactive');
})->name('support')->middleware('redirect.if.active');



require __DIR__.'/auth.php';
