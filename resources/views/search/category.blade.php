@extends('layouts.app')

@section('title', "Поиск по категориям")

@section('content')

    <div class="containerQuery mt-2">
        <div>
            <div class="form-group">
                <label for="query">Введите запрос</label>
                <input type="text" class="form-control" id="query" placeholder="Введите название категории">
            </div>

            <div class="d-flex justify-content-between">
                <div class="form-group select-container">
                    <label for="dist">Федеральный округ</label>
                    <select class="form-control" id="dist" name="dist">
                        <option value="">Выберите</option>
                    </select>
                </div>

                <div class="form-group select-container">
                    <label for="region">Регион</label>
                    <select class="form-control" id="region" name="region">
                        <option value="">Выберите</option>
                    </select>
                </div>

                <div class="form-group select-container">
                    <label for="city">Город</label>
                    <select class="form-control" id="city" name="city">
                        <option value="">Выберите</option>
                    </select>
                </div>
            </div>

            <button type="submit" id="search" class="btn btn-info btn-block mt-3 form-control">Поиск</button>
        </div>
    </div>

    <style>
        .containerQuery {
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 10px;
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px;
            font-size: 14px;
        }

        .btn-info {
            background-color: #5bc0de;
            border: none;
            color: white;
            border-radius: 8px;
            padding: 12px 20px;
        }

        .btn-info:hover {
            background-color: #31b0d5;
        }

        .d-flex {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .select-container {
            flex: 1;
            max-width: 33%;
        }

        .select-container select {
            width: 100%;
        }
    </style>
    <div id="answer"></div>

    {{--Модальное окно для просмотра продукции, которое производится этим производителем по этой категории--}}
    <div class="modal fade" id="showProductsByCategoryByManufacture" tabindex="-1" aria-labelledby="showProductsByCategoryByManufactureLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Продукция</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="productsShow"></div>
            </div>
        </div>
    </div>


    <script>
        let csrfToken = $('input[name="_token"]').val();

        function workWithFederalDist(parent_id, type) {
            let targetSelect = type === 'createDist' ? '#dist' : type === 'dist' ? '#region' : '#city';

            if (type === 'dist') {
                $('#region, #city').empty().append('<option value="">Выберите</option>');
            } else if (type === 'region') {
                $('#city').empty().append('<option value="null">Выберите</option>');
            }

            $.ajax({
                url: '/federalDist/' + parent_id,
                headers: { 'X-CSRF-TOKEN': csrfToken },
                method: 'GET',
                success: function (response) {
                    $(targetSelect).empty().append('<option value="">Выберите</option>');
                    response.federalDist.forEach(item => {
                        $(targetSelect).append(`<option value="${item.id}">${item.name}</option>`);
                    });
                },
                error: function (response) {
                    console.error('Ошибка загрузки данных:', response);
                }
            });
        }

        $(document).ready(function() {
            workWithFederalDist(1, 'createDist');

            $('#dist').change(function() { workWithFederalDist($(this).val(), 'dist'); });
            $('#region').change(function() { workWithFederalDist($(this).val(), 'region'); });


            $('#search').on('click', function (){
                $.ajax({
                    url: '/search/cfind',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        input: $('#query').val(),
                        dist: $('#dist').val(),
                        region: $('#region').val(),
                        city: $('#city').val(),
                    },
                    success: function(response) {
                        let table = '<table class="table table-hover"> <thead class="table-dark">' +
                            '<tr> <th>Категория</th> <th>Производитель</th> <th>Сайт</th> <th>Почта</th> <th>Город</th> <th>Комментарий</th> </tr> </thead> <tbody>';

                        Object.values(response.data).forEach(item => {
                            table += `<tr>`;

                            if (item.price_manufacture === null) {
                                table += `<td>${item.name_category}</td>`;
                            } else if (item.price_manufacture) {
                                table += `
                                        <td><button class="btn btn-outline-danger mb-3" data-bs-toggle="modal" data-id="${item.id_category}" data-bs-target="#showProductsByCategoryByManufacture">
                                            ${item.name_category}
                                        </button></td>`;
                            }

                            table += `
                                <td>${item.name_manufacture}</td>
                                <td><a href="${item.website}" class="btn btn-outline-info" target="_blank">Перейти</a></td>
                            `;

                            if (!item.emails || item.emails.length === 0) {
                                table += `
                                    <td>
                                        <a href="/manufacture/${item.id_manufacture}" class="btn btn-sm btn-outline-primary">
                                            Добавить
                                        </a>
                                    </td>
                                `;
                            } else {
                                table += `
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button"
                                                    id="emailDropdown${item.id_manufacture}" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                Почта
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="emailDropdown${item.id_manufacture}">
                                `;

                                Object.values(item.emails).forEach(el => {
                                    table += `<li><a class="dropdown-item" href="mailto:${el.email}">${el.email}</a></li>`;
                                });

                                table += `
                                            </ul>
                                        </div>
                                    </td>
                                `;
                            }

                            table += `<td>${Object.values(response.dist)[item.id_city_manufacture] ?? ''}</td>`;

                            table += `<td> ${item.comment_category ?? ""}</td>`;

                            table += `</tr>`;
                        });

                        table += '</tbody> </table>';

                        $("#answer").html(table);
                        // alert('Here');

                        console.log(response.dist[1])
                    },
                    error: function(xhr) {
                        console.error('Ошибка:', xhr);
                        alert('Ошибка при поиске');
                    }
                });
            });
        });

    </script>
@endsection
