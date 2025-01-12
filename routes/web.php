<?php

use App\Models\User;
use App\Models\WholesaleStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

Route::prefix('/api')->group( function () {
    Route::group(["Middleware" => ['auth:sanctum']], function() {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::post('logout', function (Request $request) {
            $request->user()->currentAccessToken()->delete();
            return response()->noContent();
        });
    });

    Route::post('login', function (Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if(! $user  || !Hash::check($request->password, $user->password)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => 'البيانات غير صحيحة'
            ]);
        }

        return response()->json([
            'token' => $user->createToken($request->deivce_name)->plainTextToken
        ]);
    });

    Route::get('wholesale-stores', function () {
        return response()->json(WholesaleStore::all());
    });
});

