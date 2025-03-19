<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManufactureRequest;
use App\Models\Category;
use App\Models\Manufacture;
use App\Models\ManufactureCategory;
use App\Models\ManufactureProduct;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ManufactureCategoryProductsController extends Controller
{
    public function manufactureCategoryStore(Request $request, Manufacture $manufacture)
    {
        $key = Auth::id() . '_' . $manufacture->id . '_category';

        if (!Cache::has($key)) {
            return response()->json([
                'message' => 'Массив для добавления пуст! Необходимо что-то добавить',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $categoriesID = Cache::get($key, []);

        $manufacture->load('categories');
        $categoriesOld = $manufacture->categories->pluck('category_id')->toArray();

        $categoriesID = array_diff($categoriesID, $categoriesOld);

        $category = array_map(function ($item) use ($manufacture) {
            return [
                'manufacture_id' => $manufacture->id,
                'category_id' => $item
            ];
        }, $categoriesID);

        if (!empty($category)) {
            DB::table('manufacture_categories')->insert($category);
        }

        Cache::forget($key);

        Log::info(Auth::user()->name . ' добавил к производителю ' . $manufacture->id) . " новые категории (" . json_encode($categoriesID)  . ")";
        return response()->json([
            'message' => empty($category) ? 'Все категории уже были добавлены!' : 'Вставка прошла успешно!',
            'route'   => '/manufacture/info/' . $manufacture->id,
        ], Response::HTTP_OK);
    }
    public function manufactureProductStore(Request $request, Manufacture $manufacture)
    {
        $key = Auth::id() . '_' . $manufacture->id . '_product';

        if (!Cache::has($key)) {
            return response()->json([
                'message' => 'Массив для добавления пуст! Необходимо что-то добавить',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $productsID = Cache::get($key, []);

        $manufacture->load(['products', 'categories']);
        $productsOld = $manufacture->products->pluck('product_id')->toArray();

        $productsID = array_diff($productsID, $productsOld);

        $product = array_map(function ($item) use ($manufacture) {
            return [
                'manufacture_id' => $manufacture->id,
                'product_id' => $item
            ];
        }, $productsID);

        /*
         * Получаем ID категорий по ID продукции.
         * Нужно для того, чтобы потом сравнить если эта категория уже в БД у этого производителя
         **/
        $productsModel = Product::whereIn('id', $productsID)->with('category')->get();
        $categoryProductsModel = $productsModel->pluck('category.id')->filter()->unique()->toArray();

        $categoryManufactureIDs = $manufacture->categories->pluck('category_id')->toArray();

        $newCategoriesIDS = array_diff($categoryProductsModel, $categoryManufactureIDs);

        $category = array_map(function ($item) use ($manufacture) {
            return [
                'manufacture_id' => $manufacture->id,
                'category_id' => $item,
            ];
        }, $newCategoriesIDS);

        DB::beginTransaction();
        try {
            if (empty($product)) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Отсутствуют главные компоненты для добавления!'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if (!empty($category)) {
                DB::table('manufacture_categories')->insert($category);
            }
            DB::table('manufacture_products')->insert($product);

            Log::info(Auth::user()->name . ' добавил к производителю ' . $manufacture->id) . " новые категории (" . json_encode($newCategoriesIDS)  . ")\n
            и новую продукцию (" . json_encode($productsID)  . ")";
            DB::commit();

            Cache::forget($key);

            return response()->json([
                'message' => 'Вставка прошла успешно!',
                'route'   => '/manufacture/info/' . $manufacture->id,
            ], Response::HTTP_OK);
        } catch (\Exception $e){
            DB::rollBack();
            $logString = Auth::user()->name . "попытался(лась) добавить продукцию к производителю, но произошла ошибка\nОшибка: " . $e->getMessage();
            Log::error($logString);
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function manufacturePCDelete(Request $request, $delete_id, $name)
    {
        if ($name === 'product'){
            DB::beginTransaction();
            $mp = ManufactureProduct::find($delete_id);
            $manufacture_id = $mp->manufacture_id;
            try {
                $mp->delete();
                DB::commit();
                $logString = Auth::user()->name . " удалил от производителя продукцию";
                Log::info($logString);

                return redirect()->route('manufacture.fullInformation', $manufacture_id)
                    ->with('success', "Удаление произошло успешно");

            } catch (\Exception $e){
                DB::rollBack();
                $logString = Auth::user()->name . " пытался удалить от производителя продукцию";
                Log::info($logString);

                return redirect()->route('manufacture.fullInformation', $manufacture_id)
                    ->with('error', $e->getMessage());
            }
        } elseif ($name === 'category'){
            DB::beginTransaction();
            $mc = ManufactureCategory::find($delete_id);
            $manufacture_id = $mc->manufacture_id;
            $category_id = $mc->category_id;

            try {
                $mc->delete();
                $productIDs = Product::where('category_id', $category_id)->pluck('id')->toArray();
                ManufactureProduct::whereIn('product_id', $productIDs)->delete();

                DB::commit();
                $logString = Auth::user()->name . " удалил от производителя категорию ";
                Log::info($logString);

                return redirect()->route('manufacture.fullInformation', $manufacture_id)
                    ->with('success', "Удаление произошло успешно");
            } catch (\Exception $e){
                DB::rollBack();
                $logString = Auth::user()->name . " пытался удалить от производителя категорию " . $category_id;
                Log::info($logString);

                return redirect()->route('manufacture.fullInformation', $manufacture_id)
                    ->with('error', $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Нет типа, который вы передали!'
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function manufacturePCUpdate(Request $request, $id, $name){
        $class = 0;
        switch ($name){
            case 'product':
                $mp = ManufactureProduct::find($id);
                $class = $mp->doit ? 0 : 1;
                $mp->update([
                   'doit' => $class,
                ]);
                $mp->save();
                break;
            case 'category':
                $mc = ManufactureCategory::find($id);
                $class = $mc->likethiscategory ? 0 : 1;

                $mc->update([
                    'likethiscategory' => $class,
                ]);

                $mc->save();
                break;
        }

        $name = $class ? "Да" : "Нет";
        $class = $class ? "warning" : "danger";

        $classFull = "btn btn-sm btn-outline-" . $class;
        return response()->json([
            'class' => $classFull,
            'name' => $name
        ], Response::HTTP_OK);
    }

    public function manufactureUpdateComment(Request $request, ManufactureCategory $id){
        $comment = $request->comment;
        $id->update(['comment' => $comment]);
        $id->save();

        return response()->json([
            'message' => 'Комментарий успешно обновлен'
        ], Response::HTTP_OK);
    }

}
