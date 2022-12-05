<?php

namespace App\Http\Controllers;

use App\Imports\ProductsImport;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::paginate(10);
    }

    /**
     * Import CSV file into Product Model
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|mimes:csv'
        ]);

        Excel::import(new ProductsImport(), $request->file, null, \Maatwebsite\Excel\Excel::CSV);
        return response()->json([
            'message' => 'Products Imported Successfully!',
        ], 200);
    }

    /**
     * Add Product into virtual cart using session
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCart(Request $request): JsonResponse
    {
        $request->validate([
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.amount' => 'required|integer',
        ]);

        session(['cart' => $request->items]);
        return response()->json([
            'message' => 'Cart Updated Successfully!',
        ], 200);
    }
}
