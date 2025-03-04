<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManufactureController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckRole;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function (){
    return view('login');
})->name('login');


Route::controller(LoginController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

Route::middleware('auth')->group(function () {
    Route::prefix('/manufacture')->group(function () {
        Route::controller(ManufactureController::class)->group(function () {
            Route::get('/', 'index')->name('manufacture.index');
            Route::get('/{manufacture}', 'show')->name('manufacture.show');


            Route::post('/', 'store')->name('manufacture.store');
            Route::put('/{manufacture}', 'update')->name('manufacture.update');
            Route::delete('/{manufacture}', 'destroy')->name('manufacture.destroy');
        });
    });
});


Route::middleware(['auth', CheckRole::class . ':1'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin.index');

        Route::prefix('/user')->group(function () {
            Route::controller(UserController::class)->group(function () {
                Route::get('/{user}', 'show')->name('user.show');
                Route::post('/', 'store')->name('user.store');

                Route::put('/{user}', 'update')->name('user.update');
                Route::put('/password/{user}', 'update_password')->name('user.update.password');
                Route::delete('/{user}', 'destroy')->name('user.destroy');
            });
        });
    });
});



//Route::middleware(['auth', 'role:2'])->group(function () {
//    Route::get('/user/dashboard', function () {
//        return view('user.dashboard');
//    });
//});
