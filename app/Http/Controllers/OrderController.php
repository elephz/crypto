<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderCollection;
use App\Models\Order;
use App\Models\SalesOrder;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        try {
            // Fetch sales orders from the database
            $salesOrders = Order::with([
                "salesOrder" => function ($query) {
                    $query->with([
                        'user' => function ($query) {
                            $query->select('id', 'name');
                        },
                    ]);
                },
                'currency' => function ($query) {
                    $query->select('id', 'symbol', 'name');
                },
                'user' => function ($query) {
                    $query->select('id', 'name');
                },
            ])->paginate(10);

            return new OrderCollection($salesOrders);
        } catch (\Exception $e) {
            // Handle any exceptions that may occur
            return response()->json([
                'error' => 'An error occurred while fetching sales orders.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
