<?php

namespace App\Http\Controllers\Manufacture;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManufactureRequest;
use App\Models\Category;
use App\Models\federalDist;
use App\Models\Manufacture;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ManufactureController extends Controller
{
    public function index(Request $request)
    {
        $manufactures = Manufacture::with(['fedDistRegion', 'emails']);


        if ($request->filled('search')) {
            $search = $request->input('search');
            $manufactures->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('inn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('city')) {
            $manufactures->where('city', $request->input('city'));

        } elseif ($request->filled('region')) {
            $manufactures->where('region', $request->input('region'));

        } elseif ($request->filled('dist')) {
            $dist = $request->input('dist');
            $regionIds = federalDist::where('parentid', $dist)->pluck('id');
            $manufactures->whereIn('region', $regionIds);
        }

        $manufactures = $manufactures->orderBy('id', 'DESC')
            ->paginate(30);

        return view('manufacture.index', compact('manufactures'));
    }

    public function show(Manufacture $manufacture)
    {
        $manufacture->load(['fedDistRegion', 'fedDistCity', 'emails']);
        return view('manufacture.show', compact('manufacture'));
    }

    public function fullInformation(Manufacture $manufacture, Request $request)
    {
        $manufacture = $manufacture->load(['products.product', 'categories.category', 'contacts']);

        $editContact = null;

        if ($request->has('editContact')) {
            $editContact = ManufactureContact::findOrFail($request->editContact);
        }

        return view('manufacture.full', compact('manufacture', 'editContact'));
    }

    public function update(ManufactureRequest $request, Manufacture $manufacture)
    {
        $manufacture->update($request->validated());
        Log::info(Auth::user()->name . ' отредактировал производителя ' . $manufacture->id);
        return redirect()->route('manufacture.show', $manufacture->id)->with('success', 'Пользователь успешно обновлен.');
    }

    public function updateBoolean(ManufactureRequest $request, Manufacture $manufacture)
    {
        $manufacture->update($request->validated());
        Log::info(Auth::user()->name . ' отредактировал производителя ' . $manufacture->id);
        return response()->json(['status' => 'success'], 200);
    }

    public function destroy(Manufacture $manufacture)
    {
        $logstring = Auth::user()->name . ' удалил производителя (' . $manufacture->id . ') с названием: ' . $manufacture->name . ', сайтом: ' . $manufacture->web;

        $manufacture->delete();
        Log::info($logstring);
        return redirect()->route('manufacture.index')->with('success', 'Производитель был удален');
    }

    public function addCategoryOrProduct(Manufacture $manufacture, $section)
    {
        switch ($section) {
            case 1:
                return $this->prepareCategoriesView($manufacture);
            case 2:
                return $this->prepareProductsView($manufacture);
            default:
                abort(404, 'Некорректный раздел');
        }
    }

    private function prepareCategoriesView(Manufacture $manufacture)
    {
        $ths = ['Добавить', 'Категория'];
        $placeholder = "Введите категорию";
        $manufacture->load('categories');

        $whereNotInIDs = $manufacture->categories->pluck('category_id')->toArray();

        $cacheKey = Auth::user()->id . '_' . $manufacture->id . '_' . 'category';
        if (Cache::get($cacheKey)){
            $cacheIDs = Cache::get($cacheKey);
            $whereNotInIDs = [...$whereNotInIDs, ...$cacheIDs];
        }

        $items = Category::select(['id', 'name'])
            ->whereNotIn('id', $whereNotInIDs)
            ->where('id', '!=', 1)
            ->where('parentid', '!=', 1)
            ->limit(150)
            ->get()
            ->map(fn($item) => ['id' => $item->id, 'name' => $item->name])
            ->toArray();

        $type = 'Category';

        $route = '/manufacture/add/rMC/'.$manufacture->id;
        $manufacture_id = $manufacture->id;

        return view('manufacture.add', compact('ths', 'items', 'type', 'placeholder', 'route', 'manufacture_id'));
    }

    private function prepareProductsView(Manufacture $manufacture)
    {
        $ths = ['Добавить', 'Название', 'Длина', 'Ширина', 'Высота', 'Масса', 'Категория'];
        $placeholder = "Введите продукцию";
        $route = '/manufacture/add/rMP/'.$manufacture->id;

        $manufacture->load('products');
        $whereNotInIDs = $manufacture->products->pluck('product_id')->toArray();

        $cacheKey = Auth::id() . '_' . $manufacture->id . '_' . 'product';
        if (Cache::has($cacheKey)){
            $cacheIDs = Cache::get($cacheKey);
            $whereNotInIDs = [...$whereNotInIDs, ...$cacheIDs];
        }

        $items = Product::whereNotIn('id', $whereNotInIDs)
            ->with('category')
            ->limit(150)
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'width' => $item->width,
                'height' => $item->height,
                'length' => $item->length,
                'weight' => $item->weight,
                'category' => $item->category?->name,
            ])
            ->toArray();

        $manufacture_id = $manufacture->id;

        return view('manufacture.add', compact('ths', 'items', 'placeholder', 'route', 'manufacture_id'));
    }

    public function CategoriesView(Request $request, Manufacture $manufacture)
    {
        $namecategory = $request->find;
        $namecategory = trim($namecategory);
        $namecategory = preg_replace('/[^a-zA-Zа-яА-Я0-9]/u', '', $namecategory);

        $ths = ['Добавить', 'Категория'];
        $manufacture->load('categories');
        $whereNotInIDs = $manufacture->categories->pluck('category_id')->toArray();

        $cacheKey = Auth::id() . '_' . $manufacture->id . '_' . 'category';
        if (Cache::get($cacheKey)){
            $cacheIDs = Cache::get($cacheKey);
            $whereNotInIDs = [...$whereNotInIDs, ...$cacheIDs];
        }

        $items = Category::select(['id', 'name'])->where('namewithout', 'LIKE', "%{$namecategory}%")
            ->whereNotIn('id', $whereNotInIDs)
            ->where('id', '!=', 1)
            ->where('parentid', '!=', 1)
            ->get()
            ->map(fn($item) => ['id' => $item->id, 'name' => $item->name])
            ->toArray();

        $type = 'Category';


        return response()->json([
            'ths' => $ths,
            'items' => $items,
            'type' => $type,
        ]);
    }

    public function ProductsView(Request $request, Manufacture $manufacture)
    {
        $find = $request->find;
        $find = trim($find);
        $find = preg_replace('/[^a-zA-Zа-яА-Я0-9\s]/u', '', $find);
        $find = preg_replace('/\s+/', ' ', $find);

        $ths = ['Добавить', 'Название', 'Длина', 'Ширина', 'Высота', 'Масса', 'Категория'];
        $manufacture->load('products');
        $whereNotInIDs = $manufacture->products->pluck('product_id')->toArray();

        $cacheKey = Auth::id() . '_' . $manufacture->id . '_' . 'product';
        if (Cache::has($cacheKey)){
            $cacheIDs = Cache::get($cacheKey);
            $whereNotInIDs = [...$whereNotInIDs, ...$cacheIDs];
        }

        $items = Product::whereNotIn('id', $whereNotInIDs)
            ->where('nameS', 'LIKE', "%{$find}%")
            ->with('category')
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'width' => $item->width,
                'height' => $item->height,
                'length' => $item->length,
                'weight' => $item->weight,
                'category' => $item->category?->name,
            ])
            ->toArray();

        return response()->json([
            'ths' => $ths,
            'items' => $items,
        ]);
    }

    public function store(ManufactureRequest $request)
    {
        DB::beginTransaction();
        try {
            $manufacture = Manufacture::create($request->validated());

            $emails = explode(" ", $request->emails);

            if (!empty($emails)) {
                $manufacureEmails = [];
                foreach ($emails as $email) {
                    $manufacureEmails[] = [
                        'manufacture_id' => $manufacture->id,
                        'email' => $email
                    ];
                }

                DB::table('emails')->insert($manufacureEmails);
            }

            DB::commit();
            Log::info(Auth::user()->name . ' создал производителя ' . $manufacture->id);
            return redirect()->route('manufacture.index')->with('success', 'Производитель был добавлен');
        } catch (\Exception $e){
            DB::rollBack();
            Log::error($e->getMessage());
        }
    }

    public function createCache(Request $request, $manufacture_id)
    {
        $id = $request->id;
        $name = $request->name;
        $key = Auth::id() . '_' . $manufacture_id . '_' . $name;

        $existingData = Cache::get($key, []);

        if (!in_array($id, $existingData)) {
            $existingData[] = (int) $id;
            Cache::put($key, $existingData, 600);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Кэш обновлен',
            'data' => Cache::get($key),
        ], Response::HTTP_ACCEPTED);
    }

    public function getCache(Request $request, $manufacture_id)
    {
        $name = $request->name;
        $key = Auth::id() . '_' . $manufacture_id . '_' . $name;

        if ($name === 'category'){
            $data = Category::select(['id', 'name'])->whereIn('id', Cache::get($key, []))->get();
            $ths = ['Удалить', 'Категория'];
            $type = 'Category';

            $return = [
                'items' => $data,
                'ths' => $ths,
                'type' => $type,
            ];

        } else {
            $ths = ['Добавить', 'Название', 'Категория'];
            $data = Product::whereIn('id', Cache::get($key, []))
                ->with('category')
                ->get()
                ->map(fn($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'category' => $item->category?->name,
                ])
                ->toArray();

            $return = [
                'items' => $data,
                'ths' => $ths,
            ];
        }

        return response()->json($return, Response::HTTP_ACCEPTED);
    }

    public function deleteCache(Request $request, $manufacture_id)
    {
        $name = $request->name;
        $deleteID = $request->id;
        $key = Auth::id() . '_' . $manufacture_id . '_' . $name;

        $existingData = Cache::get($key, []);

        if (is_array($existingData)) {
            $existingData = array_filter($existingData, function ($item) use ($deleteID) {
                return $item !== $deleteID;
            });

            Cache::put($key, array_values($existingData));
        }

        return response()->json([
            'message' => 'OK',
            'countData'  => count($existingData)
        ], Response::HTTP_ACCEPTED);
    }

}
