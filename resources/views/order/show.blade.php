@extends('layouts.app')

@section('title', 'Заказ: ' . $order->id)

@section('content')

    <div class="d-flex gap-2 align-items-center mb-3">
        <input type="number" min="0" value="{{ $order->amo_lead }}" class="amo_lead form-control w-25" onfocusout="update(this.value, {{ $order->amo_lead ?? 0 }}, 17)">
        @if(Auth::user()->id === $order->user_id)
            <a href="{{ route('search.product', ['pagination' => 30, 'order_id' => $order->id]) }}" target="_blank" class="btn btn-warning w-50">Добавить продукцию к {{ $order->id }}</a>

        @endif
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

    <?php
        $totalBuy = $moneyDelivery["buying_sum"] ?? 0;
        $totalSell = $moneyDelivery["selling_sum"] ?? 0;
        $totalWeight = 0;
    ?>

    @foreach($uniqueCategories as $category)
        <h5>
            <a href="{{ route('search.category', ['pagination' => 30, 'search' => $category->name, 'order_id' => $order->id]) }}" target="_blank" class="btn btn-outline-success"> {{ $category->name }}</a>
            <button onclick="copyText('{{ $category->name }}')" class="btn btn-sm btn-outline-primary">
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
            <thead class="table-light">
            <tr>
                <th>Название</th>
                <th>Длина</th>
                <th>Ширина</th>
                <th>Высота</th>
                <th>Вес</th>
                <th>Общий вес</th>
                <th>Объем</th>
                <th>Кол-во</th>
                <th>Цена закупки</th>
                <th>Цена реализации</th>
                @isset($manufactures["th"][$category->id])
                @foreach($manufactures["th"][$category->id] as $manufacture_id => $value)
                    <th>
                        {{ $value['name'] }}
                        <div class="b-popup" id="popup_{{ $manufacture_id }}">
                            <div class="b-popup-content">
                                @foreach($value['emails'] as $email)
                                    <p> email: {{ $email }} </p>
                                @endforeach
                                <a href="javascript:closePopUp({{ $manufacture_id }})">Скрыть</a>
                            </div>
                        </div>
                        <span onclick="showPopUp({{ $manufacture_id }})">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 50 50">
                                <path d="M25,2C12.297,2,2,12.297,2,25s10.297,23,23,23s23-10.297,23-23S37.703,2,25,2z M25,11c1.657,0,3,1.343,3,3s-1.343,3-3,3 s-3-1.343-3-3S23.343,11,25,11z M29,38h-2h-4h-2v-2h2V23h-2v-2h2h4v2v13h2V38z"></path>
                            </svg>
                        </span>

                        <form action="{{ route('order.delete.sm', [$order->id, 18, $value['delete_id']]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Удалить производителя?');">
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
{{--                        <span onclick="return delete_sm({{ $manufacture['id_update'] }}, {{ $manufacture['id_update'] }}, 18);">--}}
{{--                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">--}}
{{--                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>--}}
{{--                                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>--}}
{{--                                </span>--}}
                    </th>
                @endforeach
                @endisset
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
                        {{ $product['weight'] * $product['quantity'] }}кг
                        <br>
                        {{ ($product['weight'] * $product['quantity']) / 20000 }} -- Кол-во фур 20т
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

                    @isset($manufactures["body"][$category->id][$product['id']])
                        @foreach($manufactures["body"][$category->id][$product['id']] as $manufacture_id => $value)
                            <td>
                                <div>
                                    <p style="margin: 0px;"> Цена: </p> <input type='number' onfocusout="update(this.value, {{ $product['id'] }}, 19, {{ $manufacture_id }})" min='-1' value="{{ $value['price'] }}">
                                </div>
                                <div>
                                    <p style="margin: 0px;"> Комментарий: </p> <input type="text" onfocusout="update(this.value, {{ $product['id'] }}, 20, {{ $manufacture_id }})" value="{{ $value['comment'] }}">
                                </div>
                            </td>
                        @endforeach
                    @endisset

                        <?php
                        $totalBuy += $product['buying_price'] * $product['quantity'];
                        $totalSell += $product['selling_price'] * $product['quantity'];
                        $totalWeight += $product['weight'] * $product['quantity'];
                        ?>
                </tr>
            @endforeach
            </tbody>
        </table>

    @endforeach

    <table class='table' style="width: 30%; text-align: center;">
        <thead class='table-light'>
        <tr>
            <td>Цена Закупки</td>
            <td>Цена Реализации</td>
            <td>Заработок с заказа</td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{ $totalBuy }}</td>
            <td>{{ $totalSell }}</td>
            <td
                @if ($totalSell - $totalBuy >= 0) style="color: #009a63" @else  style="color: red" @endif >
                {{ $totalSell - $totalBuy }}/
                @php
                    if ($totalBuy != 0) {
                        $percent = round((($totalSell - $totalBuy) / $totalBuy) * 100, 2);
                    } else {
                        $percent = 0;
                    }
                @endphp
                {{ $percent }}%
            </td>
        </tr>
        </tbody>
    </table>

    <table class='table' style="width: 30%; text-align: center;">
        <thead class='table-light'>
        <tr>
            <td>Информация</td>
            <td>О заказе</td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td></td>
            <td>
                Общий вес: {{ $totalWeight }}
                <br>Количество 20т машин - {{ round($totalWeight / 20000) }}
                <br>Количество 10т машин - {{ round($totalWeight / 10000) }}
            </td>

        </tr>
        </tbody>
    </table>

{{--    {{ print_r($deliveries) }}--}}

    <table class='table' style="width: 60%; text-align: center;">
        <thead class='table-light'>
        <tr>
            <td>
                <button type="button" class="btn btn-sm btn-outline-warning" onclick="update({{ $order->id }}, {{ $order->id }}, 10)">
                     Добавить доставку
                </button>
            </td>
            <td>Откуда</td>
            <td>Куда</td>
            <td>Закупка</td>
            <td>Реализация</td>
            <td>Кол-во</td>
            <td>Удалить</td>
        </tr>
        </thead>
        <tbody>
        @foreach($deliveries as $delivery)
            <tr>
                <td></td>
                <td><input type="text" onfocusout="update(this.value, 1, 11, {{$delivery->id}})" value="{{$delivery->from}}"></td>
                <td><input type="text" onfocusout="update(this.value, 1, 12, {{$delivery->id}})" value="{{$delivery->to}}"></td>
                <td><input type="number"  class="money" onfocusout="update(this.value, 1, 13, {{$delivery->id}})" value="{{$delivery->buying_price}}"></td>
                <td><input type="number"  class="money" onfocusout="update(this.value, 1, 14, {{$delivery->id}})" value="{{$delivery->selling_price}}"></td>
                <td><input type="number" min="1" class="input_number" onfocusout="update(this.value, 1, 15, {{$delivery->id}})" value="{{$delivery->count}}"></td>
                <td>
                    <form action="{{ route('order.delete.sm', [$order->id, 20, $delivery->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Удалить доставку?');">
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
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <script>
        const csrfToken = $('input[name="_token"]').val();

        $(document).ready(function(){
            $(".b-popup").hide();
        });

        function copyText(text) {
            var tempInput = document.createElement("textarea");
            tempInput.value = text;
            document.body.appendChild(tempInput);

            tempInput.select();
            document.execCommand("copy");

            document.body.removeChild(tempInput);

            alert("Текст скопирован!!");
        }

        function update(value, product_id, what, update_id = ''){
            $.ajax({
                url: '/order/' + {{$order->id}},
                headers: { 'X-CSRF-TOKEN': csrfToken },
                type: "PUT",
                data: { value, product_id, update_id, what,},
                success: function (response){
                    switch (what){
                        case 10:
                            location.reload()
                            break;
                    }
                },
                error: function (){

                }
            });
        }

        function showPopUp(id_element){
            $("#popup_"+id_element).show();
        }

        function closePopUp(id_element){
            $("#popup_"+id_element).hide();
        }
    </script>
@endsection
