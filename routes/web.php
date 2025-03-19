<?php

use App\Http\Controllers\{AdminController,
    EmailController,
    federalDistController,
    LoginController,
    ManufactureCategoryProductsController,
    ManufactureController,
    UserController};
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckRole;

Route::get('/', function () {
    return redirect()->route('login');
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

            Route::get('/info/{manufacture}', 'fullInformation')->name('manufacture.fullInformation');

            Route::get('/add/{manufacture}/{section}', 'addCategoryOrProduct')->name('manufacture.add');

            Route::post('/add/rMC/{manufacture}', 'CategoriesView');
            Route::post('/add/rMP/{manufacture}', 'ProductsView');

            Route::post('/', 'store')->name('manufacture.store');
            Route::put('/{manufacture}', 'update')->name('manufacture.update');
            Route::put('/boolean/{manufacture}', 'updateBoolean')->name('manufacture.boolean');
            Route::delete('/{manufacture}', 'destroy')->name('manufacture.destroy');

            Route::prefix('/cache')->group(function () {
                Route::post('/{manufacture_id}', 'createCache')->name('manufacture.cache.create');
                Route::post('/show/{manufacture_id}', 'getCache')->name('manufacture.cache.get');
                Route::delete('/{manufacture_id}', 'deleteCache')->name('manufacture.cache.delete');
            });
        });

        Route::controller(ManufactureCategoryProductsController::class)->group(function () {
            Route::post('/addPC/category/{manufacture}', 'manufactureCategoryStore');
            Route::post('/addPC/product/{manufacture}', 'manufactureProductStore');

            Route::put('/updatePC/{id}/{name}', 'manufacturePCUpdate');
            Route::put('/updateComment/{id}', 'manufactureUpdateComment');


            Route::delete('/delete/{delete_id}/{name}', 'manufacturePCDelete')->name('manufacture.pc.delete');
        });
    });

    Route::prefix('/email')->group(function () {
        Route::controller(EmailController::class)->group(function () {
            Route::post('/', 'store')->name('email.store');
            Route::post('/checked', 'check')->name('email.check');
            Route::put('/{email}', 'update')->name('email.update');
            Route::delete('/{email}', 'destroy')->name('email.destroy');
        });
    });

    Route::prefix('/federalDist')->group(function () {
        Route::controller(federalDistController::class)->group(function () {
            Route::get('/{parent_id}', 'show')->name('federalDist.show');
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
