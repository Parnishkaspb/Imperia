<?php

namespace App\Http\Controllers\Cities;

use App\Http\Controllers\Controller;
use App\Models\federalDist;

class federalDistController extends Controller
{
    public function show($parent_id)
    {
        $federalDist = federalDist::where('parentid', $parent_id)->get();
        return response()->json(['federalDist' => $federalDist], 200);
    }
}
