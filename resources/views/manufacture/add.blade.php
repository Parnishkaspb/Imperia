@extends('layouts.app')

@section('title', 'Добавление к производителю')

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <table class="table table-hover">
        <thead class="table-dark">
        <tr>
            @foreach($ths as $th)
                <th> {{ $th }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td><button id="{{ $item['id'] }}" > Добавить  </button></td>
                <td> {{ $item['name'] }} </td>

                @if(!isset($type))
                    <td> {{ $item['width'] }} </td>
                    <td> {{ $item['height'] }} </td>
                    <td> {{ $item['length'] }} </td>
                    <td> {{ $item['weight'] }} </td>
                    <td> {{ $item['category'] }} </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>

{{--    <div class="d-flex justify-content-center mt-3">--}}
{{--        {{ $items->links('pagination::bootstrap-5') }}--}}
{{--    </div>--}}


    <!-- Модальное окно для добавления нового производителя -->
    <div class="modal fade @if ($errors->any()) show d-block @endif" id="createNewManufacture" tabindex="-1" aria-labelledby="createNewManufactureLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createNewManufacture">Добавление нового производителя</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger">
                                {{ $error }}
                            </div>
                        @endforeach
                    @endif
                    <form method="POST" action="{{ route('user.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Имя</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="surname" class="form-label">Фамилия</label>
                            <input type="text" name="surname" class="form-control" value="{{ old('surname') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="patronymic" class="form-label">Отчество</label>
                            <input type="text" name="patronymic" class="form-control" value="{{ old('patronymic') }}">
                        </div>
                        <div class="mb-3">
                            <label for="role_id" class="form-label">Роль</label>
                            <select name="role_id" class="form-select" required>
{{--                                @foreach($roles as $role)--}}
{{--                                    <option value="{{ $role->id }}" @if(old('role_id') == $role->id) selected @endif>--}}
{{--                                        {{ $role->name }}--}}
{{--                                    </option>--}}
{{--                                @endforeach--}}
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Подтвердите пароль</label>
                            <input type="password" name="password_confirmation"
                                   class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-outline-primary w-100">Создать</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip({
                trigger: 'manual',
                placement: 'top'
            }).on('click', function () {
                let _this = $(this);
                if (_this.attr('data-tooltip-shown') === 'true') {
                    _this.tooltip('hide').attr('data-tooltip-shown', 'false');
                } else {
                    _this.tooltip('show').attr('data-tooltip-shown', 'true');
                    $('.tooltip').on('click', function () {
                        _this.tooltip('hide').attr('data-tooltip-shown', 'false');
                    });
                }
            });
        });
    </script>
@endsection
