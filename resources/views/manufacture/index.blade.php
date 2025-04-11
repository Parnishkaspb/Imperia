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

    <form action="{{ route('manufacture.index') }}" method="GET" class="mb-4">
        <div class="row g-3 align-items-end">

            <div class="col-md-4">
                <label for="search" class="form-label">Поиск по имени, ИНН или почте</label>
                <input type="text" name="search" id="search" class="form-control"
                       placeholder="Введите имя или ИНН..." value="{{ request('search') }}">
            </div>

            <div class="col-md-2">
                <label for="dist" class="form-label">Фед. округ</label>
                <select class="form-select" id="dist" name="dist">
                    <option value="">Выберите</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="region" class="form-label">Регион</label>
                <select class="form-select" id="region" name="region">
                    <option value="">Выберите</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="city" class="form-label">Город</label>
                <select class="form-select" id="city" name="city">
                    <option value="">Выберите</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Поиск</button>
                <a href="{{ route('manufacture.index') }}" class="btn btn-outline-secondary w-100">Сброс</a>
            </div>

        </div>
    </form>

    <table class="table table-hover">
        <thead class="table-dark">
        <tr>
            <th>Имя</th>
            <th>Почта</th>
            <th>ИНН</th>
            <th>Сайт</th>
            <th>Регион</th>
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
                                    <li>
                                        <button type="button"
                                                class="dropdown-item copy-email-btn"
                                                data-email="{{ $email->email }}"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="right"
                                                title="Скопировано!">
                                            {{ $email->email }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </td>

                <td> {{$manufacture->inn ?? ""}} </td>
                <td> <a href="{{ $manufacture->web}}" target="_blank"> Сайт </a> </td>
                <td>{{ $manufacture->fedDistRegion?->name ?? "Без региона"}}</td>

                <td>
                    <span class="name-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $manufacture->adress_loading }}">
                        {{ Str::limit($manufacture->adress_loading, 15, '...') }}
                    </span>
                </td>

                <td>
                    <span class="name-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $manufacture->note }}">
                        {{ Str::limit($manufacture->note, 15, '...') }}
                    </span>
                </td>

                <td>
                    <a href="{{ route('manufacture.fullInformation', $manufacture->id) }}" class="btn btn-sm btn-outline-info">
                        Просмотр
                    </a>
                </td>

                <td>
                    <a href="{{ route('manufacture.show', $manufacture->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
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
        {{ $manufactures->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>

    <!-- Модальное окно для добавления нового производителя -->
    <div class="modal fade @if ($errors->any()) show d-block @endif" id="createNewManufacture" tabindex="-1" aria-labelledby="createNewManufactureLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Добавление нового производителя</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('manufacture.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Название</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name')}}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="web" class="form-label">Сайт</label>
                            <input type="text" name="web" class="form-control @error('web') is-invalid @enderror" value="{{ old('web')}}" required>
                            @error('web')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="inn" class="form-label">ИНН</label>
                            <input type="number" min="0" name="inn" class="form-control @error('inn') is-invalid @enderror" value="{{ old('inn')}}">
                            @error('inn')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email-адреса</label>
                            <input type="hidden" id="emails" name="emails">
                            <div id="email-list">
                                @if(isset($emails))
                                    @foreach($emails as $email)
                                        <div class="email-container d-flex align-items-center mb-2">
                                            <input type="email" class="form-control" name="emails[{{ $email->id }}]" value="{{ $email->email }}" required>
                                            <button type="button" class="btn btn-danger ms-2 delete-email" data-id="{{ $email->id }}">Удалить</button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="d-flex">
                                <input type="email" id="new-email" class="form-control me-2" placeholder="Введите новый email">
                                <button type="button" class="btn btn-warning" id="add-email">Добавить</button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="adress_loading" class="form-label">Адрес погрузки</label>
                            <textarea name="adress_loading" class="form-control @error('adress_loading') is-invalid @enderror" >
                                {{ old('adress_loading')}}
                            </textarea>
                            @error('adress_loading')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="createDist" class="form-label">Федеральный округ</label>
                            <select id="createDist" name="dist" class="form-control"></select>
                        </div>
                        <div class="mb-3">
                            <label for="createRegion" class="form-label">Регион</label>
                            <select id="createRegion" name="region" class="form-control" required></select>
                        </div>
                        <div class="mb-3">
                            <label for="createCity" class="form-label">Город</label>
                            <select id="createCity" name="city" class="form-control"></select>
                        </div>

                        <div class="mb-3">
                            <label for="note" class="form-label">Заметки</label>
                            <input type="text" name="note" class="form-control @error('note') is-invalid @enderror" value="{{ old('note')}}">
                            @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Ссылка на цены</label>
                            <input type="text" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price')}}">
                            @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <input type="checkbox" id="nottypicalproduct" name="nottypicalproduct" class="form-check-input @error('nottypicalproduct') is-invalid @enderror"
                                   value="1" {{ old('nottypicalproduct') ? 'checked' : '' }}>
                            <label for="nottypicalproduct" class="form-check-label">Не типовая продукция</label>
                            @error('nottypicalproduct')
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

                    if (type === 'dist' && selectedRegion) {
                        workWithFederalDist(selected, 'region', selectedCity);
                    }
                },
                error: function (response) {
                    console.error('Ошибка загрузки данных:', response);
                }
            });
        }

        function initCreateFormLocationSelects() {
            const createDist = $('#createDist');
            const createRegion = $('#createRegion');
            const createCity = $('#createCity');

            createDist.empty().append('<option value="">Выберите</option>');
            createRegion.empty().append('<option value="">Выберите</option>');
            createCity.empty().append('<option value="">Выберите</option>');

            $.ajax({
                url: '/federalDist/1',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                method: 'GET',
                success: function (response) {
                    response.federalDist.forEach(item => {
                        createDist.append(`<option value="${item.id}">${item.name}</option>`);
                    });
                }
            });

            createDist.change(function () {
                const distId = $(this).val();
                createRegion.empty().append('<option value="">Выберите</option>');
                createCity.empty().append('<option value="">Выберите</option>');

                if (!distId) return;

                $.ajax({
                    url: '/federalDist/' + distId,
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    method: 'GET',
                    success: function (response) {
                        response.federalDist.forEach(item => {
                            createRegion.append(`<option value="${item.id}">${item.name}</option>`);
                        });
                    }
                });
            });

            createRegion.change(function () {
                const regionId = $(this).val();
                createCity.empty().append('<option value="">Выберите</option>');

                if (!regionId) return;

                $.ajax({
                    url: '/federalDist/' + regionId,
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    method: 'GET',
                    success: function (response) {
                        response.federalDist.forEach(item => {
                            createCity.append(`<option value="${item.id}">${item.name}</option>`);
                        });
                    }
                });
            });
        }

        $(document).ready(function () {
            workWithFederalDist(1, 'createDist', selectedDist);
            if (selectedDist) workWithFederalDist(selectedDist, 'dist', selectedRegion);
            if (selectedRegion && selectedCity) workWithFederalDist(selectedRegion, 'region', selectedCity);

            $('#dist').change(function () { workWithFederalDist($(this).val(), 'dist'); });
            $('#region').change(function () { workWithFederalDist($(this).val(), 'region'); });

            $('#createNewManufacture').on('shown.bs.modal', function () {
                initCreateFormLocationSelects();
            });
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

        $(document).ready(function () {
            $('.copy-email-btn').each(function () {
                new bootstrap.Tooltip(this, {
                    trigger: 'manual' // отключаем hover/focus
                });
            });

            $('.copy-email-btn').on('click', function () {
                const btn = this;
                const email = $(btn).data('email');

                navigator.clipboard.writeText(email).then(function () {
                    const tooltip = bootstrap.Tooltip.getInstance(btn);
                    tooltip.setContent({ '.tooltip-inner': 'Скопировано!' });
                    tooltip.show();

                    setTimeout(function () {
                        tooltip.hide();
                    }, 3000);
                }).catch(function (err) {
                    console.error('Ошибка копирования: ', err);
                });
            });
        });
    </script>
@endsection
