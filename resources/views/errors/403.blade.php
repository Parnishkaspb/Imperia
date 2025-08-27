<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden</title>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #1a1a2e;
            color: white;
            text-align: center;
            padding: 50px 20px;
            margin: 0;
        }

        .lock-icon {
            font-size: 100px;
            animation: float 3s ease-in-out infinite;
            margin-bottom: 30px;
        }

        .error-code {
            font-size: 120px;
            font-weight: bold;
            color: #e94560;
            margin: 0;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.3);
        }

        .error-message {
            font-size: 24px;
            margin: 20px 0 40px;
            color: #ccc;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 15px 30px;
            border: 2px solid #e94560;
            background: transparent;
            color: white;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 16px;
        }

        .action-btn:hover {
            background: #e94560;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(233, 69, 96, 0.3);
        }

        .contact-info {
            margin-top: 50px;
            color: #888;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="lock-icon">🔒</div>
<h1 class="error-code">403</h1>
<p class="error-message">Доступ к запрашиваемому ресурсу запрещен</p>

<div class="actions">
{{--    <a href="/" class="action-btn">Главная страница</a>--}}
{{--    <a href="javascript:history.back()" class="action-btn">Вернуться назад</a>--}}
    <a href="mailto:admin@example.com" class="action-btn">Связаться с поддержкой</a>
</div>

<div class="contact-info">
    Если вы считаете, что видите это сообщение по ошибке,<br>
    пожалуйста, свяжитесь с администратором системы.
</div>
</body>
</html>
