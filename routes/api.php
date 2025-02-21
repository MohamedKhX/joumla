<?php

use App\Enums\ShipmentStateEnum;
use App\Enums\UserTypeEnum;
use App\Models\Area;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipment;
use App\Models\Trader;
use App\Models\User;
use App\Models\WholesaleStore;
use App\Notifications\DriverAcceptedOrderNotification;
use App\Notifications\DriverReceivedOrderNotification;
use App\Notifications\DriverShipedOrderNotification;
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

    // Load trader relationship if user is a trader
    if ($user->type === UserTypeEnum::Trader) {
        $user->load('trader');
    }

    $token = $user->createToken($request->device_name);

    return response()->json([
        'token' => $token->plainTextToken,
        'user' => $user
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
            'total_amount' => collect($orders)->sum('totalAmount')
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

    $orders = Order::with(['wholesaleStore', 'shipment', 'items.product'])
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
                }),
                'shipment_area_name' => $order->shipment?->area->name ?? '',
                'shipment_deliver_price' => $order->shipment?->area->price ?? 0,
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
            'is_active' => false
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
                'total_amount' => $shipment->totalAmount,
                'shipment_area_name' => $shipment->area->name ?? '',
                'shipment_deliver_price' => $shipment->area->price ?? 0,
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
                        'location_latitude' => $order->wholesaleStore->location_latitude,
                        'location_longitude' => $order->wholesaleStore->location_longitude,
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
                'total_amount' => $shipment->totalAmount,
                'shipment_area_name' => $shipment->area->name ?? '',
                'shipment_deliver_price' => $shipment->area->price ?? 0,
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

                        'location_latitude' => $order->wholesaleStore->location_latitude,
                        'location_longitude' => $order->wholesaleStore->location_longitude,
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

    $shipment->trader->user->notify(new DriverAcceptedOrderNotification($shipment));

    return response()->json(['message' => 'Shipment accepted successfully']);
});

// Receive a shipment
Route::post('/shipments/{id}/Received', function (Request $request) {
    $shipment = Shipment::findOrFail($request->id);

    $shipment->update(['state' => ShipmentStateEnum::Received]);

    $shipment->trader->user->notify(new DriverReceivedOrderNotification($shipment));

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

    $shipment->trader->user->notify(new DriverShipedOrderNotification($shipment));

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
   /* DB::table('notifications')  // Adjust the table name if it's different
    ->whereJsonContains('data->shipment_id', $request->id) // Querying the JSON field for shipment_id
    ->delete();*/
    // Delete rejected orders
    $shipment->orders()->where('state', \App\Enums\OrderStateEnum::Rejected)->delete();

    // Update shipment total amount
    $shipment->update([
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
  /*  DB::table('notifications')  // Adjust the table name if it's different
    ->whereJsonContains('data->shipment_id', $request->id) // Querying the JSON field for shipment_id
    ->delete();*/
    // Delete all orders and the shipment
    $shipment->orders()->delete();
    $shipment->delete();


    return response()->json([
        'message' => 'تم إلغاء جميع الطلبات بنجاح'
    ]);
});
Route::get('/traders/{id}', function (Request $request, $id) {
    $trader = Trader::with('user')->findOrFail($id);

    return response()->json([
        'id' => $trader->id,
        'store_name' => $trader->store_name,
        'address' => $trader->address ?? '',
        'city' => $trader->city ?? '',
        'phone' => $trader->phone ?? '',
        'location' => [
            'latitude' => $trader->location_latitude ?? 32.8872,
            'longitude' => $trader->location_longitude ?? 13.1913,
        ],
        'logo' => $trader->getFirstMediaUrl('logo'),
        'user' => [
            'name' => $trader->user->name,
            'email' => $trader->user->email,
            'phone' => $trader->user->phone ?? '',
        ]
    ]);
});
// Add this new route for handling logo updates
Route::post('/traders/{id}/update-logo', function (Request $request, $id) {
    $trader = Trader::findOrFail($id);

    if ($request->hasFile('logo')) {
        $trader->clearMediaCollection('logo');
        $trader->addMedia($request->file('logo'))->toMediaCollection('logo');
        return response()->json(['message' => 'تم تحديث الشعار بنجاح']);
    }

    return response()->json(['message' => 'لم يتم تحديث الشعار'], 400);
});

// Update the main trader update route to handle JSON
Route::put('/traders/{id}', function (Request $request, $id) {
    $trader = Trader::findOrFail($id);

    // Validate request
    $request->validate([
        'store_name' => 'required|string|max:100',
        'address' => 'required|string|max:255',
        'city' => 'required|string|max:100',
        'phone' => 'required|string|max:20',
        'location_latitude' => 'required|numeric|between:-90,90',
        'location_longitude' => 'required|numeric|between:-180,180',
        'user.name' => 'required|string|max:100',
        'user.email' => 'required|email|max:100',
    ]);

    DB::beginTransaction();
    try {
        // Update trader
        $trader->update([
            'store_name' => $request->store_name,
            'address' => $request->address,
            'city' => $request->city,
            'phone' => $request->phone,
            'location_latitude' => $request->location_latitude,
            'location_longitude' => $request->location_longitude,
        ]);

        // Update associated user
        $trader->user->update([
            'name' => $request->user['name'],
            'email' => $request->user['email'],
        ]);

        DB::commit();
        return response()->json(['message' => 'تم تحديث البيانات بنجاح']);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'حدث خطأ أثناء تحديث البيانات',
            'error' => $e->getMessage()
        ], 500);
    }
});
// Add these new routes for areas
Route::get('/areas', function () {
    $areas = Area::all()->map(function ($area) {
        return [
            'id' => $area->id,
            'name' => $area->name,
            'price' => $area->price,
        ];
    });

    return response()->json($areas);
});

// Update the order creation endpoint to include area
Route::post('/trader/orders', function (Request $request) {
    // Validate the request
    $request->validate([
        'trader_id' => 'required|exists:traders,id',
        'area_id' => 'required|exists:areas,id',  // Add area validation
        'orders' => 'required|array',
        'orders.*.wholesale_store_id' => 'required|exists:wholesale_stores,id',
        'orders.*.products' => 'required|array',
        'orders.*.products.*.product_id' => 'required|exists:products,id',
        'orders.*.products.*.quantity' => 'required|integer|min:1',
        'orders.*.products.*.price' => 'required|numeric|min:0'
    ]);

    DB::beginTransaction();
    try {
        // Get delivery price
        $area = Area::findOrFail($request->area_id);
        $orders = [];
        $ordersTotal = 0;

        foreach ($request->orders as $orderData) {
            $orderTotal = collect($orderData['products'])->sum(function($product) {
                return $product['price'] * $product['quantity'];
            });

            $ordersTotal += $orderTotal;

            // Create order
            $order = Order::create([
                'date' => now(),
                'trader_id' => $request->trader_id,
                'wholesale_store_id' => $orderData['wholesale_store_id'],
                'total_amount' => $orderTotal,
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

        // Create shipment with delivery price
        $shipment = Shipment::create([
            'date' => now(),
            'trader_id' => $request->trader_id,
            'area_id' => $request->area_id,
            'delivery_price' => $area->price,
            'total_amount' => $ordersTotal + $area->price
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
