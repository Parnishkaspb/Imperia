<?php

namespace App\Http\Controllers\Edit;

use App\Http\Controllers\Controller;
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

}
