<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

// route های مربوط به users

Route::post('/login', [AuthController::class, 'login']);  // ورود کاربران به سیستم
Route::post('/register', [UserController::class, 'create']);  // ایجاد یک کاربر جدید

Route::group(['prefix' => 'users'], function () {
    Route::get('/', [UserController::class, 'getAll']);  // دریافت لیست تمام کاربران
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [AuthController::class, 'logout']);  // خروج کاربران از سیستم
    Route::get('/profile/{id}', [UserController::class, 'show']);  // نمایش اطلاعات کاربر احراز هویت شده

    Route::group(['prefix' => 'users', 'middleware' => 'auth:sanctum'], function () {
        Route::put('/{id}', [UserController::class, 'update']);  // ویرایش اطلاعات یک کاربر خاص
        Route::delete('/{id}', [UserController::class, 'destroy']);  // حذف یک کاربر خاص
        Route::put('restore/{id}', [UserController::class, 'restore']);  // برگرداندن کاربر حذف شده
    })->middleware(AdminMiddleware::class);
});


// route های مربوط به news

Route::group(['prefix' => 'news'], function () {
    Route::get('/', [NewsController::class, 'getAll']);  // دریافت لیست تمام اخبار
    Route::get('/{id}', [NewsController::class, 'show']);  // دریافت یک خبر خاص بر اساس شناسه‌اش
});

Route::group(['prefix' => 'news', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/', [NewsController::class, 'create']);  // ساخت خبر جدید
    Route::put('/{id}', [NewsController::class, 'update']);  // ویرایش یک خبر خاص
    Route::delete('/{id}', [NewsController::class, 'destroy']);  // حذف یک خبر خاص
    Route::put('restore/{id}', [NewsController::class, 'restore']);  // برگرداندن خبر حذف شده
    Route::put('revert/{id}', [NewsController::class, 'revert']); // بازگشت به نسخه‌ای قبلی از خبر
})->middleware(AdminMiddleware::class);


// route های مربوط به categories

Route::group(['prefix' => 'categories'], function () {
    Route::get('/', [CategoryController::class, 'getAll']);  // دریافت لیست تمام شاخه‌ها
    Route::get('/{id}', [CategoryController::class, 'news']);  // دریافت اخبار مربوط به یک شاخه خاص بر اساس شناسه‌اش
});

Route::group(['prefix' => 'categories', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/', [CategoryController::class, 'create']);  // ساخت شاخه‌ جدید
    Route::put('/{id}', [CategoryController::class, 'update']);  // ویرایش یک شاخه‌ خاص
    Route::delete('/{id}', [CategoryController::class, 'destroy']);  // حذف یک شاخه‌ خاص
    Route::put('restore/{id}', [CategoryController::class, 'restore']);  // برگرداندن یک شاخه‌ حذف شده
})->middleware(AdminMiddleware::class);
