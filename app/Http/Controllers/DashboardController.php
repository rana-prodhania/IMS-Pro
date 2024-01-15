<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\InvoiceDetail;

class DashboardController extends Controller
{

    public function index()
    {
        $data = [
            'supplier' => Supplier::count(),
            'customer' => Customer::count(),
            'product' => Product::count(),
            'category' => Category::count(),
            'totalPurchase' => Purchase::sum('buying_price'),
            'totalSales' => InvoiceDetail::sum('selling_price'),
        ];

        return view('admin.dashboard', $data);
    }
}
