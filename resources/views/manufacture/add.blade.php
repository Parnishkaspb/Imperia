@extends('layouts.app')

@section('title', 'Добавление к производителю')

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <style>
        /* Общий контейнер для поиска */
        .search-container {
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
            padding: 10px;
            /*border-bottom: 1px solid #ccc;*/
            display: flex;
            justify-content: center;
        }

        .search-form {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            max-width: 1200px;
        }

        /* Поле ввода */
        .search-form input {
            flex-grow: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* Кнопка */
        .search-form button {
            background-color: #ffc107;
            border: none;
            padding: 8px 15px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
        }

        .table-container {
            max-height: 700px;
            overflow-y: auto;
            border: 1px solid #ccc;
            width: 100%;
            max-width: 1200px; /* Должно совпадать с поисковой строкой */
            margin: auto;
        }

        .table thead {
            position: sticky;
            top: 0;
            z-index: 9;
            background-color: #343a40;
        }

        .table thead th {
            background-color: #343a40 !important;
            color: white !important;
            padding: 10px;
        }

        .divForButton {
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
            /*padding: 10px;*/
            /*border-bottom: 1px solid #ccc;*/
            display: flex;
            justify-content: center;
        }
    </style>

    <!-- Фиксированное поле поиска -->
    <div class="divForButton">
        <button class="btn btn-info mb-3" id="buttonModal" data-name="{{isset($type) ? "category" : "product"}}" data-bs-toggle="modal" data-bs-target="#showCacheData">
            {{$count = count(Cache::get(Auth::id() . "_" . $manufacture_id . "_" . (isset($type) ? "category" : "product"), []))}}
            Просмотр данных. {{ $count > 0 ? "Кол-во: " . $count : "" }}
        </button>
    </div>
    <div class="search-container">
        <div class="search-form">
            @csrf
            <input type="text" name="find" id="find" placeholder="{{$placeholder}}">
            <button type="button" class="btn btn-warning ms-2 " id="search">Искать</button>
        </div>
    </div>

    <!-- Контейнер с прокруткой для таблицы -->
    <div class="table-container">
        <table class="table table-hover" id="table">
            <thead class="table-dark">
            <tr>
                @foreach($ths as $th)
                    <th> {{ $th }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr id="tr_{{$item['id']}}">
                    <td>
                        <button class="btn btn-primary" id="addsmt" data-id="{{ $item['id'] }}" data-name="{{isset($type) ? "category" : "product"}}"> Добавить  </button>
                    </td>
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
    </div>


    <div class="modal fade" id="showCacheData" tabindex="-1" aria-labelledby="showCacheDataLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="btn btn-primary" id="addToManufacture" data-name="{{isset($type) ? "category" : "product"}}"><h5 class="modal-title">Добавить к производителю</h5></button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-hover" id="tableModal"></table>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            const csrfToken = $('input[name="_token"]').val();

            $('#search').click(function() {
                let find = $('#find').val().trim();

                if (!find) {
                    alert("Поле поиска не может быть пустым!");
                    return;
                }

                $.ajax({
                    url: '{{$route}}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        find: find,
                    },
                    success: function(response) {
                        let table = `<thead class="table-dark"><tr>`;
                        response.ths.forEach(th => {
                            table += `<th>${th}</th>`;
                        });
                        table += `</tr></thead><tbody>`;

                        response.items.forEach(item => {
                            table += `<tr id="tr_${item.id}">
                        <td><button class="btn btn-primary" id="addsmt" data-id="${item.id}" data-name="${response.type ? 'category' : 'product'}">Добавить</button></td>
                        <td>${item.name}</td>`;

                            if (!response.type) {
                                table += `
                        <td>${item.width}</td>
                        <td>${item.height}</td>
                        <td>${item.length}</td>
                        <td>${item.weight}</td>
                        <td>${item.category}</td>`;
                            }

                            table += `</tr>`;
                        });

                        table += `</tbody>`;

                        $('#table').html(table);
                    },
                    error: function(xhr) {
                        console.log(xhr);
                        alert('Ошибка: ' + xhr.response.message);
                    }
                });
            });

            $(document).on('click', '#addsmt', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');

                $.ajax({
                    url: '/manufacture/cache/'+{{$manufacture_id}},
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        id: id,
                        name: name
                    },
                    success: function(response) {
                        $("#tr_" + id).remove();
                        console.log(response.data.length);

                        $('#buttonModal').html('Просмотр данных. Кол-во: ' + response.data.length);
                    },
                    error: function(xhr) {
                        console.log(xhr);
                        alert('Ошибка: ' + xhr.response.message);
                    }
                });
            });

            $(document).on('click', '#buttonModal', function (){
                let name = $(this).data('name');

                $.ajax({
                    url: '/manufacture/cache/show/'+{{$manufacture_id}},
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        name: name
                    },
                    success: function(response) {
                        let table = `<thead class="table-dark"><tr>`;
                        response.ths.forEach(th => {
                            table += `<th>${th}</th>`;
                        });
                        table += `</tr></thead><tbody>`;

                        response.items.forEach(item => {
                            table += `<tr id="tr_delete_${item.id}">
                        <td><button class="btn btn-danger" id="deletesmt" data-id="${item.id}" data-name="${response.type ? 'category' : 'product'}">Удалить</button></td>
                        <td>${item.name}</td>`;

                            if (!response.type) {
                                table += `<td>${item.category}</td>`;
                            }

                            table += `</tr>`;
                        });

                        table += `</tbody>`;

                        $('#tableModal').html(table);
                    },
                    error: function(xhr) {
                        console.log(xhr);
                        alert('Ошибка: ' + xhr.response.message);
                    }
                });

            })

            $(document).on('click', '#deletesmt', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');

                $.ajax({
                    url: '/manufacture/cache/'+{{$manufacture_id}},
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        id: id,
                        name: name
                    },
                    success: function(response) {
                        $("#tr_delete_" + id).remove();

                        $('#buttonModal').html('Просмотр данных. Кол-во: ' + response.countData);
                    },
                    error: function(xhr) {
                        console.log(xhr);
                        alert('Ошибка: ' + xhr.response.message);
                    }
                });
            });

            $(document).on('click', '#addToManufacture', function() {
                let name = $(this).data('name');

                $.ajax({
                    url: '/manufacture/addPC/' + name + '/' + {{$manufacture_id}},
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        name: name
                    },
                    success: function(response){
                        alert(response.message);
                        location.href = response.route;
                    },
                    error: function(xhr) {
                        let httpCode = xhr.status;

                        let errorMessage = 'Произошла неизвестная ошибка';

                        if (xhr.responseText) {
                            try {
                                let response = JSON.parse(xhr.responseText);
                                errorMessage = response.message || errorMessage;
                            } catch (e) {
                                console.error("Ошибка парсинга JSON:", e);
                            }
                        }

                        console.log("Ошибка:", errorMessage);
                        alert(`Ошибка ${httpCode}: ${errorMessage}`);
                    }
                });
            });

        });

    </script>
@endsection
