<?php

namespace App\Http\Controllers\Edit;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        return view('edit.category', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $nameS = preg_replace('/[^\p{L}\p{N}]/u', '', $request->name);

        $category->update([
            'name' => $request->name,
            'namewithout'  => $nameS,
        ]);

        $category->save();

        $string = Auth::user()->name ." изменил ID категории" . $category->id;
        Log::info($string);

        return redirect()->route('edit.show.category', ['category' => $category->id])->with('success', 'Обновление произошло успешно');
    }

    public function store(CategoryRequest $request)
    {
        try {
            $data                = $request->validated();
            $data['namewithout'] = Helper::cleanSearchString($request->validated());
            $data['parent_id']   = 1;

            $category = Category::create($data);

            Log::info('User '. Auth::user()->name . ' created category ID: ' . $category->id);

            return redirect()->route('edit.show.category' , $category->id)
                ->with('success', 'Категория успешно добавлена');

        } catch (\Exception $e) {
            Log::error('Попытка добавить категорию '. Auth::user()->name . ': ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Произошла ошибка при создании категории. Попробуйте еще раз.']);
        }
    }

    public function index(Request $request)
    {
        return view('edit.store_category');
    }
}
