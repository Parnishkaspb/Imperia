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
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endforeach
    @endif
    <form method="POST" action="{{ route('manufacture.update', $manufacture->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Название</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') ?? $manufacture->name }}" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="web" class="form-label">Сайт</label>
            <input type="url" name="web" class="form-control @error('web') is-invalid @enderror"
                   value="{{ old('web') ?? $manufacture->web }}">
            @error('web')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            @if(!empty($manufacture->emails))
                @foreach($manufacture->emails as $email)
                    <input type="email" name="email_{{ $email->id }}" value="{{ $email->name }}">
                    <button class="btn btn-outline-danger" onclick="deleteEmail({{ $email->id }})">Удалить</button>
                @endforeach
                <input type="email" name="email_{{$manufacture->emails->last() + 1}}" value="">
                <button class="btn btn-warning" onclick="createEmail()"> Добавить </button>
            @else
                <input type="email" name="email_1" value="">
                <button class="btn btn-warning" onclick="createEmail()"> Добавить </button>
            @endif
        </div>


        <button type="submit" class="btn btn-outline-primary w-100">Редактировать</button>
    </form>

    <script>
        $(document).ready(function() {
            let emailsArray =
            function deleteEmail(email_id) {
                if (confirm("Вы точно хотите удалить почту?")) {
                    $.ajax({
                        url: '/email/delete/' + email_id,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $(`input[name="email_${email_id}"]`).next('button').remove();
                            $(`input[name="email_${email_id}"]`).remove();
                            alert(response.message);
                        },
                        error: function(xhr) {
                            alert('Error deleting email: ' + xhr.responseJSON.message);
                        }
                    });
                }
            }

            const createEmail = () = {

            };
        });
    </script>
@endsection
