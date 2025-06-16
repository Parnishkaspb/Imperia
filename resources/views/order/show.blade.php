@extends('layouts.app')

@section('title', 'Заказ: ' . $order->id)

@section('content')

    <div class="d-flex gap-2 align-items-center mb-3">
        <input type="number" min="0" value="{{ $order->amo_lead }}" class="amo_lead form-control w-25" onfocusout="update(this.value, {{ $order->amo_lead }}, 17)">
        <a href="{{ route('search.product', ['pagination' => 30, 'order_id' => $order->id]) }}" class="btn btn-warning w-50">Добавить к {{ $order->id }}</a>
        {{--    <button class="btn btn-danger" onclick="downloadFile({{ $order->id }})"> Для КП </button>--}}
        <select class="form-control w-25" onchange="update(this.value, '', 9)">
            @foreach ($statuses as $status)
                <option value="{{ $status->id }}" {{ $order->status_id === $status->id ? 'selected' : '' }}>
                    {{ $status->name }}
                </option>
            @endforeach
        </select>
    </div>



    <style>
        .input_number{
            width: 55px;
        }
        .money {
            width: 90px;
        }
        .amo_lead {
            width: 200px;
        }
    </style>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

{{--    {{print_r($uniqueCategories)}}--}}

    @foreach($uniqueCategories as $category)
        <h5>
            {{ $category->name }}
            <button onclick="copyText('{{ $category->name }}')">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-sm">
                    <path fill-rule="evenodd"
                          clip-rule="evenodd"
                          d="M12 4C10.8954 4 10 4.89543 10 6H14C14 4.89543 13.1046 4 12 4ZM8.53513 4C9.22675 2.8044 10.5194 2 12 2C13.4806 2 14.7733 2.8044 15.4649 4H17C18.6569 4 20 5.34315 20 7V19C20 20.6569 18.6569 22 17 22H7C5.34315 22 4 20.6569 4 19V7C4 5.34315 5.34315 4 7 4H8.53513ZM8 6H7C6.44772 6 6 6.44772 6 7V19C6 19.5523 6.44772 20 7 20H17C17.5523 20 18 19.5523 18 19V7C18 6.44772 17.5523 6 17 6H16C16 7.10457 15.1046 8 14 8H10C8.89543 8 8 7.10457 8 6Z"
                          fill="currentColor">
                    </path>
                </svg>
            </button>
        </h5>

        <table class="table table-hover">
            <thead class="table-dark">
            <tr>
                <th>Название</th>
                <th>Длина</th>
                <th>Ширина</th>
                <th>Высота</th>
                <th>Вес</th>
                <th>Объем</th>
                <th>Кол-во</th>
                <th>Цена закупки</th>
                <th>Цена реализации</th>
            </tr>
            </thead>
            <tbody>
            @foreach($products[$category->id] as $product)
                <tr>
                    <td>
                        {{ $product['name'] }}
                        @can('delete', $order)
                        <form action="{{ route('order.delete.sm', [$order->id, 16, $product['id']]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Удалить товар?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-trash" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1
                                    .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3
                                    .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0
                                          1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0
                                          1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0
                                          1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118
                                          4 4 4.059V13a1 1 0 0 0 1
                                          1h6a1 1 0 0 0 1-1V4.059L11.882
                                          4H4.118zM2.5 3V2h11v1h-11z"/>
                                </svg>
                            </button>
                        </form>
                        @endcan
                    </td>
                    <td>
                        {{ $product['length'] }}
                    </td>
                    <td>
                        {{ $product['width'] }}
                    </td>
                    <td>
                        {{ $product['height'] }}
                    </td>
                    <td>
                        {{ $product['weight'] }}кг
                    </td>
                    <td>
                        {{ $product['concrete_volume'] }}
                    </td>

                    <td>
                        <input type="number" class="input_number" @can('updatePrices', $order) onfocusout="update(this.value, {{ $product['id'] }}, 2)" @else disabled="disabled" @endcan min='1' value="{{$product['quantity']}}">
                    </td>

                    <td>
                        <input type="number" class="money" @can('updatePrices', $order) onfocusout="update(this.value, {{ $product['id'] }}, 3)" @else disabled="disabled" @endcan min='1' value="{{$product['buying_price']}}">
                        <br><input type="number" class="money" disabled="disabled" value="{{$product['buying_price'] * $product['quantity']}}">
                    </td>

                    <td>
                        <input type="number" class="money" @can('updatePrices', $order) onfocusout="update(this.value, {{ $product['id'] }}, 4)" @else disabled="disabled" @endcan min='1' value="{{$product['selling_price']}}">
                        <br><input type="number" class="money" disabled="disabled" value="{{$product['selling_price'] * $product['quantity']}}">
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>

    @endforeach


    <script>
        const csrfToken = $('input[name="_token"]').val();

        function copyText(text) {
            var tempInput = document.createElement("textarea");
            tempInput.value = text;
            document.body.appendChild(tempInput);

            tempInput.select();
            document.execCommand("copy");

            document.body.removeChild(tempInput);

            alert("Текст скопирован!!");
        }

        function update(value, product_id, what, id_update = ''){
            $.ajax({
                url: '/order/' + {{$order->id}},
                headers: { 'X-CSRF-TOKEN': csrfToken },
                type: "PUT",
                data: { value, product_id, id_update, what,},
                success: function (response){
                    // console.log(response);
                },
                error: function (){

                }
                // .done(function(data){
                //     if (what == 8){
                //         alert("Комментарий успешно добавлен");
                //     }
                //     if(what == 10){
                //         location.reload();
                //     }
                //
                //     // alert('/order_dd.php?id='+deal);
            });
        }
    </script>
@endsection
