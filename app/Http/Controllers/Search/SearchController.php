<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Manufacture;
use App\Models\Category;
use App\Models\federalDist;
use App\Models\ManufactureCategory;
use App\Models\Product;
use Illuminate\Http\{Request, Response};

class SearchController extends Controller
{
    public function searchCategoryView(Request $request)
    {
        $search = $request->input('search');
        $searchClean = preg_replace('/[^\p{L}\p{N}]/u', '', trim($search));
        $pagination = $request->input('pagination') ?? 30;

        $dist   = (int) $request->input('dist');
        $region = (int) $request->input('region');
        $city   = (int) $request->input('city');

        // Поиск категорий
        $categories = Category::select('id', 'name')
            ->when($searchClean !== '', function ($query) use ($searchClean) {
                $query->where('nameWithOut', 'like', '%' . $searchClean . '%');
            })
            ->get();

        $categoryIDs = $categories->pluck('id');

        // Получение ManufactureCategory c отношениями
        $mcQuery = ManufactureCategory::with(['manufacture.emails'])
            ->whereIn('category_id', $categoryIDs)
            ->orderByDesc('likethiscategory');

        // Фильтрация по региону, округу или городу
        if ($city !== 0) {
            $mcQuery->whereHas('manufacture', fn($q) => $q->where('city', $city));
        } elseif ($region !== 0) {
            $mcQuery->whereHas('manufacture', fn($q) => $q->where('region', $region));
        } elseif ($dist !== 0) {
            $regionIds = federalDist::where('parentid', $dist)->pluck('id');
            $mcQuery->whereHas('manufacture', fn($q) => $q->whereIn('region', $regionIds));
        }

        // Пагинированный результат
        $mcPaginated = $mcQuery->paginate($pagination);

        // Преобразуем результат, добавляя данные из categories
        $mcPaginated->getCollection()->transform(function ($item) use ($categories) {
            $cat = $categories->firstWhere('id', $item->category_id);
            return [
                'name_category'       => $cat->name ?? '',
                'name_manufacture'    => $item->manufacture->name ?? '',
                'price_manufacture'   => $item->manufacture->price ?? '',
                'website'             => $item->manufacture->web ?? '',
                'emails'              => $item->manufacture->emails->pluck('email'),
                'id_manufacture'      => $item->manufacture->id ?? null,
                'comment_category'    => $item->comment ?? '',
                'id_category'         => $item->category_id,
                'id_all'              => $item->id,
                'id_city_manufacture' => $item->manufacture->city ?? null,
            ];
        });

        $distList = federalDist::pluck('name', 'id');

        return view('search.category', [
            'mc'   => $mcPaginated,
            'dist' => $distList,
        ]);
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
                'name_category'       => $cat ? $cat->name : "",
                'name_manufacture'    => $item->manufacture?->name,
                'price_manufacture'   => $item->manufacture?->price,
                'website'             => $item->manufacture?->web,
                'emails'              => $item->manufacture?->emails->map(function ($email) {
                    return [
                        'email'       => $email->email,
                    ];
                }),
                'id_manufacture'      => $item->manufacture?->id,
                'comment_category'    => $cat? $cat->comment : "",
                'id_category'         => $item->category_id,
                'id_city_manufacture' => $item->manufacture?->city,
            ];
        })->toArray();

        $dist = federalDist::pluck('name', 'id')->toArray();
        return response()->json([
                'data'   => $mc,
                'dist'   => $dist
            ], Response::HTTP_OK);
    }

    public function searchProductView(Request $request)
    {
        $search = $request->input('search');
        $searchClean = preg_replace('/[^\p{L}\p{N}]/u', '', ltrim($search));
        $pagination = $request->input('pagination') ?? 30;

        $query = Product::with('category');

        $filled = $request->input('type') ?? '';

        // Поиск по имени продукта
        if ($filled === 'product' && $search) {
            $query->where('nameS', 'like', '%' . $searchClean . '%');
        }

        // Поиск по имени категории
        if ($filled === 'category' && $search) {
            $query->whereHas('category', function ($q) use ($searchClean) {
                $q->where('namewithout', 'like', '%' . $searchClean . '%');
            });
        }

        $results = $query->orderBy('id')->paginate($pagination);

        return view('search.product', compact('results'));
    }

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

}
