<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function GetCategory(Request $request){

        $supplier_id = $request->supplier_id;
        $allCategory = Product::with(['category'])->select('category_id')->where('supplier_id',$supplier_id)->groupBy('category_id')->get();
        return response()->json($allCategory);
    } // End Method 

     public function GetProduct(Request $request){

        $category_id = $request->category_id; 
        $allProduct = Product::where('category_id',$category_id)->get();
        return response()->json($allProduct);
    } // End Method 
}
