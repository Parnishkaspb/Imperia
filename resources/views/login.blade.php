<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Империя</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100 bg-light text-dark">

<div class="d-flex align-items-center justify-content-center flex-grow-1">
    <main class="card shadow-lg rounded-lg p-4 bg-white w-100" style="max-width: 400px;">
        <div class="card-body">
            <h1 class="text-center fw-semibold mb-4">Вход</h1>

            <form method="post" action="/login">
                @csrf
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Логин" name="email" value="{{ old('email') }}" required>
                    @error('email')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" placeholder="Пароль" name="password" required>
                    @error('password')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-100">Войти</button>
            </form>
        </div>
    </main>
</div>

</body>
</html>
