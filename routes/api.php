<?php

use App\Http\Controllers\CertificateController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::post('/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($request->only('email', 'password'))) {
        $user = Auth::user();
        $token = $user->createToken('Token Name')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    return response()->json(['error' => 'Unauthorized'], 401);
});

Route::middleware('auth:sanctum')->prefix('certificates')->group(function () {
    Route::post('/search', [CertificateController::class, 'apiSearch']);
    Route::middleware('auth:sanctum')->get('/{document}', [CertificateController::class, 'getDocumentDetails']);
});
