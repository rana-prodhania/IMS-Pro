<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\InvoiceDetail;
use App\Models\PaymentDetail;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allData = Invoice::with(['payment.customer'])->get();
        return view('admin.invoice.invoice_all', compact('allData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $category = Category::all();
        $customer = Customer::all();
        $invoice_no = Invoice::orderBy('id', 'desc')->value('invoice_no') + 1;
        $date = date('Y-m-d');
        return view('admin.invoice.invoice_add', compact('invoice_no', 'category', 'date', 'customer'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->category_id == null) {
            return redirect()->back()->with([
                'message' => 'Sorry you did not select any item',
                'alert-type' => 'error'
            ]);
        }

        if ($request->paid_amount > $request->estimated_amount) {
            return redirect()->back()->with([
                'message' => 'Sorry Paid Amount is Maximum the total price',
                'alert-type' => 'error'
            ]);
        }

        $invoice = new Invoice();
        $invoice->invoice_no = $request->invoice_no;
        $invoice->date = date('Y-m-d', strtotime($request->date));
        $invoice->description = $request->description;
        $invoice->status = '0';

        DB::transaction(function () use ($request, $invoice) {
            if ($invoice->save()) {
                $count_category = count($request->category_id);
                for ($i = 0; $i < $count_category; $i++) {
                    $invoice_details = new InvoiceDetail();
                    $invoice_details->date = date('Y-m-d', strtotime($request->date));
                    $invoice_details->invoice_id = $invoice->id;
                    $invoice_details->category_id = $request->category_id[$i];
                    $invoice_details->product_id = $request->product_id[$i];
                    $invoice_details->selling_qty = $request->selling_qty[$i];
                    $invoice_details->unit_price = $request->unit_price[$i];
                    $invoice_details->selling_price = $request->selling_price[$i];
                    $invoice_details->status = '0';
                    $invoice_details->save();
                }

                if ($request->customer_id == '0') {
                    $customer = new Customer();
                    $customer->name = $request->name;
                    $customer->mobile_no = $request->mobile_no;
                    $customer->email = $request->email;
                    $customer->save();
                    $customer_id = $customer->id;
                } else {
                    $customer_id = $request->customer_id;
                }

                $payment = new Payment();
                $payment_details = new PaymentDetail();

                $payment->invoice_id = $invoice->id;
                $payment->customer_id = $customer_id;
                $payment->paid_status = $request->paid_status;
                $payment->discount_amount = $request->discount_amount;
                $payment->total_amount = $request->estimated_amount;

                if ($request->paid_status == 'full_paid') {
                    $payment->paid_amount = $request->estimated_amount;
                    $payment->due_amount = '0';
                    $payment_details->current_paid_amount = $request->estimated_amount;
                } elseif ($request->paid_status == 'full_due') {
                    $payment->paid_amount = '0';
                    $payment->due_amount = $request->estimated_amount;
                    $payment_details->current_paid_amount = '0';
                } elseif ($request->paid_status == 'partial_paid') {
                    $payment->paid_amount = $request->paid_amount;
                    $payment->due_amount = $request->estimated_amount - $request->paid_amount;
                    $payment_details->current_paid_amount = $request->paid_amount;
                }
                $payment->save();

                $payment_details->invoice_id = $invoice->id;
                $payment_details->date = date('Y-m-d', strtotime($request->date));
                $payment_details->save();
            }
        });

        $notification = array(
            'message' => 'Invoice Data Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('invoice.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();
        InvoiceDetail::where('invoice_id', $invoice->id)->delete();
        Payment::where('invoice_id', $invoice->id)->delete();
        PaymentDetail::where('invoice_id', $invoice->id)->delete();

        $notification = array(
            'message' => 'Invoice Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

    // Invoice Approve
    public function approve($id)
    {
        $invoice = Invoice::with('invoice_details')->findOrFail($id);
        return view('admin.invoice.invoice_approve', compact('invoice'));
    }

    // Invoice Approval Store
    public function approvalStore(Request $request, $id)
    {
        foreach ($request->selling_qty as $key => $val) {
            $invoice_details = InvoiceDetail::where('id', $key)->first();
            $product = Product::where('id', $invoice_details->product_id)->first();
            if ($product->quantity < $request->selling_qty[$key]) {

                $notification = array(
                    'message' => 'Sorry you approve Maximum Value',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            }
        } // End foreach 

        $invoice = Invoice::findOrFail($id);
        $invoice->status = '1';

        DB::transaction(function () use ($request, $invoice, $id) {
            foreach ($request->selling_qty as $key => $val) {
                $invoice_details = InvoiceDetail::where('id', $key)->first();

                $invoice_details->status = '1';
                $invoice_details->save();

                $product = Product::where('id', $invoice_details->product_id)->first();
                $product->quantity = ((float)$product->quantity) - ((float)$request->selling_qty[$key]);
                $product->save();
            } // end foreach

            $invoice->save();
        });

        $notification = array(
            'message' => 'Invoice Approve Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('invoice.index')->with($notification);
    }

    // Invoice Print List
    public function printInvoiceList()
    {

        $allData = Invoice::orderBy('date', 'desc')->orderBy('id', 'desc')->where('status', '1')->get();
        return view('admin.invoice.print_invoice_list', compact('allData'));
    }

    // Invoice Print
    public function printInvoice($id)
    {
        $invoice = Invoice::with('invoice_details')->findOrFail($id);
        return view('admin.pdf.invoice_pdf', compact('invoice'));
    }
}
