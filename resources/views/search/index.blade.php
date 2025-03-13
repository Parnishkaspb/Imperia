@extends('layouts.app')

@section('title', 'Производители')

@section('content')
    @if (in_array(Auth::user()->role_id, [1, 2, 3]))
    <button class="btn btn-outline-info mb-3" data-bs-toggle="modal" data-bs-target="#createNewManufacture">
        <h4>Производители</h4>
    </button>
    @else
        <h4>Производители</h4>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <table class="table table-hover">
        <thead class="table-dark">
        <tr>
            <th>Имя</th>
            <th>Почта</th>
            <th>Сайт</th>
            <th>Регион</th>
            <th>Город</th>
            <th>Адрес погрузки</th>
            <th>Заметки</th>
            <th>Просмотр</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($manufactures as $manufacture)
            <tr>
                <td>
                    @php
                        $maxLength = 15;
                        $fullName = $manufacture->name;
                        $shortName = Str::limit($fullName, $maxLength, '...');
                    @endphp
                    <span class="name-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $fullName }}">
                        {{ $shortName }}
                    </span>
                </td>

                <td>
                    @if($manufacture->emails->isEmpty())
                        <a href="{{ route('manufacture.show', $manufacture->id) }}" class="btn btn-sm btn-outline-primary">
                            Добавить
                        </a>
                    @elseif($manufacture->emails->count() >= 1)
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="emailDropdown{{ $manufacture->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                Почта
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="emailDropdown{{ $manufacture->id }}">
                                @foreach($manufacture->emails as $email)
                                    <li><a class="dropdown-item" href="mailto:{{ $email->email }}">{{ $email->email }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        @if(isset($manufacture->emails[0]))
                            <a href="mailto:{{ $manufacture->emails[0]->email }}">{{ $manufacture->emails[0]->email }}</a>
                        @else
                            <a href="{{ route('manufacture.show', $manufacture->id) }}" class="btn btn-sm btn-outline-primary">
                                Добавить
                            </a>
                        @endif
                    @endif
                </td>


                <td> <a href="{{ $manufacture->web}}" target="_blank"> Сайт </a> </td>
                <td>{{ $manufacture->fedDistRegion?->name ?? "Без региона"}}</td>
                <td>{{ $manufacture->fedDistCity?->name ?? "Без города"}}</td>

                <td>
                    @php
                        $maxLength = 15;
                        $fullName = $manufacture->adress_loading;
                        $shortName = Str::limit($fullName, $maxLength, '...');
                    @endphp
                    <span class="name-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $fullName }}">
                        {{ $shortName }}
                    </span>
                </td>

                <td>
                    @php
                        $maxLength = 15;
                        $fullName = $manufacture->note;
                        $shortName = Str::limit($fullName, $maxLength, '...');
                    @endphp
                    <span class="name-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $fullName }}">
                        {{ $shortName }}
                    </span>
                </td>

                <td>
                    <a href="{{ route('manufacture.fullInformation', $manufacture->id) }}" class="btn btn-sm btn-outline-info">
                        Просмотр
                    </a>
                </td>

                <td>
                    <a href="{{ route('manufacture.show', $manufacture->id) }}" class="btn btn-sm btn-outline-primary">
                        Редактировать
                    </a>
                    @if(in_array(Auth::user()->role_id, [1, 2, 3]))
                        <form method="POST" action="{{ route('manufacture.destroy', $manufacture->id) }}" class="d-inline"
                              onsubmit="return confirm('Вы уверены, что хотите удалить производителя?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-3">
        {{ $manufactures->links('pagination::bootstrap-5') }}
    </div>


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
