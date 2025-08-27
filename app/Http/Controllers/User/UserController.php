<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function show(User $user)
    {
        $roles = Role::all();
        return view('user.index', compact(['user', 'roles']));
    }

    public function store(UserRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->password !== $request->password_confirmation) {
                return redirect()->route('admin.index')
                    ->withErrors(['password' => 'Пароли не совпадают!'])
                    ->withInput();
            }

            $data['password'] = $request->password;
            $data['role_id'] = $request->role_id;

            $user = User::create($data);
            Log::info(Auth::user()->name . ' создал пользователя ' . $user->id);
            return redirect()->route('admin.index')->with('success', 'Пользователь успешно создан.');
        } catch (\Exception $e){
            Log::error($e->getMessage());
            return redirect()->route('admin.index')
                ->withErrors($request->validated())
                ->withInput();
        }
    }

    public function update(UserRequest $request, User $user)
    {
        $user->update($request->validated());

        Log::info(Auth::user()->name . ' отредактировал пользователя ' . $user->id);
        return redirect()->route('user.show', $user->id)->with('success', 'Пользователь успешно обновлен.');
    }

    public function update_password(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'password_old' => 'required|string',
            'password_new' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->route('user.show', $user->id)
                ->withErrors($validator)
                ->withInput();
        }

        if (!Hash::check($request->password_old, $user->password)) {
            return redirect()->route('user.show', $user->id)
                ->with('passwordOld_error', 'Старый пароль не совпадает!');
        }

        $user->update([
            'password' => $request->password_new,
        ]);

        Log::info(Auth::user()->name . ' отредактировал пароль пользователю ' . $user->id);

        return redirect()->route('user.show', $user->id)
            ->with('success', 'Пароль успешно обновлен.');
    }

    public function destroy(User $user)
    {
        $logstring = Auth::user()->name . ' уволил пользователя (' . $user->id . ') с именем: ' . $user->name . ', фамилией: ' . $user->surname;

        $user->update([
            'email'    => "deleteduser@imperiajbi.ru",
            'password' => "deleteduser",
            'is_work'  => false,
        ]);

        Log::info($logstring);
        return redirect()->route('admin.index')->with('success', 'Пользователь был удален');
    }
}
