<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand">Терминал ЖБИ</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="/manufacture">Производители</a>
                    </li>

                    @if(Auth::user()->role_id == 1)
                        <li class="nav-item">
                            <a class="nav-link" href="/admin">Админ панель</a>
                        </li>
                    @endif

                    @if(in_array(Auth::user()->role_id, [1, 2, 3, 4]))
                        <li class="nav-item">
                            <a class="nav-link" href="/search/category?pagination=30">Поиск по производителям</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="/search/product?pagination=30">Поиск продукции</a>
                        </li>
                    @endif

                    @if(in_array(Auth::user()->role_id, [1, 5]))
                        <li class="nav-item">
                            <a class="nav-link" href="/carrier">Выбор перевозчиков</a>
                        </li>
                    @endif

{{--                    @if(Auth::user()->role_id == 1)--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link" href="{{ route('edit.index.category') }}">Добавить категорию</a>--}}
{{--                        </li>--}}
{{--                    @endif--}}

                    @if(Auth::user()->role_id !== 6)
                        <li class="nav-item">
                            <a class="nav-link" href="/order">Заказы</a>
                        </li>
                    @endif


                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link">Выйти</button>
                        </form>
                    </li>
                @endauth

                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Войти</a>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    @yield('content')
</div>

</body>
</html>
