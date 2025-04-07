<?php

namespace App\Http\Controllers\Edit;

use Illuminate\Support\Facades\{Auth, Log};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        return view('edit.product', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $nameS = preg_replace('/[^\p{L}\p{N}]/u', '', $request->name);

        $product->update([
            'name' => $request->name,
            'length' => $request->length,
            'width' => $request->width,
            'height' => $request->height,
            'weight' => $request->weight,
            'concrete_volume' => $request->concrete_volume,
            'nameS'  => $nameS,
        ]);

        $product->save();

        $string = Auth::user()->name ." изменил ID продукта" . $product->id;
        Log::info($string);

        return redirect()->route('edit.show.product', ['product' => $product->id])->with('success', 'Обновление произошло успешно');
    }
}
