<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipment;
use App\Models\User;
use App\Models\WholesaleStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;


// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->noContent();
    });
});

// Public routes
Route::post('login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => 'البيانات غير صحيحة'
        ]);
    }
    $token = $user->createToken($request->device_name);

    return response()->json([
        'token' => $token->plainTextToken
    ]);
});

Route::get('wholesale-stores', function () {
    return response()->json(WholesaleStore::all());
});


Route::get('/wholesale-stores/{id}/products', function ($id) {
    $store = WholesaleStore::find($id);
    return response()->json($store->products);
});
Route::post('/trader/orders', function (Request $request) {
    // Validate the request
    $request->validate([
        'trader_id' => 'required|exists:traders,id',
        'orders' => 'required|array',
        'orders.*.wholesale_store_id' => 'required|exists:wholesale_stores,id',
        'orders.*.products' => 'required|array',
        'orders.*.products.*.product_id' => 'required|exists:products,id',
        'orders.*.products.*.quantity' => 'required|integer|min:1',
        'orders.*.products.*.price' => 'required|numeric|min:0'
    ]);

    DB::beginTransaction();
    try {
        $orders = [];
        foreach ($request->orders as $orderData) {
            // Create order
            $order = Order::create([
                'date' => now(),
                'trader_id' => $request->trader_id,
                'wholesale_store_id' => $orderData['wholesale_store_id'],
                'total_amount' => collect($orderData['products'])->sum(function($product) {
                    return $product['price'] * $product['quantity'];
                })
            ]);

            // Create order items
            foreach ($orderData['products'] as $product) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'unit_price' => $product['price']
                ]);
            }

            $orders[] = $order;
        }

        // Create shipment
        $shipment = Shipment::create([
            'date' => now(),
            'trader_id' => $request->trader_id,
            'total_amount' => collect($orders)->sum('total_amount')
        ]);

        // Attach orders to shipment
        $shipment->orders()->attach(collect($orders)->pluck('id'));

        DB::commit();

        return response()->json([
            'message' => 'Order created successfully',
            'shipment_id' => $shipment->id
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => $e->getMessage(),
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('/trader/orders', function (Request $request) {
    $trader = \App\Models\Trader::find(1);

    $orders = Order::with(['wholesaleStore', 'items.product'])
        ->where('trader_id', $trader->id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($order) {
            return [
                'id' => $order->id,
                'date' => $order->created_at->format('Y-m-d H:i'),
                'store' => [
                    'id' => $order->wholesaleStore->id,
                    'name' => $order->wholesaleStore->name,
                ],
                'total_amount' => $order->total_amount,
                'state' => $order->state,
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'price' => $item->unit_price,
                    ];
                })
            ];
        });

    return response()->json($orders);
});
