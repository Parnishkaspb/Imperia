<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;

class AdminController extends Controller
{
    public function index(){
        $users = User::select(['id', 'email', 'name', 'role_id'])->with(['roles'])->where("is_work", 1)->get();
        $roles = Role::all();
        return view('admin.index', compact('users', 'roles'));
    }
}
