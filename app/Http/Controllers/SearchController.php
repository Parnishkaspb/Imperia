<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchCategoryView(Request $request)
    {
        return view('search.category');
    }

    public function searchProductView(Request $request)
    {
        return view('search.product');
    }
}
