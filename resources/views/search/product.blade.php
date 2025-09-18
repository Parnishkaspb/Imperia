@extends('layouts.app')

@section('title', 'Поиск по продукции')

@section('content')

    <form action="{{ route('search.product') }}" method="GET" class="mb-4">
        <div class="row g-3 align-items-end">

            {{-- Поле поиска --}}
            <div class="col-md-4">
                <label for="search" class="form-label">Поиск</label>
                <input type="text" name="search" id="search" class="form-control"
                       placeholder="Введите название или категорию..."
                       value="{{ request('search') }}">
            </div>

            {{-- Радиокнопки выбора типа поиска --}}
            <div class="col-md-3">
                <label class="form-label d-block">Тип поиска</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" id="byProduct" value="product"
                        {{ request()->get('type', 'product') === 'product' ? 'checked' : '' }}>
                    <label class="form-check-label" for="byProduct">По названию</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" id="byCategory" value="category"
                        {{ request()->get('type') === 'category' ? 'checked' : '' }}>
                    <label class="form-check-label" for="byCategory">По категории</label>
                </div>
            </div>

            <div class="col-md-2">
                <label for="pagination" class="form-label">Кол-во на странице</label>
                <select class="form-select" id="pagination" name="pagination">
                    <option value="10" {{ request()->get('pagination') == '10' ? 'selected' : '' }}>10</option>
                    <option value="30" {{ request()->get('pagination') == '30' ? 'selected' : '' }}>30</option>
                    <option value="50" {{ request()->get('pagination') == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request()->get('pagination') == '100' ? 'selected' : '' }}>100</option>
                </select>
            </div>

            {{-- Кнопки действий --}}
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-50">Поиск</button>
                <a href="{{ route('search.product', ['pagination' => 30, 'order_id' => request()->get('order_id')]) }}" class="btn btn-outline-secondary w-50">Сброс</a>
            </div>


            {{-- Кнопки действий для добавления к заказу --}}
            <div class="col-md-12 d-flex gap-2">
                <button type="button" onclick="addToOrder({{request()->get('order_id') ?? null}})" class="btn btn-outline-warning {{request()->get('order_id') ? "w-50" : "w-100"}}"> {{request()->get('order_id') ? "Добавить к заказу: " . request()->get('order_id')  : "Создать новый"}} </button>
                @if(request()->get('order_id'))
                <a href="{{ route('order.show', request()->get('order_id')) }}" class="btn btn-outline-secondary w-50"> Вернуться к заказу </a>
                @endif

                <input type="hidden" name="order_id" value="{{request()->get('order_id')}}">
            </div>

            <div class="col-md-12 d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary w-100" id="watchCount" onclick="countRedis({{request()->get('order_id') ?? null}})"> Посмотреть </button>
            </div>

        </div>
    </form>

    <table class="table table-hover" style="display: none" id="tableCount">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody id="tbodyTable"></tbody>
    </table>


    <table class="table table-hover">
        <thead class="table-dark">
        <tr>
            <th>Выбор</th>
            <th>Название</th>
            <th>Длина</th>
            <th>Ширина</th>
            <th>Высота</th>
            <th>Вес</th>
            <th>Категория</th>
            @if(in_array(Auth::user()->role_id, [1, 2]))
                <th>Действия</th>
            @endif
        </tr>
        </thead>
        <tbody>

        @foreach($results as $result)
            <tr>
                <td>
                    <input type="checkbox" name="scales" class="orderRedis" onclick="getSelectedCheckboxArray(this, {{$result->id}}, '{{$result->name}}', {{$result->category?->id}}, {{request()->get('order_id') ?? null}})"
                        {{ in_array($result->id, $items) ? 'checked' : '' }}
                    >
                </td>
                <td>
                    {{ $result->name }}
                </td>

                <td>
                    {{ $result->length }}
                </td>

                <td>
                    {{ $result->width }}
                </td>

                <td>
                    {{ $result->height }}
                </td>

                <td>
                    {{ $result->weight }}
                </td>

                <td>
                    {{ $result->category?->name }}
                </td>
                @if(in_array(Auth::user()->role_id, [1, 2]))
                    <td>
                        <a href="{{ route('edit.show.category', ['category' => $result->category?->id]) }}" target="_blank" class="btn btn-outline-danger mt-2"> Изменить категорию </a>
                        <a href="{{ route('edit.show.product', ['product' => $result->id]) }}" target="_blank" class="btn btn-outline-danger"> Изменить продукцию </a>
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-3">
        {{ $results->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>

    <script>
        const csrfToken = $('input[name="_token"]').val();

        function validateEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

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

                    // Если дальше нужно загружать следующую зависимость
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

            if (selectedDist) {
                workWithFederalDist(selectedDist, 'dist', selectedRegion || null);
            }

            if (selectedRegion && selectedCity) {
                workWithFederalDist(selectedRegion, 'region', selectedCity);
            }

            $('#dist').change(function() { workWithFederalDist($(this).val(), 'dist'); });
            $('#region').change(function() { workWithFederalDist($(this).val(), 'region'); });
        });

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

        $(document).ready(function() {
            var emails = [];

            $(document).on('input', '.email-input', function() {
                let oldEmail = $(this).attr('data-old-email');
                let newEmail = $(this).val().trim();

                if (!newEmail || !validateEmail(newEmail)) return;

                let emailIndex = emails.indexOf(oldEmail);
                if (emailIndex !== -1) {
                    emails[emailIndex] = newEmail;
                }

                $(this).attr('data-old-email', newEmail);
                $("#emails").val(emails.join(","));
            });

            $(document).on('click', '.delete-email', function() {
                let emailId = $(this).data('id');
                if (!confirm("Вы точно хотите удалить почту?")) return;

                let emailIndex = emails.indexOf(emailId);
                if (emailIndex !== -1) {
                    emails.splice(emailIndex, 1);
                }

                $(`button[data-id='${emailId}']`).closest('.email-container').remove();

                $("#emails").val(emails.join(","));
            });

            $('#add-email').click(function() {
                let newEmailValue = $('#new-email').val().trim();
                if (!newEmailValue) {
                    alert("Поле email не может быть пустым!");
                    return;
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(newEmailValue)) {
                    alert("Пожалуйста, введите корректный email!");
                    return;
                }

                $.ajax({
                    url: '/email/checked',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        email: newEmailValue,
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.success) {
                            if (!emails.includes(newEmailValue)){
                                emails.push(newEmailValue);

                                $("#emails").val(emails.join(","));
                                let newEmailHtml = `
                                <div class="email-container d-flex align-items-center mb-2">
                                    <input type="email" class="form-control email-input" name="emails[${newEmailValue}]" value="${newEmailValue}" required disabled>
                                    <button type="button" class="btn btn-danger ms-2 delete-email" data-old-email="${newEmailValue}" data-id="${newEmailValue}">Удалить</button>
                                </div>`;
                                $('#email-list').append(newEmailHtml);
                                $('#new-email').val('');
                            }
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr);
                        alert('Ошибка при добавлении почты: ' + xhr.response.message);
                    }
                });
            });
        });

        function getSelectedCheckboxArray(checkbox, product_id, product_name, product_category, order = null){
            let isChecked = $(checkbox).is(':checked');
            $.post({
                url: "/order/product",
                headers: { 'X-CSRF-TOKEN': csrfToken },
                data: {
                    product_id, product_name, order, isChecked, product_category
                },
                success: function (response){
                    // console.log(response);
                },
                error: function (){

                }
            });
        }

        function countRedis(order_id){
            $('#tableCount').toggle();

            $.post({
                url: "/order/productC",
                headers: { 'X-CSRF-TOKEN': csrfToken },
                data: {
                    order: order_id
                },
                success: function (response){
                    $("#tbodyTable").html(response);
                },
                error: function (){

                }
            });
        }


        function addToOrder(order = null){
            $.post({
                url: '/order',
                data: {order},
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function (response){
                    alert(response.message);
                    if (response.order_id >= 1){
                        location.href = "/order/" + response.order_id;
                    }
                },
                error: function (){}
            });
        }
    </script>
@endsection
