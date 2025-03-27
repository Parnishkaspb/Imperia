<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\federalDist;
use App\Models\ManufactureCategory;
use Illuminate\Http\{Request, Response};

class SearchController extends Controller
{
    public function searchCategoryView(Request $request)
    {
        return view('search.category');
    }

    public function searchCategoryJson(Request $request)
    {
        $input  = $request->input;
        $dist   = (int) $request->dist;
        $region = (int) $request->region;
        $city   = (int) $request->city;

        $input = preg_replace('/[^\p{L}\p{N}]/u', '', $input);

        $categories = collect(Category::select('id', 'name')
            ->when($input !== "", function ($query) use ($input) {
                return $query->where('nameWithOut', 'like', '%' . ltrim($input) . '%');
            })
            ->groupBy('id')
            ->get());

        $categoryIDs = $categories->pluck('id')->toArray();

        $mc = ManufactureCategory::with('manufacture.emails')
            ->whereIn('category_id', $categoryIDs)
            ->orderBy('likethiscategory', 'DESC')
            ->get();

        if ($city !== 0) {
            $mc = $mc->filter(function ($item) use ($city) {
                return $item->manufacture && $item->manufacture->city === $city;
            });

        } elseif ($region !== 0 || $dist !== 0) {
            $mc = $mc->filter(function ($item) use ($region, $dist) {
                if (!$item->manufacture) return false;

                if ($region !== 0 && $item->manufacture->region !== $region) {
                    return $item->manufacture && $item->manufacture->region === $region;
                }

                if ($dist !== 0) {
                    $federalDist = federalDist::where('parentid', $dist)->pluck('id')->toArray();
                    return in_array($item->manufacture->region, $federalDist);
                }

                return true;
            });
        }

        $mc = $mc->map(function ($item) use ($categories) {
            $cat = $categories->where('id', $item->category_id)->first();
            return [
                'name_category' => $cat ? $cat->name : "",
                'name_manufacture' => $item->manufacture?->name,
                'price_manufacture' => $item->manufacture?->price,
                'website' => $item->manufacture?->web,
                'emails'  => $item->manufacture?->emails->map(function ($email) {
                    return [
                        'email' => $email->email,
                    ];
                }),
                'id_manufacture' => $item->manufacture?->id,
                'comment_category' => $cat? $cat->comment : "",
            ];
        })->toArray();

        return response()->json([
                'data'   => $mc
            ], Response::HTTP_OK);
    }

    public function searchProductView(Request $request)
    {
        return view('search.product');
    }
}
