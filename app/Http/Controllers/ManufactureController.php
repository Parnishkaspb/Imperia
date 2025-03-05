<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManufactureRequest;
use App\Models\Email;
use App\Models\federalDist;
use App\Models\Manufacture;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


class ManufactureController extends Controller
{
    public function index(){
        $manufactures = Manufacture::with(['fedDistRegion', 'fedDistCity', 'emails'])->orderBy('id', 'DESC')->paginate(30);
        return view('manufacture.index', compact('manufactures'));
    }

    public function show(Manufacture $manufacture){
        $manufacture->load(['fedDistRegion', 'fedDistCity', 'emails']);
        return view('manufacture.show', compact('manufacture'));
    }

    public function update(ManufactureRequest $request, Manufacture $manufacture){
        $manufacture->update([
            'name' => $request->name,
            'web' => $request->web,
//            'adress_loading' => $request->adress_loading,
//            'note' => $request->note,
//            'nottypicalproduct'=> $request->nottypicalproduct,
//            'checkmanufacture'=> $request->checkmanufacture,
//            'date_contract'=> $request->date_contract,
//            'region'=> $request->region,
//            'city'=> $request->city,
        ]);

        return redirect()->route('manufacture.show', $manufacture->id)->with('success', 'Пользователь успешно обновлен.');
    }

    public function destroy(Manufacture $manufacture){
        $logstring = Auth::user()->name . ' удалил производителя (' . $manufacture->id . ') с названием: ' . $manufacture->name . ', сайтом: ' . $manufacture->web;

        $manufacture->delete();
        Log::info($logstring);
        return redirect()->route('manufacture.index')->with('success', 'Производитель был удален');
    }
}
