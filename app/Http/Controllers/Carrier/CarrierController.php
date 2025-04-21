<?php

namespace App\Http\Controllers\Carrier;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\TypeCar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\{Request, Response};

class CarrierController extends Controller
{
    public function index(Request $request)
    {
        $carriers = Carrier::with(['type']);

        $pagination = (int) ($request->input('pagination') ?? 30);

        if ($request->filled('search')) {
            $search = $request->input('search');

            if (str_contains($search, '@')) {
                $carriers = $carriers->where('email', 'like', '%' . $search . '%');
            } else {
                $carriers = $carriers->where('who', 'like', '%' . $search . '%')
                ->orWhere('telephone', 'like', '%' . $search . '%');
            }
        }
        if ($request->filled('type_car')) {
            $carriers = $carriers->where('type_car_id', (int) $request->input('type_car'));
        }

        $carriers = $carriers->paginate($pagination);

        $types = TypeCar::all();

        return view('carrier.index', compact('carriers', 'types'));
    }

    public function store(Request $request)
    {

    }

    public function destroy(Carrier $carrier)
    {
        $carrier->delete();

        return back()->with('success', 'Удаление произошло успешно');
    }

    public function change(Carrier $carrier, $type)
    {
        $who = $carrier->who;
        $telephone = $carrier->telephone;
        $email = $carrier->email;

        $carriers = Carrier::where('who', $who)->where('telephone', $telephone)->where('email', $email);

        if ($type === 'work')
        {
            $carriers->update([
                'isWorkEarly' => !$carrier->isWorkEarly,
                'updated_at' => now()
            ]);
        } else {
            $carriers->update([
                'isDoc' => !$carrier->isDoc,
                'updated_at' => now()
            ]);
        }

        $logInfo = Auth::user()->name . " обновил данные у " . $carrier->who . ". Обновлял: " . $type;
        Log::info($logInfo);

        return response()->json([
            'message' => 'Успешное обновление данных'
        ], Response::HTTP_ACCEPTED);
    }
}
