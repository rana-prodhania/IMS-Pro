<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Product;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $allData = Purchase::with('category', 'supplier', 'product')->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        return view('admin.purchase.purchase_all', compact('allData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $supplier = Supplier::all();
        $unit = Unit::all();
        $category = Category::all();
        return view('admin.purchase.purchase_add', compact('supplier', 'unit', 'category'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (empty($request->category_id)) {
            $notification = [
                'message' => 'Please select at least one product',
                'alert-type' => 'error',
            ];

            return redirect()->back()->with($notification);
        }

        foreach ($request->category_id as $index => $categoryId) {
            $purchase = new Purchase();
            $purchase->date = date('Y-m-d', strtotime($request->date[$index]));
            $purchase->purchase_no = $request->purchase_no[$index];
            $purchase->supplier_id = $request->supplier_id[$index];
            $purchase->category_id = $categoryId;
            $purchase->product_id = $request->product_id[$index];
            $purchase->buying_qty = $request->buying_qty[$index];
            $purchase->unit_price = $request->unit_price[$index];
            $purchase->buying_price = $request->buying_price[$index];
            $purchase->description = $request->description[$index];
            $purchase->status = '0';
            $purchase->save();
        }

        $notification = [
            'message' => 'Purchase Added Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->route('purchase.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $purchase = Purchase::find($id);
        $purchase->delete();
        $notification = [
            'message' => 'Purchase Deleted Successfully',
            'alert-type' => 'success',
        ];
        return redirect()->back()->with($notification);
    }

    // Approved Purchase
    public function approve($id)
    {
        
        $purchase = Purchase::findOrFail($id);
        $product = Product::findOrFail($purchase->product_id);

        $purchase_qty = ((float)($purchase->buying_qty)) + ((float)($product->quantity));

        $product->quantity = $purchase_qty;
        $purchase->status = '1';
        $product->save();

        $purchase->update([
            'status' => '1',
        ]);

        $notification = array(
            'message' => 'Status Approved Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('purchase.pending')->with($notification);

    }


}
