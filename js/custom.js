$(document).ready(function () {

    $(document).on('click', '.btn', function (e) {
        e.preventDefault()

        $('#errors').empty()
        let errors = false;

        let name = $('#name').val()
        let phone = $('#phone').val()
        let email = $('#email').val()
        let email_form = /^[a-z0-9_-]+@[a-z0-9-]+\.[a-z]{2,6}$/i;
        let price = $('#price').val()

        //Валидация имени
        if (name.length < 2) {
            errors = true
            $('#name').removeClass('success').addClass('error')
        } else {
            $('#name').removeClass('error').addClass('success')
        }
        //Валидация телефона
        if (phone.length < 18) {
            errors = true
            $('#phone').removeClass('success').addClass('error')
        } else {
            $('#phone').removeClass('error').addClass('success')
        }

        //Валидация email
        if (email.search(email_form) == 0) {
            $('#email').removeClass('error').addClass('success')
        } else {
            errors = true
            $('#email').removeClass('success').addClass('error')
        }

        //Валидация товара
        if (price <= 0) {
            $('#errors').append('<div id="err_product"><p> • Не выбран товар</p></div>')
            errors = true
        }

        if (errors === false) {

            $.ajax({
                type: "POST",
                url: "/amo/add_lead.php",
                data: "name=" + name + "&phone=" + phone + "&email=" + email + "&price=" + price,

                cache: false,
                success: function (msg) {

                    if (msg == 200) {
                        $('.form').fadeOut();
                        $('#title').html('Заявка успешно отправлена!')
                    } else {
                        $('.form').fadeOut();
                        $('#title').html('Ошибка!')
                    }
                }
            });
        } else {
            $('#errors').append('<div id="err"><p>• Заполните все поля</p></div>')
        }


    });

});