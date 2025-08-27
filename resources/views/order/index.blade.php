@extends('layouts.app')

@section('title', 'Заказы')

@section('content')
{{--    @if (in_array(Auth::user()->role_id, [1, 5]))--}}
{{--        <button class="btn btn-outline-info mb-3" data-bs-toggle="modal" data-bs-target="#createNewCarrier">--}}
{{--            <h4>Перевозчики</h4>--}}
{{--        </button>--}}
{{--    @else--}}
{{--        <h4>Перевозчики</h4>--}}
{{--    @endif--}}

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(Auth::user()->role_id !== 4)
        <form action="{{ route('order.index') }}" method="GET" class="mb-4">
            <div class="row g-3 align-items-end">

                {{--            <div class="col-md-4">--}}
                {{--                <label for="search" class="form-label">Поиск по имени, телефону или почте</label>--}}
                {{--                <input type="text" name="search" id="search" class="form-control"--}}
                {{--                       placeholder="Введите имя, телефон или почту..." value="{{ request('search') }}">--}}
                {{--            </div>--}}

                <div class="col-md-6">
                    <label for="type_car" class="form-label">Ответственные</label>
                    <select class="form-select" id="user" name="user">
                        <option value=""> Все ответственные </option>
                        @foreach($users as $user_id => $user_full_name)
                            <option value="{{ $user_id }}" {{ (int) request('user') === (int) $user_id ? 'selected' : '' }}>
                                {{ $user_full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Поиск</button>
                    <a href="{{ route('order.index') }}" class="btn btn-outline-secondary w-100">Сброс</a>
                </div>

            </div>
        </form>
    @endif

    <table class="table table-hover">
        <thead class="table-dark">
        <tr>
            <th>ID заказа</th>
            <th>ID амо</th>
            @if(Auth::user()->role_id !== 4)
                <th>Ответственный</th>
            @endif
            <th>Дата создания</th>
            <th>Дата изменения</th>
            <th>Статус</th>
            <th>Комментарий</th>
        </tr>
        </thead>
        <tbody>
        @foreach($orders as $order)
            <tr>
                <td>
                    <a href="{{route('order.show', $order->id)}}" class="btn btn-sm mt-1 btn-outline-success" target="_blank"> {{ $order->id }} </a>
                </td>

                <td>
                    {{ ($order->amo_lead === 0) ? "" : $order->amo_lead }}
                </td>

                @if(Auth::user()->role_id !== 4)
                    <td>
                        {{ $users[$order->user_id] ?? "" }}
                    </td>
                @endif

                <td>
                    {{ $order->created_at }}
                </td>

                <td>
                    {{ $order->updated_at }}
                </td>

                <td>
                    {{ $order?->status->name }}
                </td>

                <td>
                    {{ $order->note }}
                </td>
{{--                <td>--}}
{{--                    @if (!empty($carrier->telephone))--}}
{{--                        <button type="button"--}}
{{--                                class="btn btn-sm btn-outline-success copy-email-btn"--}}
{{--                                data-email="{{ $carrier->telephone }}"--}}
{{--                                data-bs-toggle="tooltip"--}}
{{--                                data-bs-placement="right"--}}
{{--                                title="Скопировано!">--}}
{{--                            {{ $carrier->telephone }}--}}
{{--                        </button>--}}
{{--                    @else--}}
{{--                        <a href="{{ route('carrier.show', $carrier->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">--}}
{{--                            Добавить--}}
{{--                        </a>--}}
{{--                    @endif--}}
{{--                </td>--}}

{{--                <td>--}}

{{--                    @if (!empty($carrier->email))--}}
{{--                        <button type="button"--}}
{{--                                class="btn btn-sm btn-outline-success copy-email-btn"--}}
{{--                                data-email="{{ $carrier->email }}"--}}
{{--                                data-bs-toggle="tooltip"--}}
{{--                                data-bs-placement="right"--}}
{{--                                title="Скопировано!">--}}
{{--                            {{ $carrier->email }}--}}
{{--                        </button>--}}
{{--                    @else--}}
{{--                        <a href="{{ route('carrier.show', $carrier->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">--}}
{{--                            Добавить--}}
{{--                        </a>--}}
{{--                    @endif--}}
{{--                </td>--}}

{{--                <td>--}}
{{--                    {{ $carrier->note }}--}}
{{--                </td>--}}

{{--                <td>--}}
{{--                    <button type="button" data-id="{{$carrier->id}}" data-type="work" id="change" class="btn change btn-sm @if($carrier->isWorkEarly) btn-outline-success @else btn-outline-danger @endif"> @if($carrier->isWorkEarly) Работали ранее @else Не работали ранее @endif </button>--}}
{{--                    <button type="button" data-id="{{$carrier->id}}" data-type="docs" id="change" class="btn change btn-sm mt-1 @if($carrier->isDoc) btn-outline-success @else btn-outline-danger @endif"> @if($carrier->isWorkEarly) Договор заключен @else Договор не заключен @endif  </button>--}}
{{--                </td>--}}

{{--                <td>--}}
{{--                    <a href="{{ route('carrier.show', $carrier->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">--}}
{{--                        Редактировать--}}
{{--                    </a>--}}
{{--                    <form method="POST" action="{{ route('carrier.destroy', $carrier->id) }}" class="d-inline"--}}
{{--                          onsubmit="return confirm('Вы уверены, что хотите удалить эту информацию у перевозчика {{ $carrier->who }}? Данное удаление не удаляет полностью данного перевозчика');">--}}
{{--                        @csrf--}}
{{--                        @method('DELETE')--}}
{{--                        <button type="submit" class="btn btn-sm mt-1 btn-outline-danger">Удалить</button>--}}
{{--                    </form>--}}
{{--                </td>--}}
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-3">
        {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>

    <!-- Модальное окно для добавления нового перевозчика -->
{{--    <div class="modal fade @if ($errors->any()) show d-block @endif" id="createNewCarrier" tabindex="-1" aria-labelledby="createNewCarrierLabel" aria-hidden="true">--}}
{{--        <div class="modal-dialog">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header">--}}
{{--                    <h5 class="modal-title">Добавление нового перевозчика</h5>--}}
{{--                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>--}}
{{--                </div>--}}
{{--                <div class="modal-body">--}}
{{--                    <form method="POST" action="{{ route('carrier.store') }}">--}}
{{--                        @csrf--}}
{{--                        <div class="mb-3">--}}
{{--                            <label for="who" class="form-label">Кто выполняет перевозку</label>--}}
{{--                            <input type="text" name="who" class="form-control @error('who') is-invalid @enderror" value="{{ old('who')}}" required>--}}
{{--                            @error('who')--}}
{{--                            <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                            @enderror--}}
{{--                        </div>--}}

{{--                        <div class="mb-3">--}}
{{--                            <label for="telephone" class="form-label">Телефон</label>--}}
{{--                            <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror" value="{{ old('telephone')}}">--}}
{{--                            @error('telephone')--}}
{{--                            <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                            @enderror--}}
{{--                        </div>--}}

{{--                        <div class="mb-3">--}}
{{--                            <label for="email" class="form-label">Почта</label>--}}
{{--                            <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email')}}">--}}
{{--                            @error('email')--}}
{{--                            <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                            @enderror--}}
{{--                        </div>--}}

{{--                        <div class="mb-3">--}}
{{--                            <label for="note" class="form-label">Заметки</label>--}}
{{--                            <textarea type="text" name="note" class="form-control @error('note') is-invalid @enderror">{{ old('note')}}</textarea>--}}
{{--                                @error('note')--}}
{{--                            <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                            @enderror--}}
{{--                        </div>--}}

{{--                        <div class="md-3">--}}
{{--                            @foreach($types as $type)--}}
{{--                                <input--}}
{{--                                    type="checkbox"--}}
{{--                                    class="btn-check"--}}
{{--                                    name="type_cars[]"--}}
{{--                                    id="{{ $type->id }}"--}}
{{--                                    value="{{ $type->id }}"--}}
{{--                                    autocomplete="off"--}}
{{--                                >--}}
{{--                                <label class="btn mt-1 btn-outline-primary" for="{{ $type->id }}">--}}
{{--                                    {{ $type->type }}--}}
{{--                                </label>--}}
{{--                            @endforeach--}}
{{--                        </div>--}}


{{--                        <button type="submit" class="btn mt-1 btn-outline-primary w-100">Создать</button>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <script>--}}
{{--        const csrfToken = $('input[name="_token"]').val();--}}

{{--        $(document).ready(function() {--}}
{{--            $(document).on('click', '.change', function() {--}}
{{--                let carrierId = $(this).data('id');--}}
{{--                let type = $(this).data('type');--}}

{{--                $.ajax({--}}
{{--                    url: '/carrier/'+carrierId+'/'+type,--}}
{{--                    method: "PUT",--}}
{{--                    headers: {--}}
{{--                        'X-CSRF-TOKEN': csrfToken--}}
{{--                    },--}}
{{--                    success: function (response){--}}
{{--                        alert(response.message + '\nДанные появятся после обновления страницы')--}}
{{--                    }--}}
{{--                });--}}
{{--            });--}}
{{--        });--}}

{{--        $(document).ready(function () {--}}
{{--            $('.copy-email-btn').each(function () {--}}
{{--                new bootstrap.Tooltip(this, {--}}
{{--                    trigger: 'manual'--}}
{{--                });--}}
{{--            });--}}

{{--            $('.copy-email-btn').on('click', function () {--}}
{{--                const btn = this;--}}
{{--                const email = $(btn).data('email');--}}

{{--                const tempInput = $('<input>');--}}
{{--                $('body').append(tempInput);--}}
{{--                tempInput.val(email).select();--}}

{{--                try {--}}
{{--                    const successful = document.execCommand('copy');--}}
{{--                    if (successful) {--}}
{{--                        const tooltip = bootstrap.Tooltip.getInstance(btn);--}}
{{--                        tooltip.setContent({ '.tooltip-inner': 'Скопировано!' });--}}
{{--                        tooltip.show();--}}
{{--                        setTimeout(() => tooltip.hide(), 3000);--}}
{{--                    } else {--}}
{{--                        console.error('Копирование не удалось');--}}
{{--                    }--}}
{{--                } catch (err) {--}}
{{--                    console.error('Ошибка копирования:', err);--}}
{{--                }--}}

{{--                tempInput.remove();--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}
@endsection
