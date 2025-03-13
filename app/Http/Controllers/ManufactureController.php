<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManufactureRequest;
use App\Models\Category;
use App\Models\Manufacture;
use App\Models\ManufactureCategory;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ManufactureController extends Controller
{
    public function index()
    {
        $manufactures = Manufacture::with(['fedDistRegion', 'emails'])
            ->orderBy('id', 'DESC')
            ->paginate(30);

        return view('manufacture.index', compact('manufactures'));
    }

    public function show(Manufacture $manufacture)
    {
        $manufacture->load(['fedDistRegion', 'fedDistCity', 'emails']);
        return view('manufacture.show', compact('manufacture'));
    }

    public function fullInformation(Manufacture $manufacture)
    {
        $manufacture = $manufacture->load(['products.product', 'categories.category']);

        return view('manufacture.full', compact('manufacture'));
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
        $manufacture->load('categories');
        $categoryIDs = $manufacture->categories->pluck('category_id')->toArray();

        $items = Category::select(['id', 'name'])
            ->whereNotIn('id', $categoryIDs)
            ->where('id', '!=', 1)
            ->where('parentid', '!=', 1)
            ->get()
            ->map(fn($item) => ['id' => $item->id, 'name' => $item->name])
            ->toArray();

        $type = 'Category';

        return view('manufacture.add', compact('ths', 'items', 'type'));
    }

    private function prepareProductsView(Manufacture $manufacture)
    {
        $ths = ['Добавить', 'Название', 'Длина', 'Ширина', 'Высота', 'Масса', 'Категория'];
        $manufacture->load('products');
        $productIDs = $manufacture->products->pluck('product_id')->toArray();

        $items = Product::whereNotIn('id', $productIDs)
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

        return view('manufacture.add', compact('ths', 'items'));
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

}
