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
use App\Http\Controllers\DashboardController;

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

// Route::get('/dashboard', function () {
//     return view('admin.dashboard');
// })->middleware(['auth'])->name('dashboard');

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
    // Dashboard All Route
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
    });

    // Supplier All Route
    Route::resource('/supplier', SupplierController::class, ['except' => ['show']]);

    // Customer All Route
    Route::controller(CustomerController::class)->group(function () {
        Route::get('/credit/customer', 'CreditCustomer')->name('credit.customer');
        Route::get('/credit/customer/print/pdf', 'CreditCustomerPrintPdf')->name('credit.customer.print.pdf');

        Route::get('/customer/edit/invoice/{invoice_id}', 'CustomerEditInvoice')->name('customer.edit.invoice');
        Route::post('/customer/update/invoice/{invoice_id}', 'CustomerUpdateInvoice')->name('customer.update.invoice');

        Route::get('/customer/invoice/details/{invoice_id}', 'CustomerInvoiceDetails')->name('customer.invoice.details.pdf');

        Route::get('/paid/customer', 'PaidCustomer')->name('paid.customer');
        Route::get('/paid/customer/print/pdf', 'PaidCustomerPrintPdf')->name('paid.customer.print.pdf');

        Route::get('/customer/wise/report', 'CustomerWiseReport')->name('customer.wise.report');
        Route::get('/customer/wise/credit/report', 'CustomerWiseCreditReport')->name('customer.wise.credit.report');
        Route::get('/customer/wise/paid/report', 'CustomerWisePaidReport')->name('customer.wise.paid.report');
        Route::resource('/customer', CustomerController::class, ['except' => ['show']]);
    });


    // Unit All Route
    Route::resource('/unit', UnitController::class, ['except' => ['show']]);

    // Category All Route
    Route::resource('/category', CategoryController::class, ['except' => ['show']]);

    // Product All Route
    Route::resource('/product', ProductController::class, ['except' => ['show']]);

    // Purchase All Route
    Route::controller(PurchaseController::class)->group(function () {
        Route::get('/purchase/approve/{id}', 'approve')->name('purchase.approve');
        Route::get('/daily/purchase/report', 'dailyPurchaseReport')->name('daily.purchase.report');
        Route::get('/daily/purchase/pdf', 'dailyPurchasePdf')->name('daily.purchase.pdf');
        Route::resource('/purchase', PurchaseController::class, ['except' => ['show,edit,update']]);
    });

    // Invoice All Route
    Route::controller(InvoiceController::class)->group(function () {
        Route::get('/invoice/approve/{id}',  'approve')->name('invoice.approve');
        Route::post('/approval/store/{id}',  'approvalStore')->name('approval.store');
        Route::get('/print/invoice/list', 'printInvoiceList')->name('print.invoice.list');
        Route::get('/print/invoice/{id}', 'printInvoice')->name('print.invoice');
        Route::get('/daily/invoice/report', 'dailyInvoiceReport')->name('daily.invoice.report');
        Route::get('/daily/invoice/pdf', 'dailyInvoicePdf')->name('daily.invoice.pdf');
        Route::resource('/invoice', InvoiceController::class, ['except' => ['show,edit,update']]);
    });

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
