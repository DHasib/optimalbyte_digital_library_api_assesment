<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    RoleController,
};


Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    Route::apiResource('roles', RoleController::class);
    Route::post('/register/employee', [AuthController::class, 'employeeRegistration']);
});



Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {

});




Route::middleware(['auth:sanctum', 'role:customer,admin'])->group(function () {



});


Route::middleware(['auth:sanctum', 'role:employee'])->group(function () {

});



Route::withoutMiddleware(['auth:sanctum', RoleCheck::class,])->group(function () {
    Route::post('/register', [AuthController::class, 'customerRegistration']);
    Route::post('/login', [AuthController::class, 'login']);

});


/**
 * POST /logout
 *
 * This endpoint logs out the authenticated user by calling the logout method in the AuthController.
 * It ensures that only authenticated users (using the 'auth:sanctum' middleware) can access this route.
 *
 */
Route::post('/logout', [AuthController::class, 'logout'])
     ->middleware('auth:sanctum');




