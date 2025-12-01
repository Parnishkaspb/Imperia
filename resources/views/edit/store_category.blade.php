{{-- create.blade.php --}}
@extends('layouts.app')

@section('title', 'Создание категории')

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('edit.store.category') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Название категории</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', '') }}" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-outline-primary w-100">Создать</button>
    </form>
@endsection
