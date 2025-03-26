@extends('layouts.app')

@section('title', $manufacture->name)

@section('content')
    <h4 class="text-center">
        <a href="{{$manufacture->web}}" target="_blank"> {{$manufacture->name}} </a>
    </h4>

    <!-- Кнопки состояния производителя -->
    <div class="d-flex justify-content-center align-items-center mb-3">
        <button type="button" class="btn btn-outline-{{ $manufacture->checkmanufacture ? 'primary' : 'danger' }} ms-2 checkmanufacture"
                data-id="{{ (bool) $manufacture->checkmanufacture }}">
            {{ $manufacture->checkmanufacture ? 'Проверенный производитель' : 'Не работали с производителем' }}
        </button>

        <button type="button" class="btn btn-outline-{{ $manufacture->date_contract ? 'primary' : 'danger' }} ms-2 date_contract"
                data-id="{{ (bool) $manufacture->date_contract }}">
            {{ $manufacture->date_contract ? 'Есть договор' : 'Нет договора' }}
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <button class="btn btn-outline-primary mb-3" data-bs-toggle="modal" data-bs-target="#createNewContactPerson">
        Новое контактное лицо
    </button>

    <!-- Кнопки для открытия информации в производители -->
    <div class="d-flex justify-content-center align-items-center mb-3">
        <table>
            <thead>
                <tr>
                    <th>
                        <button type="button" class="btn btn-outline-primary add-categories" style="width: 190px;"
                                data-id="0">
                            Добавить категорию
                        </button>
                    </th>
                    <th>
                        <button type="button" class="btn btn-outline-primary  show-categories" style="width: 190px;"
                                data-id="1">
                            Просмотр категорий
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <button type="button" class="btn btn-outline-primary add-products" style="width: 190px;"
                            data-id="0">
                        Добавить продукцию
                    </button>
                </td>
                <td>
                    <button type="button" class="btn btn-outline-primary show-products" style="width: 190px;"
                            data-id="1">
                        Просмотр продукций
                    </button>
                </td>
            </tr>

            </tbody>
        </table>
    </div>

    <!-- Информация производителя -->
    <div class="d-flex justify-content-center align-items-center mb-3">
        <table class="table" style="width: 50%; display: none" id="categories">
            <thead class="table-warning">
            <tr>
                <th>Название</th>
                <th>Комментарий</th>
                <th>Делает</th>
                <th>Действие</th>
            </tr>
            </thead>
            <tbody>
            @foreach($manufacture->categories as $category)
                <tr>
                    <td> {{ $category->category->name }} </td>
                    <td> {{ $category->comment}} </td>
                    <td> {{ $category->likethiscategory ? "Да" : "Нет" }} </td>
                    <td>
                        <form method="POST" action="" class="d-inline"
                              {{--                            <form method="POST" action="{{ route('manufacture_product.destroy', $product->id) }}" class="d-inline"--}}
                              onsubmit="return confirm('Вы уверены, что хотите удалить эту связку?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <table class="table" style="width: 50%; display: none"  id="products">
            <thead class="table-warning">
            <tr>
                <th>Название</th>
                <th>Делает</th>
                <th>Действие</th>
            </tr>
            </thead>
            <tbody>
            @foreach($manufacture->products as $product)
                <tr>
                    <td> {{ $product->product->name }} </td>
                    <td> {{ $product->doit ? "Да" : "Нет" }} </td>
                    <td>
                        <form method="POST" action="" class="d-inline"
                              {{--                            <form method="POST" action="{{ route('manufacture_product.destroy', $product->id) }}" class="d-inline"--}}
                              onsubmit="return confirm('Вы уверены, что хотите удалить эту связку?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Модальное окно для добавления нового контактного лица -->
    <div class="modal fade @if ($errors->any()) show d-block @endif" id="createNewContactPerson" tabindex="-1" aria-labelledby="createNewContactPersonLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Добавление нового производителя</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
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
                                {{-- Опции ролей будут добавлены здесь --}}
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
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-outline-primary w-100">Создать</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let csrfToken = $('input[name="_token"]').val();

            $('.checkmanufacture, .date_contract').click(function() {
                let manufactureId = "{{$manufacture->id}}";
                let field = $(this).hasClass('checkmanufacture') ? 'checkmanufacture' : 'date_contract';
                let newValue = $(this).data('id') === 1 ? 0 : 1;

                $.ajax({
                    url: `/manufacture/boolean/${manufactureId}`,
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: JSON.stringify({
                        [field]: newValue,
                        name: '{{$manufacture->name}}',
                        web: '{{$manufacture->web}}',
                    }),
                    contentType: 'application/json',
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        console.error('Ошибка:', xhr);
                        alert('Ошибка при обновлении данных');
                    }
                });
            });

            $('#categories, #products').hide();

            $('.show-categories').click(function () {
                if ($('#categories').is(':visible')) {
                    $('#categories').hide();
                    return
                }
                $('#products').fadeOut(200, function () {
                    $('#categories').fadeIn(200);
                });
            });

            $('.show-products').click(function () {
                if ($('#products').is(':visible')) {
                    $('#products').hide();
                    return
                } // Если уже открыто, ничего не делать
                $('#categories').fadeOut(200, function () {
                    $('#products').fadeIn(200);
                });
            });
        });

    </script>
@endsection
