<?php

namespace App\Http\Controllers;

use App\Models\federalDist;
use Illuminate\Http\Request;

class federalDistController extends Controller
{
    public function show($parent_id)
    {
        $federalDist = federalDist::where('parentid', $parent_id)->get();
        return response()->json(['federalDist' => $federalDist], 200);
    }
}
