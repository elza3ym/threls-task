<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Place Order and empty cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function placeOrder(Request $request): JsonResponse
    {
       $cartItems = $request->session()->get('cart');

       if (!$cartItems) {
           return response()->json([
               'message' => 'Please add some items to your cart first!',
               'code' => 400
           ], 400);
       }

       $order = new Order();
       $order->save();
       $total = 0;
       foreach ($cartItems as $cartItem) {
           $product = Product::findOrFail($cartItem['product_id']);
           $order->products()->attach($product->id, ['product_id' => $product->id, 'amount' => $cartItem["amount"]]);
           $total += ( $product->price * $cartItem['amount'] );
       }
       $order->total = $total;
       $order->save();

       $request->session()->flush();

        return response()->json([
            'message' => 'Order Placed Successfully!',
        ], 200);
    }

}
