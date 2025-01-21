<?php

use App\Enums\ShipmentStateEnum;
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
                }),
                'is_deferred' => $orderData['deferred'] ?? false,
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
                'shipmentState' => $order?->shipment?->state,
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

Route::get('/user/{id}/notifications', function (Request $request) {
    $user = User::find($request->id);


    return $user->notifications->map(function ($item) {
        if(isset($item->data['type'])) {
            return $item;
        }

        return [
            'id' => $item->id,
            'title' => $item->data['title'],
            'body' => $item->data['body'] ?? '',
            'created_at' => $item->created_at->format('Y-m-d H:i'),
        ];
    });
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
// Add these routes to your existing routes file

// Get all available shipments for drivers
Route::get('/shipments/available', function () {
    return Shipment::where('state', ShipmentStateEnum::WaitingForShipping)
        ->where('driver_id', null)
        ->with(['orders.wholesaleStore', 'orders.items.product', 'trader'])
        ->get()
        ->map(function ($shipment) {
            return [
                'id' => $shipment->id,
                'date' => $shipment->created_at->format('Y-m-d H:i'),
                'total_amount' => $shipment->total_amount,
                'trader' => [
                    'name' => $shipment->trader->store_name,
                    'address' => $shipment->trader->address,
                    'phone' => $shipment->trader->phone,
                    'location' => [
                        'latitude' => $shipment->trader->location_latitude,
                        'longitude' => $shipment->trader->location_longitude,
                    ],
                ],
                'orders' => $shipment->orders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'store_name' => $order->wholesaleStore->name,
                        'items' => $order->items->map(function ($item) {
                            return [
                                'product_name' => $item->product->name,
                                'quantity' => $item->quantity,
                            ];
                        }),
                    ];
                }),
            ];
        });
});

// Get driver's current shipments
Route::get('/driver/{id}/shipments', function (Request $request) {
    return Shipment::where('driver_id', $request->id)
        ->with(['orders.wholesaleStore', 'orders.items.product', 'trader'])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($shipment) {
            return [
                'id' => $shipment->id,
                'date' => $shipment->created_at->format('Y-m-d H:i'),
                'state' => $shipment->state,
                'total_amount' => $shipment->total_amount,
                'trader' => [
                    'name' => $shipment->trader->store_name,
                    'address' => $shipment->trader->address,
                    'phone' => $shipment->trader->phone,
                    'location' => [
                        'latitude' => $shipment->trader->location_latitude,
                        'longitude' => $shipment->trader->location_longitude,
                    ],
                ],
                'orders' => $shipment->orders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'store_name' => $order->wholesaleStore->name,
                        'items' => $order->items->map(function ($item) {
                            return [
                                'product_name' => $item->product->name,
                                'quantity' => $item->quantity,
                            ];
                        }),
                    ];
                }),
            ];
        });
});

// Accept a shipment
Route::post('/shipments/{id}/{driverId}/accept', function (Request $request) {
    $shipment = Shipment::findOrFail($request->id);

    if ($shipment->driver_id) {
        return response()->json(['message' => 'This shipment is already assigned to another driver'], 400);
    }

    $shipment->update([
        'driver_id' => $request->driverId,
        'state' => ShipmentStateEnum::WaitingForReceiving,
    ]);

    return response()->json(['message' => 'Shipment accepted successfully']);
});

// Receive a shipment
Route::post('/shipments/{id}/Received', function (Request $request) {
    $shipment = Shipment::findOrFail($request->id);

    $shipment->update(['state' => ShipmentStateEnum::Received]);

    return response()->json(['message' => 'Shipment Received successfully']);
});


// Receive a shipment
Route::post('/shipments/{id}/Shipping', function (Request $request, $id) {
    $shipment = Shipment::findOrFail($request->id);

    $shipment->update(['state' => ShipmentStateEnum::Shipping]);

    return response()->json(['message' => 'Shipment Shipping successfully']);
});

Route::post('/shipments/{id}/Shipped', function (Request $request, $id) {
    $shipment = Shipment::findOrFail($request->id);

    $shipment->update(['state' => ShipmentStateEnum::Shipped]);

    return response()->json(['message' => 'Shipment Shipped successfully']);
});

// Cancel a shipment
Route::post('/shipments/{id}/cancel', function (Request $request, $id) {
    $shipment = Shipment::findOrFail($id);

    $shipment->update([
        'state' => ShipmentStateEnum::WaitingForShipping,
        'driver_id' => null,
    ]);

    return response()->json(['message' => 'Shipment canceled successfully']);
});

// Proceed with approved orders
Route::post('/shipments/{id}/proceed-with-approved', function (Request $request) {
    $shipment = Shipment::findOrFail($request->id);

    // Delete the notification(s) related to this shipment in the custom table
  /*  DB::table('notifications')  // Adjust the table name if it's different
    ->whereJsonContains('data->shipment_id', $request->id) // Querying the JSON field for shipment_id
    ->delete();*/
    // Delete rejected orders
    $shipment->orders()->where('state', \App\Enums\OrderStateEnum::Rejected)->delete();

    // Update shipment total amount
    $shipment->update([
        'total_amount' => $shipment->orders()->sum('total_amount'),
        'state' => ShipmentStateEnum::WaitingForShipping
    ]);



    return response()->json([
        'message' => 'تم تحديث الشحنة بنجاح'
    ]);
});

// Cancel all orders
Route::post('/shipments/{id}/cancel-all', function (Request $request) {
    $shipment = Shipment::findOrFail($request->id);

    // Delete the notification(s) related to this shipment in the custom table
    DB::table('notifications')  // Adjust the table name if it's different
    ->whereJsonContains('data->shipment_id', $request->id) // Querying the JSON field for shipment_id
    ->delete();
    // Delete all orders and the shipment
    $shipment->orders()->delete();
    $shipment->delete();


    return response()->json([
        'message' => 'تم إلغاء جميع الطلبات بنجاح'
    ]);
});
