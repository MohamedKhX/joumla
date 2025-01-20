<?php

use App\Enums\UserTypeEnum;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipment;
use App\Models\Trader;
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
    $wholesaleStores = WholesaleStore::all()->map(function ($store) {
        return [
            'id' => $store->id,
            'name' => $store->name,
            'logo' => $store->getFirstMediaUrl('logo'), // Assuming 'logos' is the collection name
        ];
    });

    return response()->json($wholesaleStores);
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

        foreach ($orders as $order) {
            $order->update(['shipment_id' => $shipment->id]);
        }

        DB::commit();

        return response()->json([
            'message' => 'Order created successfully',
            'shipment_id' => $shipment->id
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => $e->getMessage() . ' Message',
            'error' => $e->getMessage() . ' Error'
        ], 500);
    }
});

Route::get('/trader/{id}/orders', function (Request $request) {
    $trader = Trader::where('user_id', $request->id)->first();

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
                'shipmentState' => $order->shipment->state ?? 'قيد الشحن',
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

Route::get('/trader/notifications', function (Request $request) {
    $user = $request->user();
    $user = User::find(1);

    $notifications = $user->notifications->map(function ($item) {
        return [
            'id' => $item->id,
            'title' => $item->data['title'],
            'body' => $item->data['body'] ?? '',
            'created_at' => $item->created_at->format('Y-m-d H:i'),
        ];
    });

    return $notifications;
});

Route::post('/new/trader', function (Request $request) {
    // Validate the request
    $request->validate([
        'email' => 'required|email|unique:users,email',
        'password' => 'required',
        'trader_name' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'phone_number' => 'required|string|max:20',
        'traderTypeId' => 'required|exists:trader_types,id',
        'location_latitude' => 'required|numeric',
        'location_longitude' => 'required|numeric',
    ]);

    DB::beginTransaction();
    try {
        // Create the user
        $user = User::create([
            'name' => $request->trader_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => UserTypeEnum::Trader
        ]);

        // Create the trader
        $trader = $user->trader()->create([
            'store_name' => $request->trader_name,
            'city' => $request->city,
            'address' => $request->address,
            'phone' => $request->phone_number,
            'trader_type_id' => $request->traderTypeId,
            'location_latitude' => $request->location_latitude,
            'location_longitude' => $request->location_longitude,
        ]);

        // Handle file uploads
        if ($request->hasFile('logo')) {
            $trader->addMedia($request->file('logo'))->toMediaCollection('logo');
        }

        if ($request->hasFile('license')) {
            $trader->addMedia($request->file('license'))->toMediaCollection('license');
        }

        DB::commit();

        return response()->json([
            'message' => 'Trader and user created successfully',
            'user' => $user,
            'trader' => $trader,
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Failed to create trader and user',
            'error' => $e->getMessage(),
        ], 500);
    }
});

Route::post('/new/driver', function (Request $request) {
    // Validate the request
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone' => 'required|string|max:20',
        'password' => 'required|string|min:8|confirmed',
        'car_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'target_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'license_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    DB::beginTransaction();
    try {
        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'type' => UserTypeEnum::Driver,
        ]);

        // Handle file uploads
        if ($request->hasFile('car_image')) {
            $user->addMedia($request->file('car_image'))->toMediaCollection('car_image');
        }

        if ($request->hasFile('target_image')) {
            $user->addMedia($request->file('target_image'))->toMediaCollection('target_image');
        }

        if ($request->hasFile('license_image')) {
            $user->addMedia($request->file('license_image'))->toMediaCollection('license_image');
        }

        DB::commit();

        return response()->json([
            'message' => 'Driver created successfully',
            'user' => $user,
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Failed to create driver',
            'error' => $e->getMessage(),
        ], 500);
    }
});
