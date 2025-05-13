<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    RoleController,
    BookshelfController,
    BookController,
    ChapterController,
    PageController
};


Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('bookshelves', BookshelfController::class);
    Route::apiResource('bookshelves.books', BookController::class);
    Route::apiResource('books.chapters', ChapterController::class);
    Route::apiResource('chapters.pages', PageController::class);
    Route::post('/register/employee', [AuthController::class, 'employeeRegistration']);

    Route::post('books/search', [BookController::class, 'search']);
    Route::get('chapters/{chapter}/full-content', [ChapterController::class, 'fullContent']);
});


Route::middleware(['auth:sanctum', 'role:customer,admin'])->group(function () {
     Route::apiResource('bookshelves', BookshelfController::class)->only(['index', 'show']);
     Route::apiResource('bookshelves.books', BookController::class)->only(['index', 'show']);
     Route::apiResource('books.chapters', ChapterController::class)->only(['index', 'show']);
     Route::apiResource('chapters.pages', PageController::class)->only(['index', 'show']);

     Route::post('books/search', [BookController::class, 'search']);
     Route::get('chapters/{chapter}/full-content', [ChapterController::class, 'fullContent']);


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




