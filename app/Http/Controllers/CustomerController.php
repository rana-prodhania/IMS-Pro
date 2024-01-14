<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\PaymentDetail;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::latest()->get();
        return view('admin.customer.customer_all', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.customer.customer_add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $image = $request->file('customer_image');
        $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
        $manager = new ImageManager(new Driver());
        $image = $manager->read($image);
        $image->scale(width: 200, height: 200)->save('upload/customer/' . $name_gen);
        $save_url = 'upload/customer/' . $name_gen;

        $customer = new Customer();
        $customer->name = $request->name;
        $customer->mobile_no = $request->mobile_no;
        $customer->email = $request->email;
        $customer->address = $request->address;
        $customer->customer_image = $save_url;
        $customer->save();


        $notification = array(
            'message' => 'Customer Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('customer.index')->with($notification);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customer.customer_edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->name = $request->name;
        $customer->mobile_no = $request->mobile_no;
        $customer->email = $request->email;
        $customer->address = $request->address;
        
        if ($request->file('customer_image')) {
            $image = $request->file('customer_image');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            $manager = new ImageManager(new Driver());
            $image = $manager->read($image);
            $image->scale(width: 200, height: 200)->save('upload/customer/' . $name_gen);
            $save_url = 'upload/customer/' . $name_gen;
            $customer->customer_image = $save_url;
        }

        $customer->save();

        $notification = array(
            'message' => 'Customer Updated Successfully', 
            'alert-type' => 'success'
        );

        return redirect()->route('customer.index')->with($notification);

        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        $img = $customer->customer_image;
        unlink($img);
        $customer->delete();

        $notification = array(
            'message' => 'Customer Deleted Successfully', 
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    // Credit Customer
    public function CreditCustomer(){

        $allData = Payment::whereIn('paid_status',['full_due','partial_paid'])->get();
        return view('admin.customer.customer_credit',compact('allData'));

    }

    // Credit Customer Report
    public function CreditCustomerPrintPdf(){

        $allData = Payment::whereIn('paid_status',['full_due','partial_paid'])->get();
        return view('admin.pdf.customer_credit_pdf',compact('allData'));

    }


    // Customer Edit Invoice
    public function CustomerEditInvoice($invoice_id){

        $payment = Payment::where('invoice_id',$invoice_id)->first();
        return view('admin.customer.edit_customer_invoice',compact('payment'));

    }

    // Customer Update Invoice
    public function CustomerUpdateInvoice(Request $request,$invoice_id){

        if ($request->new_paid_amount < $request->paid_amount) {

            $notification = array(
            'message' => 'Sorry You Paid Maximum Value', 
            'alert-type' => 'error'
        );
        return redirect()->back()->with($notification); 
        } else{
            $payment = Payment::where('invoice_id',$invoice_id)->first();
            $payment_details = new PaymentDetail();
            $payment->paid_status = $request->paid_status;

            if ($request->paid_status == 'full_paid') {
                 $payment->paid_amount = Payment::where('invoice_id',$invoice_id)->first()['paid_amount']+$request->new_paid_amount;
                 $payment->due_amount = '0';
                 $payment_details->current_paid_amount = $request->new_paid_amount;

            } elseif ($request->paid_status == 'partial_paid') {
                $payment->paid_amount = Payment::where('invoice_id',$invoice_id)->first()['paid_amount']+$request->paid_amount;
                $payment->due_amount = Payment::where('invoice_id',$invoice_id)->first()['due_amount']-$request->paid_amount;
                $payment_details->current_paid_amount = $request->paid_amount;

            }

            $payment->save();
            $payment_details->invoice_id = $invoice_id;
            $payment_details->date = date('Y-m-d',strtotime($request->date));
            $payment_details->save();

              $notification = array(
            'message' => 'Invoice Update Successfully', 
            'alert-type' => 'success'
        );
        return redirect()->route('credit.customer')->with($notification); 


        }

    }

    // Customer Invoice Details
    public function CustomerInvoiceDetails($invoice_id){

        $payment = Payment::where('invoice_id',$invoice_id)->first();
        return view('admin.pdf.invoice_details_pdf',compact('payment'));

    }

    // Paid Customer List
    public function PaidCustomer(){
        $allData = Payment::where('paid_status','!=','full_due')->get();
        return view('admin.customer.customer_paid',compact('allData'));
    }

    // Paid Customer
    public function PaidCustomerPrintPdf(){

        $allData = Payment::where('paid_status','!=','full_due')->get();
        return view('admin.pdf.customer_paid_pdf',compact('allData'));
    }

 
    // Customer Wise Report List
    public function CustomerWiseReport(){

        $customers = Customer::all();
        return view('admin.customer.customer_wise_report',compact('customers'));

    }

    // Customer Wise Report
    public function CustomerWiseCreditReport(Request $request){

         $allData = Payment::where('customer_id',$request->customer_id)->whereIn('paid_status',['full_due','partial_paid'])->get();
        return view('admin.pdf.customer_wise_credit_pdf',compact('allData'));
    }

    //Customer Wise Paid Report
    public function CustomerWisePaidReport(Request $request){

        $allData = Payment::where('customer_id',$request->customer_id)->where('paid_status','!=','full_due')->get();
       return view('admin.pdf.customer_wise_paid_pdf',compact('allData'));
   }
}
