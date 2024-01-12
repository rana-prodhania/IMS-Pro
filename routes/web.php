<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

// Admin All Route
Route::middleware('auth')->group(function () {
    Route::controller(AdminController::class)->group(function () {
        Route::get('/admin/logout', 'destroy')->name('admin.logout');
        Route::get('/admin/profile', 'Profile')->name('admin.profile');
        Route::get('/edit/profile', 'EditProfile')->name('edit.profile');
        Route::post('/store/profile', 'StoreProfile')->name('store.profile');
        Route::get('/change/password', 'ChangePassword')->name('change.password');
        Route::post('/update/password', 'UpdatePassword')->name('update.password');
    });
    // Supplier All Route
    Route::resource('/supplier', SupplierController::class, ['except' => ['show']]);
    // Customer All Route
    Route::resource('/customer', CustomerController::class, ['except' => ['show']]);
    // Unit All Route
    Route::resource('/unit', UnitController::class, ['except' => ['show']]);
    // Category All Route
    Route::resource('/category', CategoryController::class, ['except' => ['show']]);
    // Product All Route
    Route::resource('/product', ProductController::class, ['except' => ['show']]);
    // Purchase All Route
    Route::get('/purchase/approve/{id}', [PurchaseController::class, 'approve'])->name('purchase.approve');
    Route::resource('/purchase', PurchaseController::class, ['except' => ['show,edit,update']]);
    // Invoice All Route
    Route::get('/invoice/approve/{id}', [InvoiceController::class, 'approve'])->name('invoice.approve');
    Route::post('/approval/store/{id}', [InvoiceController::class, 'approvalStore'])->name('approval.store');
    Route::get('/print/invoice/list', [InvoiceController::class, 'printInvoiceList'])->name('print.invoice.list');
    Route::get('/print/invoice/{id}', [InvoiceController::class, 'printInvoice'])->name('print.invoice');
    Route::get('/daily/invoice/report', [InvoiceController::class, 'dailyInvoiceReport'])->name('daily.invoice.report');
    Route::get('/daily/invoice/pdf', [InvoiceController::class, 'dailyInvoicePdf'])->name('daily.invoice.pdf');
    Route::resource('/invoice', InvoiceController::class, ['except' => ['show,edit,update']]);

    // Stock All Route 
    Route::controller(StockController::class)->group(function () {
        Route::get('/stock/report', 'StockReport')->name('stock.report');
        Route::get('/stock/report/pdf', 'StockReportPdf')->name('stock.report.pdf');

        Route::get('/stock/supplier/wise', 'StockSupplierWise')->name('stock.supplier.wise');
        Route::get('/supplier/wise/pdf', 'SupplierWisePdf')->name('supplier.wise.pdf');
        Route::get('/product/wise/pdf', 'ProductWisePdf')->name('product.wise.pdf');
    });

    // Ajax All Route
    Route::controller(AjaxController::class)->group(function () {
        Route::get('/get-category', 'GetCategory')->name('get-category');
        Route::get('/get-product', 'GetProduct')->name('get-product');
        Route::get('/check-product', 'GetStock')->name('check-product-stock');
    });
});


require __DIR__ . '/auth.php';
