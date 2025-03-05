@extends('layouts.app')

@section('title', 'Редактирование производителя')

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('manufacture.update', $manufacture->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Название</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $manufacture->name) }}" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="web" class="form-label">Сайт</label>
            <input type="url" name="web" class="form-control @error('web') is-invalid @enderror" value="{{ old('web', $manufacture->web) }}">
            @error('web')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Email-адреса</label>
            <div id="email-list">
                @foreach($manufacture->emails as $email)
                    <div class="email-container d-flex align-items-center mb-2">
                        <input type="email" class="form-control" name="emails[{{ $email->id }}]" value="{{ $email->email }}" required>
                        <button type="button" class="btn btn-danger ms-2 delete-email" data-id="{{ $email->id }}">Удалить</button>
                    </div>
                @endforeach
            </div>
            <div class="d-flex">
                <input type="email" id="new-email" class="form-control me-2" placeholder="Введите новый email">
                <button type="button" class="btn btn-warning" id="add-email">Добавить</button>
            </div>
        </div>

        <button type="submit" class="btn btn-outline-primary w-100">Редактировать</button>
    </form>

    <script>
        $(document).ready(function() {
            let csrfToken = $('input[name="_token"]').val();

            $(document).on('click', '.delete-email', function() {
                let emailId = $(this).data('id');
                if (!confirm("Вы точно хотите удалить почту?")) return;

                $.ajax({
                    url: `/email/${emailId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        if (response.success) {
                            $(`button[data-id='${emailId}']`).closest('.email-container').remove();
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Ошибка при удалении почты: ' + xhr.responseJSON.message);
                    }
                });
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
                    url: '/email/',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        email: newEmailValue,
                        manufacture_id: {{ $manufacture->id }}
                    },
                    success: function(response) {
                        if (response.success) {
                            let newEmailHtml = `
                                <div class="email-container d-flex align-items-center mb-2">
                                    <input type="email" class="form-control" name="emails[${response.email.id}]" value="${response.email.name}" required>
                                    <button type="button" class="btn btn-danger ms-2 delete-email" data-id="${response.email.id}">Удалить</button>
                                </div>`;
                            $('#email-list').append(newEmailHtml);
                            $('#new-email').val('');
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Ошибка при добавлении почты: ' + xhr.responseJSON.message);
                    }
                });
            });
        });
    </script>
@endsection
