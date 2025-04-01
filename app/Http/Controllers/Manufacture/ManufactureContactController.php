<?php

namespace App\Http\Controllers\Manufacture;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManufactureContactRequest;
use App\Models\{Manufacture, ManufactureContact};
use Illuminate\Support\Facades\{Auth, Log};
use Illuminate\Http\{Response};

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;


class ManufactureContactController extends Controller
{
    public function showByManufactureID(Manufacture $manufacture)
    {
        $manufacture->load('contacts');
        $manufacture = $manufacture->contacts?->map(function ($contact) {
            return [
                'id'       => $contact->id,
                'name'     => $contact->name,
                'phone'    => $contact->phone,
                'email'    => $contact->email,
                'position' => $contact->position,
            ];
        })->toArray() ?? [];
        return response()->json($manufacture, Response::HTTP_OK);
    }

    public function store(ManufactureContactRequest $request)
    {
        try {
            ManufactureContact::create([
                'manufacture_id' => $request->manufacture_id,
                'name'           => $request->name,
                'phone'          => $request->phone,
                'position'       => $request->position,
                'email'          => $request->email,
            ]);

            $string = Auth::user()->name ." добавить к " . $request->manufacture_id . " новое контактное лицо";
            Log::info($string);
        } catch (\Exception $e) {
            $string = Auth::user()->name ." пытался добавить к " . $request->manufacture_id . " новое контактное лицо\n
            Ошибка: " . $e->getMessage();
            Log::error($string);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['message' => 'Не удалось создать контакт.'], 'createContact');
        }

        return redirect()
            ->back()
            ->with('success', 'Контакт успешно добавлен.');
    }

    public function update(ManufactureContactRequest $request, ManufactureContact $contact)
    {
        try {
            $contact->update([
                'manufacture_id' => $request->manufacture_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'position' => $request->position,
                'email' => $request->email,
            ]);

            $string = Auth::user()->name ." обновил контактные данные у " . $request->manufacture_id;
            Log::info($string);
        } catch (\Exception $e) {

            $string = Auth::user()->name ." пытался обновить контактные данные у " . $request->manufacture_id . "\nОшибка: " . $e->getMessage();
            Log::error($string);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['message' => 'Ошибка при обновлении.'], 'editContact');
        }

        return redirect()
            ->route('manufacture.show', $contact->manufacture_id)
            ->with('success', 'Контакт успешно обновлён.');
    }

    public function destroy(ManufactureContact $contact)
    {
        $contact->update([
            'active' => false
        ]);

        $string = Auth::user()->name ." удалил контактное лицо у " . $contact->manufacture_id;
        Log::info($string);

        return response()->json([
            $message = 'Удаление прошло успешно!',
        ], Response::HTTP_OK);
    }

}
