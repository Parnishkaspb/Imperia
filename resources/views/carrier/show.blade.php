@extends('layouts.app')

@section('title', 'Перевозчики')

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('carrier.update', $carriers->id)   }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="who" class="form-label">Кто выполняет перевозку</label>
            <input type="text" name="who" class="form-control @error('who') is-invalid @enderror" value="{{ old('who') ?? $carriers->who}}" required>
            @error('who')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="telephone" class="form-label">Телефон</label>
            <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror" value="{{ old('telephone') ?? $carriers->telephone}}">
            @error('telephone')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Почта</label>
            <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') ?? $carriers->email}}">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="note" class="form-label">Заметки</label>
            <textarea type="text" name="note" class="form-control @error('note') is-invalid @enderror">{{ old('note') ?? $carriers->note}}</textarea>
            @error('note')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="md-3">
            @foreach($types as $type)
                <input
                    type="checkbox"
                    class="btn-check"
                    name="type_cars[]"
                    id="{{ $type->id }}"
                    value="{{ $type->id }}"
                    {{ in_array($type->id, $carriers->type_car_id) ? 'checked' : '' }}
                    autocomplete="off"
                >
                <label class="btn mt-1 btn-outline-primary" for="{{ $type->id }}">
                    {{ $type->type }}
                </label>
            @endforeach
        </div>


        <button type="submit" class="btn mt-1 btn-outline-primary w-100">Редактировать</button>
    </form>
@endsection
