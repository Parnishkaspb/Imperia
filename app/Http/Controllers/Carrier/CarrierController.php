<?php

namespace App\Http\Controllers\Carrier;
use App\Http\Controllers\Controller;
use App\Models\{Carrier, TypeCar};
use Illuminate\Support\Facades\{Auth, DB, Log};
use Illuminate\Http\{Request, Response};
use Illuminate\Support\Collection;

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
        $selectedTypes = $request->input('type_cars', []);

        if (!empty($selectedTypes)) {
            back()->with('error', 'Нет данных для добавления!');
        }

        foreach ($selectedTypes as $type) {
            $insert[] = [
                'who' => $request->who,
                'type_car_id' => (int) $type,
                'telephone' => $request->telephone,
                'email' => $request->email,
                'note' => $request->note,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('carriers')->insert($insert);

        $logString = Auth::user()->name . " добавил нового перевозчика";
        Log::info($logString);

        back()->with('success', 'Добавление произошло успешно');
    }

    public function show(Request $request, Carrier $carrier)
    {
        $who        = $carrier->who;
        $telephone  = $carrier->telephone;
        $email      = $carrier->email;
        $note       = $carrier->note;

        $typesCarIDs = Carrier::where('who', $who)
            ->where('telephone', $telephone)
            ->where('email', $email)
            ->where('note', $note)->get();

        $carriers = [];
        $carriers['id']        = $carrier->id;
        $carriers['who']       = $carrier->who;
        $carriers['telephone'] = $carrier->telephone;
        $carriers['note']      = $carrier->note;
        $carriers['email']     = $carrier->email;
        foreach ($typesCarIDs as $carrier) {
            $carriers['type_car_id'][] = $carrier->type_car_id;
        }
        $carriers = (object)$carriers;

        $types = TypeCar::all();

        return view('carrier.show', compact('carriers', 'types'));
    }

    public function update(Request $request, Carrier $carrier)
    {
        $validated = $request->validate([
            'who' => 'required|string',
            'telephone' => 'required|string',
            'email' => 'required|email',
            'note' => 'nullable|string',
            'type_cars' => 'nullable|array',
            'type_cars.*' => 'integer|exists:type_cars,id',
        ]);

        DB::beginTransaction();

        try {
            $currentTypes = DB::table('carriers')
                ->where('who', $carrier->who)
                ->where('telephone', $carrier->telephone)
                ->where('email', $carrier->email)
                ->where('note', $carrier->note)
                ->pluck('type_car_id')
                ->toArray();

            $selectedTypes = $validated['type_cars'] ?? [];

            $deletedTypes = array_diff($currentTypes, $selectedTypes);
            $createdTypes = array_diff($selectedTypes, $currentTypes);

            if (!empty($deletedTypes)) {
                DB::table('carriers')
                    ->where('who', $carrier->who)
                    ->whereIn('type_car_id', $deletedTypes)
                    ->delete();
            }

            DB::table('carriers')
                ->where('who', $carrier->who)
                ->whereIn('type_car_id', array_intersect($currentTypes, $selectedTypes))
                ->update([
                    'who' => $validated['who'],
                    'telephone' => $validated['telephone'],
                    'email' => $validated['email'],
                    'note' => $validated['note'],
                    'updated_at' => now(),
                ]);

            if (!empty($createdTypes)) {
                $newRecords = array_map(function ($type) use ($validated) {
                    return [
                        'who' => $validated['who'],
                        'type_car_id' => $type,
                        'telephone' => $validated['telephone'],
                        'email' => $validated['email'],
                        'note' => $validated['note'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $createdTypes);

                DB::table('carriers')->insert($newRecords);
            }

            DB::commit();

            Log::info(Auth::user()->name . " обновил перезвочика ". $validated['who']);

            return back()->with('success', 'Данные успешно обновлены!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info(Auth::user()->name . " пытался обновить перезвочика ". $validated['who']);

            return back()->with('error', 'Ошибка при обновлении: ' . $e->getMessage());
        }
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
