<!doctype html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bitter:ital,wght@0,400;1,100&display=swap" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script src="/js/phone.js"></script>
    <script src="/js/custom.js"></script>

    <title>Форма заявки</title>
</head>
<body>

<div id="container">
    <div id="title"><h2>Форма заявки</h2></div>
    <form class="form" action="/amo/amo_api.php">

        <div id="errors" class="errors">

        </div>

        <div class="form_box">
            <label for="name">Укажите ваше имя</label>
            <input id="name" name="name" value="" placeholder="Имя" required>
        </div>


        <div class="form_box">
            <label for="name">Укажите ваш телефон</label>
            <input id="phone" name="phone" data-tel-input type="tel" maxlength="18" value="" placeholder="Телефон" required>
        </div>


        <div class="form_box">
            <label for="name">Укажите ваш email</label>
            <input id="email" name="email" type="email" value="" placeholder="Email" required>
        </div>

        <div class="form_box">
            <label for="name">Товар 1, руб.</label>
            <input id="price" name="price" type="text" value="1500" placeholder="Цена" disabled>
        </div>

        <input class="btn" type="submit" value="Оставить заявку">
    </form>
</div>
</body>
</html>