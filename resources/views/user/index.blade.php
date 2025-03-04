@extends('layouts.app')

@section('title', 'Редактирование пользователя')

@section('content')
    <div class="container">
        <h3>Редактирование пользователя: {{$user->name}}</h3>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Форма редактирования пользователя -->
        <form method="POST" action="{{ route('user.update', $user->id) }}" class="mb-3 border border-1 border-solid p-3">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Имя</label>
                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
            </div>

            <div class="mb-3">
                <label for="surname" class="form-label">Фамилия</label>
                <input type="text" name="surname" class="form-control" value="{{ $user->surname }}" required>
            </div>

            <div class="mb-3">
                <label for="patronymic" class="form-label">Отчество</label>
                <input type="text" name="patronymic" class="form-control" value="{{ $user->patronymic }}">
            </div>

            <div class="mb-3">
                <label for="role_id" class="form-label">Роль пользователя</label>
                <select name="role_id" class="form-control">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" @if($role->id === $user->role_id) selected @endif>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
            </div>
            <button type="submit" class="btn btn-outline-primary form-control">Обновить</button>
        </form>

        @if(session('passwordOld_error'))
            <div class="alert alert-danger">
                {{ session('passwordOld_error') }}
            </div>
        @endif

        @if(session('passwordNew_error'))
            <div class="alert alert-danger">
                {{ session('passwordNew_error') }}
            </div>
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="alert alert-danger">
                    {{ $error }}
                </div>
            @endforeach
        @endif

        <!-- Форма обновления пароля -->
        <form method="POST" action="{{ route('user.update.password', $user->id) }}" class="mb-3 border border-1 border-solid p-3">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="password_old" class="form-label">Старый пароль</label>
                <input type="password" name="password_old" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password_new" class="form-label">Новый пароль</label>
                <input type="password" name="password_new" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password_new_confirmation" class="form-label">Подтвердите новый пароль</label>
                <input type="password" name="password_new_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-outline-danger form-control">Обновить пароль</button>
        </form>
    </div>
@endsection
