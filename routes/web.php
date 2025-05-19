<?php

use App\Http\Middleware\CheckIp;
use App\Models\Product;
use App\Http\Controllers\{Carrier\CarrierController,
    Cities\federalDistController,
    Edit\CategoryController,
    Edit\ProductController,
    Manufacture\ManufactureCategoryProductsController,
    Manufacture\ManufactureContactController,
    Manufacture\ManufactureController,
    OrderController,
    OrderDetailController,
    Search\SearchController,
    User\AdminController,
    User\EmailController,
    User\LoginController,
    User\UserController};
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;

Route::middleware(CheckIp::class)->group(function () {

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

            Route::controller(ManufactureContactController::class)->group(function () {
                Route::delete('/contact/{contact}', 'destroy')->name('manufacture.contact.delete');

                Route::post('/contact', 'store')->name('manufacture.contact.store');
                Route::put('/manufacture/contact/{contact}', 'update')->name('manufacture.contact.update');
            });

            Route::controller(ManufactureCategoryProductsController::class)->group(function () {
                Route::post('/addPC/category/{manufacture}', 'manufactureCategoryStore');
                Route::post('/addPC/product/{manufacture}', 'manufactureProductStore');

                Route::put('/updatePC/{id}/{name}', 'manufacturePCUpdate');
                Route::put('/updateComment/{id}', 'manufactureUpdateComment');


                Route::delete('/delete/{delete_id}/{name}', 'manufacturePCDelete')->name('manufacture.pc.delete');
            });

            Route::controller(ProductController::class)->group(function () {
                Route::get('/product/{manufacture}/{category}', 'showProductsByManufacture')->name('product.index');
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

        Route::prefix('/search')->group(function () {
            Route::controller(SearchController::class)->group(function () {
                Route::get('/category', 'searchCategoryView')->name('search.category');
                Route::post('/cfind', 'searchCategoryJson');

                Route::get('/product', 'searchProductView')->name('search.product');
            });
        });

        Route::prefix('/edit')->group(function () {
            Route::controller(ProductController::class)->group(function () {
                Route::get('/product/{product}', 'show')->name('edit.show.product');
                Route::put('/product/{product}', 'update')->name('edit.update.product');
            });

            Route::controller(CategoryController::class)->group(function () {
                Route::get('/category/{category}', 'show')->name('edit.show.category');
                Route::put('/category/{category}', 'update')->name('edit.update.category');
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

    Route::middleware(['auth', CheckRole::class . ':5'])->group(function () {
        Route::prefix('carrier')->group(function () {
            Route::controller(CarrierController::class)->group(function () {
                Route::get('/', 'index')->name('carrier.index');
                Route::post('/', 'store')->name('carrier.store');
                Route::delete('/{carrier}', 'destroy')->name('carrier.destroy');
                Route::get('/{carrier}', 'show')->name('carrier.show');
                Route::put('/{carrier}', 'update')->name('carrier.update');

                Route::put('/{carrier}/{type}', 'change')->name('carrier.change');


            });
        });
    });

    Route::middleware(['auth', CheckRole::class . ':4'])->group(function () {
        Route::prefix('carrier')->group(function () {
            Route::controller(OrderController::class)->group(function () {
                Route::get('/', 'index')->name('carrier.index');
                Route::post('/', 'store')->name('carrier.store');
                Route::delete('/{carrier}', 'destroy')->name('carrier.destroy');
                Route::get('/{carrier}', 'show')->name('carrier.show');
                Route::put('/{carrier}', 'update')->name('carrier.update');

                Route::put('/{carrier}/{type}', 'change')->name('carrier.change');


            });
        });
    });

});

//Route::middleware(['auth', 'role:2'])->group(function () {
//    Route::get('/user/dashboard', function () {
//        return view('user.dashboard');
//    });
//});
