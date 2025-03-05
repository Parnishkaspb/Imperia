<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailRequest;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    public function store(EmailRequest $request)
    {
        try {
            Email::create($request->validated());
            Log::info(Auth::user()->name . ' добавил почту к производителю ('. $request->manufacture_id .')');
            return response()->json([
                'success' => true,
            ], 200);
        } catch (\Exception $e) {
            Log::error(Auth::user()->name . ' пытался добавить почту к производителю ('. $request->manufacture_id .')\nОшибка: '. $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], $e->getCode());
        }
    }
}
