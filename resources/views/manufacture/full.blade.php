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

    <!-- Кнопки для открытия информации в производители -->
    <div class="d-flex justify-content-center align-items-center mb-3">
        <table>
            <thead>
                <tr>
                    <th>
                        <a class="btn btn-outline-primary add-categories" style="width: 190px;" href="{{ route('manufacture.add', ['manufacture' => $manufacture->id, 'section' => 1]) }}"> Добавить категорию </a>
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
                    <a class="btn btn-outline-primary add-products" style="width: 190px;" href="{{ route('manufacture.add', ['manufacture' => $manufacture->id, 'section' => 2]) }}"> Добавить продукцию </a>
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
                <th>Избранное</th>
                <th>Действие</th>
            </tr>
            </thead>
            <tbody>
            @foreach($manufacture->categories as $category)
                <tr>
                    <td> {{ $category->category->name }} </td>
                    <td id="td_comment_{{$category->id}}"> {{ $category->comment}} </td>
                    <td>
                        <button class="btn btn-sm btn-outline-{{($category->likethiscategory) ? "warning" : "danger" }}" id="yesno" data-name="category" data-id="{{$category->id}}">
                            {{ $category->likethiscategory ? "Да" : "Нет" }}
                        </button>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('manufacture.pc.delete', [$category->id, 'category']) }}" class="d-inline"
                              onsubmit="return confirm('Вы уверены, что хотите удалить эту связку?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                        </form>

                        <button class="btn btn-sm btn-outline-primary mb-3 mt-1" id="addComment" data-bs-toggle="modal" data-id="{{$category->id}}" data-bs-target="#createNewComment">
                            Комментарий
                        </button>
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
                    <td>
                        <button class="btn btn-sm btn-outline-{{($product->doit) ? "warning" : "danger" }}" id="yesno" data-name="product" data-id="{{$product->id}}">
                            {{ $product->doit ? "Да" : "Нет" }}
                        </button>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('manufacture.pc.delete', [$product->id, 'product']) }}" class="d-inline"
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

    <div>
        <button class="btn btn-outline-primary mb-3" data-bs-toggle="modal" data-bs-target="#createNewContactPerson">
            Новое контактное лицо
        </button>
        <table class="table table-hover">
            <thead class="table-dark">
            <tr>
                <th>Кто</th>
                <th>Должность</th>
                <th>Телефон</th>
                <th>Почта</th>
                <th>Действие</th>
            </tr>
            </thead>
            <tbody>
            @foreach($manufacture->contacts as $contact)
                <tr>
                    <td id="td_name_{{$contact->id}}"> {{ in_array($contact->name, ['.', ',', '+', '-', '']) ? "Уточнить" : $contact->name}} </td>
                    <td id="td_position_{{$contact->id}}">  {{ in_array($contact->position, ['.', ',', '+', '-', '']) ? "Уточнить" : $contact->position}} </td>
                    <td id="td_phone_{{$contact->id}}">  {{ in_array($contact->phone, ['.', ',', '+', '-', '']) ? "Уточнить" : $contact->phone}} </td>
                    <td id="td_email_{{$contact->id}}"> {{ in_array($contact->email, ['.', ',', '+', '-', '']) ? "Уточнить" : $contact->email}} </td>
                    <td>
                        <a href="{{ route('manufacture.fullInformation', ['manufacture' => $manufacture->id, 'editContact' => $contact->id]) }}"
                           class="btn btn-sm btn-outline-warning">Редактировать</a>
                        <form method="POST" action="{{ route('manufacture.contact.delete', [$contact->id]) }}" class="d-inline"
                              onsubmit="return confirm('Вы уверены, что хотите удалить это контактную информацию?');">
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

    @php $isEdit = isset($editContact); @endphp

        <!-- Модалка создания/редактирования контактного лица -->
    <div class="modal fade" id="{{ $isEdit ? 'editContactModal' : 'createNewContactPerson' }}" tabindex="-1" aria-labelledby="{{ $isEdit ? 'editContactModalLabel' : 'createNewContactPersonLabel' }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $isEdit ? 'Редактирование контактного лица' : 'Добавление новой контактной информации' }}
                    </h5>
                    <a href="{{ route('manufacture.fullInformation', $manufacture->id) }}" class="btn-close" aria-label="Close"> </a>
{{--                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>--}}
                </div>
                <div class="modal-body">
                    @if ($errors->hasBag($isEdit ? 'editContact' : 'createContact'))
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->getBag($isEdit ? 'editContact' : 'createContact')->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ $isEdit ? route('manufacture.contact.update', $editContact->id) : route('manufacture.contact.store') }}">
                        @csrf
                        @if ($isEdit)
                            @method('PUT')
                        @endif

                        <input type="hidden" name="manufacture_id" value="{{ $manufacture->id }}">

                        <div class="mb-3">
                            <label class="form-label">Имя</label>
                            <input type="text" name="name" class="form-control @error('name', $isEdit ? 'editContact' : 'createContact') is-invalid @enderror" value="{{ old('name', $editContact->name ?? '') }}" required>
                            @error('name', $isEdit ? 'editContact' : 'createContact')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Должность</label>
                            <input type="text" name="position" class="form-control @error('position', $isEdit ? 'editContact' : 'createContact') is-invalid @enderror" value="{{ old('position', $editContact->position ?? '') }}">
                            @error('position', $isEdit ? 'editContact' : 'createContact')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Телефон</label>
                            <input type="text" name="phone" class="form-control @error('phone', $isEdit ? 'editContact' : 'createContact') is-invalid @enderror" value="{{ old('phone', $editContact->phone ?? '') }}">
                            @error('phone', $isEdit ? 'editContact' : 'createContact')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Почта</label>
                            <input type="email" name="email" class="form-control @error('email', $isEdit ? 'editContact' : 'createContact') is-invalid @enderror" value="{{ old('email', $editContact->email ?? '') }}" required>
                            @error('email', $isEdit ? 'editContact' : 'createContact')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-outline-primary w-100">
                            {{ $isEdit ? 'Сохранить' : 'Создать' }}
                        </button>
                    </form>
                </div>
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
        window.addEventListener('DOMContentLoaded', function () {

            @if ($errors->hasBag('createContact'))
            const contactModal = new bootstrap.Modal(document.getElementById('createNewContactPerson'));
            contactModal.show();
            @endif

            @if (request()->has('editContact'))
            const editModal = new bootstrap.Modal(document.getElementById('editContactModal'));
            editModal.show();
            @endif
        });
    </script>

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
                }
                $('#categories').fadeOut(200, function () {
                    $('#products').fadeIn(200);
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

            $(document).on('click', '#addComment', function() {
                let id = $(this).data('id');
                $('#newCommentID').val(id);
                let comment = $('#td_comment_' + id).text();
                $('#newCommentText').text(comment);
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
                        $('#td_comment_' + id).text(comment)
                    },
                    error: function(xhr) {
                        alert('Ошибка: ' + xhr.response.message);
                    }
                });
            });
        });

    </script>
@endsection
