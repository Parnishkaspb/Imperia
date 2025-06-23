@extends('layouts.app')

@section('title', "Поиск по категориям")

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="containerQuery mt-2">
        <form action="{{ route('search.category') }}" method="GET">
            <div class="form-group">
                <label for="search">Введите запрос</label>
                <input type="text" class="form-control" name="search" id="search" placeholder="Введите название категории" value="{{ request('search') }}">
            </div>

            <div class="d-flex justify-content-between">
                <div class="form-group select-container">
                    <label for="dist">Федеральный округ</label>
                    <select class="form-control" id="dist" name="dist">
                        <option value="">Выберите</option>
                    </select>
                </div>

                <div class="form-group select-container">
                    <label for="region">Регион</label>
                    <select class="form-control" id="region" name="region">
                        <option value="">Выберите</option>
                    </select>
                </div>

                <div class="form-group select-container">
                    <label for="city">Город</label>
                    <select class="form-control" id="city" name="city">
                        <option value="">Выберите</option>
                    </select>
                </div>
            </div>

            <input type="hidden" name="order_id" id="order_id" value="{{ request('order_id') }}">

            {{-- Кнопки действий --}}
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary w-50">Поиск</button>
                <a href="{{ route('search.category', ['pagination' => 30]) }}" class="btn btn-outline-secondary w-50">Сброс</a>
            </div>

            <div class="form-group mt-4">
                <select class="form-select" id="pagination" name="pagination">
                    <option value="10" {{ request()->get('pagination') == '10' ? 'selected' : '' }}>Количество элементов на странице: 10</option>
                    <option value="30" {{ request()->get('pagination') == '30' ? 'selected' : '' }}>Количество элементов на странице: 30</option>
                    <option value="50" {{ request()->get('pagination') == '50' ? 'selected' : '' }}>Количество элементов на странице: 50</option>
                    <option value="100" {{ request()->get('pagination') == '100' ? 'selected' : '' }}>Количество элементов на странице: 100</option>
                </select>
            </div>
        </form>
    </div>

    <style>
        .containerQuery {
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 10px;
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px;
            font-size: 14px;
        }

        .btn-info {
            background-color: #5bc0de;
            border: none;
            color: white;
            border-radius: 8px;
            padding: 12px 20px;
        }

        .btn-info:hover {
            background-color: #31b0d5;
        }

        .d-flex {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .select-container {
            flex: 1;
            max-width: 33%;
        }

        .select-container select {
            width: 100%;
        }
    </style>

    <table class="table table-hover">
        <thead class="table-dark">
        <tr>
            <th>Категория</th>
            <th>Производитель</th>
            <th>Сайт</th>
            <th>Почта</th>
            <th>Город</th>
            <th>Комментарий</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($mc as $item)
            <tr>
                <td>
                    @if($item['price_manufacture'] !== '')
                        <a href="{{ $item['price_manufacture'] }}" class="btn btn-outline-success" target="_blank"> Прайс </a>
                    @endif
                    @if($item['count_category'] > 0 )
                            <button class="btn btn-outline-secondary mb-3 mt-1" id="showAll" data-bs-toggle="modal"
                                    data-id-category="{{$item['id_category']}}"
                                    data-id-manufacture="{{$item['id_manufacture']}}"
                                    data-bs-target="#showAllProductsByThisManufactureAndCategory">
                                {{ $item['name_category'] }}
                            </button>
                    @endif
                    @if ($item['price_manufacture'] === '' && $item['count_category'] === 0)
                        {{ $item['name_category'] }}
                    @endif
                </td>
                <td>{{ $item['name_manufacture'] }}</td>

                <td>
                    <a href="{{ $item['website'] }}" class="btn btn-outline-info" target="_blank">Перейти</a>
                </td>

                <td>
                    @if($item['emails']->isEmpty())
                        <a href="/manufacture/{{ $item['id_manufacture'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            Добавить
                        </a>
                    @else
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button"
                                    id="emailDropdown{{ $item['id_manufacture'] }}" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                Почта
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="emailDropdown{{ $item['id_manufacture'] }}">
                                @foreach($item['emails'] as $email)
                                    <li><a class="dropdown-item" href="mailto:{{ $email }}">{{ $email }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </td>

                <td>{{ $dist[$item['id_city_manufacture']] ?? '' }}</td>

                <td id="td_comment_{{$item['id_all']}}"> {{ $item['comment_category'] ?? ''}}  </td>

                <td>
                    <form method="POST" action="{{ route('manufacture.pc.delete', [$item['id_all'], 'category']) }}" class="d-inline"
                          onsubmit="return confirm('Вы уверены, что хотите удалить эту связку?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                    </form>

                    <button class="btn btn-sm btn-outline-primary mb-1 mt-1" id="addComment" data-bs-toggle="modal" data-id="{{$item['id_all']}}" data-bs-target="#createNewComment">
                        Комментарий
                    </button>

                    <a href="{{ route('manufacture.fullInformation', $item['id_manufacture']) }}" target="_blank" class="btn btn-sm btn-outline-secondary"> К производителю </a>

                    @if(!empty(request('order_id')))
                        <button type="button" onclick="addToOrder({{ request('order_id') }}, {{ $item['id_manufacture'] }}, {{ $item['id_category'] }})" class="btn btn-sm btn-outline-warning mb-1 mt-1">
                            К заказу № {{ request('order_id') }}
                        </button>
                    @endif
                </td>

            </tr>
        @endforeach
        </tbody>
    </table>

    {{-- Laravel pagination links --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $mc->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    <div id="answer"></div>

    {{--Модальное окно для просмотра продукции, которое производится этим производителем по этой категории--}}
    <div class="modal fade" id="showProductsByCategoryByManufacture" tabindex="-1" aria-labelledby="showProductsByCategoryByManufactureLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Продукция</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="productsShow"></div>
            </div>
        </div>
    </div>

    <div class="modal fade @if ($errors->any()) show d-block @endif" id="createNewComment" tabindex="-1" aria-labelledby="createNewCommentLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Редактировать комментарий</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        @csrf
                        <input type="hidden" id="newCommentID" value="">
                        <div class="mb-3">
                            <label for="comment" class="form-label">Комментарий</label>
                            <textarea name="comment" class="form-control" id="newCommentText"></textarea>
                        </div>
                        <button id="addCommentModal" class="btn btn-outline-primary w-100">Создать</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        let csrfToken = $('input[name="_token"]').val();

        const selectedDist = "{{ request('dist') }}";
        const selectedRegion = "{{ request('region') }}";
        const selectedCity = "{{ request('city') }}";

        function workWithFederalDist(parent_id, type, selected = null) {
            let targetSelect;
            if (type === 'createDist') {
                targetSelect = '#dist';
            } else if (type === 'dist') {
                $('#region, #city').empty().append('<option value="">Выберите</option>');
                targetSelect = '#region';
            } else if (type === 'region') {
                $('#city').empty().append('<option value="">Выберите</option>');
                targetSelect = '#city';
            }

            $.ajax({
                url: '/federalDist/' + parent_id,
                headers: { 'X-CSRF-TOKEN': csrfToken },
                method: 'GET',
                success: function (response) {
                    $(targetSelect).empty().append('<option value="">Выберите</option>');
                    response.federalDist.forEach(item => {
                        const isSelected = selected && selected == item.id ? 'selected' : '';
                        $(targetSelect).append(`<option value="${item.id}" ${isSelected}>${item.name}</option>`);
                    });

                    if (type === 'dist' && selectedRegion) {
                        workWithFederalDist(selected, 'region', selectedCity);
                    }
                },
                error: function (response) {
                    console.error('Ошибка загрузки данных:', response);
                }
            });
        }

        $(document).ready(function() {
            workWithFederalDist(1, 'createDist', selectedDist);
            if (selectedDist) workWithFederalDist(selectedDist, 'dist', selectedRegion);
            if (selectedRegion && selectedCity) workWithFederalDist(selectedRegion, 'region', selectedCity);

            $('#dist').change(function () { workWithFederalDist($(this).val(), 'dist'); });
            $('#region').change(function () { workWithFederalDist($(this).val(), 'region'); });



            $(document).on('click', '#addComment', function() {
                let id = $(this).data('id');
                $('#newCommentID').val(id);
                let comment = $('#td_comment_' + id).text();
                $('#newCommentText').text(comment.trim());
                console.log(comment);
            });

            $(document).on('click', '#addCommentModal', function() {
                let id = $('#newCommentID').val();
                let comment = $('#newCommentText').val();
                console.log(id, comment);

                $.ajax({
                    url: '/manufacture/updateComment/'+ id,
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        comment: comment
                    },
                    success: function(response) {
                        alert(response.message);
                        $('#td_comment_' + id).text(comment.trim());
                    },
                    error: function(xhr) {
                        alert('Ошибка: ' + xhr.response.message);
                    }
                });
            });


            $(document).on('click', '#showAll', function() {
                const id_manufacture = $(this).data('id-manufacture');
                const id_category = $(this).data('id-category');

                $.get({
                    url: '/manufacture/product/' + id_manufacture + '/' + id_category,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (response) {
                        let table = '<table class="table table-bordered"><thead><tr>' +
                            '<th>Название</th>' +
                            '<th>Делает</th>' +
                            '<th>Действие</th>' +
                            '</tr></thead><tbody>';

                        response.products.forEach(product => {
                            table += `<tr>
                                <td>${product.name}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-${product.doit ? 'warning' : 'danger'}"
                                            id="yesno"
                                            data-name="product"
                                            data-id="${product.id}">
                                        ${product.doit ? 'Да' : 'Нет'}
                                    </button>
                                </td>
                                <td>
                                    <form method="POST"
                                          action="/manufacture/delete/${product.id}/product"
                                          class="d-inline"
                                          onsubmit="return confirm('Вы уверены, что хотите удалить эту связку?');">
                                        <input type="hidden" name="_token" value="${csrfToken}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                                    </form>
                                </td>
                            </tr>`;
                        });

                        table += '</tbody></table>';

                        $('#productsShow').html(table);
                        $('#showProductsByCategoryByManufacture').modal('show');
                    }
                });

            });


            $(document).on('click', '#yesno', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let element = $(this);

                $.ajax({
                    url: '/manufacture/updatePC/'+ id +'/' + name,
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        element.text(response.name);
                        element.attr('class', response.class);
                    },
                    error: function(xhr) {
                        alert('Ошибка: ' + xhr.response.message);
                    }
                });
            });

        });

        function addToOrder(order_id, manufacture_id, category_id){
            $.ajax({
                url: '/order/addManufacture/' + order_id,
                headers: { 'X-CSRF-TOKEN': csrfToken },
                type: "POST",
                data: {manufacture_id, category_id,},
                success: function (response){
                    // console.log(response);
                    alert(response.message);
                },
                error: function (){

                }
                // .done(function(data){
                //     if (what == 8){
                //         alert("Комментарий успешно добавлен");
                //     }
                //     if(what == 10){
                //         location.reload();
                //     }
                //
                //     // alert('/order_dd.php?id='+deal);
            });
        }

    </script>
@endsection
