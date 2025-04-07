@extends('layouts.app')

@section('title', 'Редактирование продукции')

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

    <form method="POST" action="{{ route('edit.update.product', $product->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Название продукции</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="length" class="form-label">Длина</label>
            <input type="text" name="length" class="form-control @error('length') is-invalid @enderror" value="{{ old('length', $product->length) }}">
            @error('length')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="width" class="form-label">Ширина</label>
            <input type="text" name="width" class="form-control @error('width') is-invalid @enderror" value="{{ old('width', $product->width) }}">
            @error('width')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="height" class="form-label">Высота</label>
            <input type="text" name="height" class="form-control @error('height') is-invalid @enderror" value="{{ old('height', $product->height) }}">
            @error('height')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="weight" class="form-label">Вес</label>
            <input type="text" name="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight', $product->weight) }}">
            @error('height')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="concrete_volume" class="form-label">Объем</label>
            <input type="text" name="concrete_volume" class="form-control @error('concrete_volume') is-invalid @enderror" value="{{ old('concrete_volume', $product->concrete_volume) }}">
            @error('concrete_volume')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-outline-primary w-100">Редактировать</button>
    </form>
@endsection

