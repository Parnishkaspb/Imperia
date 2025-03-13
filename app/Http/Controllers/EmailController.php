<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailRequest;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    public function store(EmailRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $email = Email::create($request->validated());
            Log::info(Auth::user()->name . ' добавил почту к производителю ('. $request->manufacture_id .')');
            return response()->json([
                'success' => true,
                'email' => [
                    'id' => $email->id,
                    'name' => $email->email
                ],
                'message' => 'Email успешно добавлен!'

            ], 200);
        } catch (\Exception $e) {
            Log::error(Auth::user()->name . ' пытался добавить почту к производителю ('. $request->manufacture_id .')\nОшибка: '. $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function check(Request $request)
    {
        $email = Email::where('email', $request->email)->exists();
        if ($email) {
            return response()->json([
                'success' => false,
                'message' => 'Такая почта уже существует у другого производителя!'
            ], 500);
        }

        return response()->json([
            'success' => true,
        ],  200);
    }

    public function update (EmailRequest $request, Email $email): \Illuminate\Http\JsonResponse
    {
        if ($request->manufacture_id !== $email->manufacture_id){
            return response()->json([
                'success' => false,
                'message' => 'Что-то произшло с ID Производителя!!! Обновление невозможно!'
            ],500);
        }
        $stringUpdate = Auth::user()->name . ' отредактировал email ' . $email->id . 'С ' . $email->email . ' на ' . $request->email;
        $email->update($request->validated());
        Log::info($stringUpdate);
        return response()->json(['success' => true,
            'message' => 'Успешное обновление почты'],200);
    }

    public function destroy(Email $email): \Illuminate\Http\JsonResponse
    {
        $manufacture_id = $email->manufacture_id;
        try {
            $email->delete();
            Log::info(Auth::user()->name . ' удалил почту у производителя ('. $manufacture_id .')');
            return response()->json([
                'success' => true,
                'message' => 'Email успешно удален!'
            ], 200);
        } catch (\Exception $e) {
            Log::error(Auth::user()->name . ' пытался удалить почту к производителя ('. $manufacture_id .')\nОшибка: '. $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], $e->getCode());
        }
    }
}
