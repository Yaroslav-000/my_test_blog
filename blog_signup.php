<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="/css/blog_global.css">
    <link rel="stylesheet" href="/css/blog_global_media.css">
    <link rel="stylesheet" href="/css/blog_header.css">
    <link rel="stylesheet" href="/css/blog_header_media.css">
    <link rel="stylesheet" href="/css/blog_sidbar.css">
    <link rel="stylesheet" href="/css/blog_sidbar_media.css">
    <link rel="stylesheet" href="/css/blog_signup.css">
    <link rel="icon" href="/img/favicon.png" type="image/x-icon">
</head>

<body>

    <?php
    include "blog_header.php";
    ?>
    <main>
        <?php
        include "blog_sidbar.php";
        
        $data = $_POST;

        // Проверка была ли отправленна форма авторизации
        if( isset($data['du_signup']) ) {

            // Создание массива для ошибок
            $errors = array();

            // Проверка введено ли имя
            if( trim( $data['name']) == '' ){

                $errors[] = 'Введите имя!';
            }

            // Обход массива $autho, в целях убедиться в уникальности имени нового пользователя
            foreach ($autho as &$name) {

                if( $name['name'] == $data['name'] ){

                    $errors[] = 'Такое имя уже есть!';

                }
            
            }

            // Проверка отсуцтвия аватара у нового пользователя
            if ( $_FILES['file_avatar']['name'] == '' ) {

                // Установка стандартного аватара для нового пользователя
                $avatar_name['avatar'] = 0;

            } else    

                // Проверка расширения файла
                if ( $_FILES['file_avatar']['type'] !=  "image/png" ) {
                    
                $errors[] = "Расщирение файла не png";

            } else 

                // Проверка размера файла
                if ( $_FILES['file_avatar']['size'] > 2097152 ) {

                    $errors[] = 'Размер файла больше 2 мегабайт';

            } else {

                // Определение номера аватара нового пользователя
                list($avatar_name) = array_slice( $autho, -1);
                $avatar_name['avatar'] = $avatar_name['avatar'] +1;
                
                // Сохранение файла
                move_uploaded_file( $_FILES['file_avatar']['tmp_name'], 
                'E:\PROGRAM\Vork\locHost\OSPanel\domains\blog\img\a'.$avatar_name['avatar'].'.png');
            }

            // Проверка, был ли введён первый пароль
            if( $data['password_1'] == '' ){

                $errors[] = 'Введите пароль!';
            }

            // проверка был ли введён второй пароль
            if( $data['password_2'] != $data['password_1'] ){

                $errors[] = 'Пароли должны совпадать!';
            }

            // Проверка на отсуцтвие ошибок
            if( empty($errors) ){
                
                // Задание переменных для SQL переменной
                $name = $data['name'];
                $avatar = $avatar_name['avatar'];
                $password = $data['password_1'];
                // $password = password_hash($password, PASSWORD_DEFAULT); Шифрование пароля

                // Создание SQL переменной для SQL запроса создания новой записи
                $sql = "INSERT INTO account (name, avatar, password) VALUES ('$name', ' $avatar', '$password')";
                
                // Совершение SQL запроса, и сразу проверка удачен ли он
                if (mysqli_query($connection, $sql)) {

                    // Вывод сообщения об успешной регистрации
                    echo '
                    <div class="flex cuc-regi">
                        Вы успешно <br> зарегестрировались!
                    </div>
                    ';

                    // Проверка была ли выбрана галочка "сразу авторизоваться"
                    if( $data['avto_autho'] == "Yes" ) {

                        // Запрос к БД аккаунтов и взятие из неё всех аккаунтов
                        $account = "SELECT * FROM account";
                        $author = mysqli_query($connection, $account);

                        // Цикл, переносящий всю БД аккаунтов в массив
                        while ($authors = mysqli_fetch_assoc($author)) {

                            $autho[$c] = $authors;
                        
                            $c++;
                        }

                        // Обход всего массива $autho, с целью найти вновь зарегестрированный аккаунт
                        // И занести в ссесию его данные
                        foreach ($autho as &$acco) {
                            
                            // Проверка являеться ли это массив нового пользователя
                            if( $acco[1] == $data[0] ){
                               
                                // Присвоение данных массива ссесии
                                $_SESSION['acount'] = $acco;

                                // Перенаправление в личный кабинет
                                echo '
                                <script> window.setTimeout(function() { window.location = "blog_my-page.php"; }, 2000) </script>
                                ';
                            } 
                        
                        }
                    }

                } else {

                    // Вывод ошибки SQL запроса 
                    echo '
                    <div class="flex errors">
                        '. "Error: " . $sql . "<br>" . mysqli_error($connection) .'
                    </div>
                    ';
                
                }

            } else {

                // Вывод ошибок при заполнении формы регистрации
                echo '
                <div class="flex errors">
                    '. array_shift($errors) .'
                </div>
                ';

            }

        }
        
        ?>

        <!-- HTML код формы регистрации -->
        <section class="flex regi">
            <h2 class="title regi-title">
                Регистрация
            </h2>
            <form class="flex regi__form" action="/blog_signup.php" method="post" enctype="multipart/form-data">
                <div class="flex regi-form__name">
                    <h3 class="regi-form-name__title">
                        Ваше имя
                    </h3>
                    <input class="input regi-form-name__inp" type="text" name="name" value="<?php echo
                    $data['name']; ?>">
                </div>
                <div class="flex regi-form__file">
                    <h3 class="regi-form-file__title">
                        Ваша аватарка
                    </h3>
                    <input class="input regi-form-file__inp" type="file" name="file_avatar" >
                </div>
                <div class="flex regi-form__pas-1">
                    <h3 class="regi-form-pas-1__title">
                        Ваш пароль
                    </h3>
                    <input class="input regi-form-pas-1__inp" type="password" name="password_1"  value="<?php echo
                    $data['password_1']; ?>">
                </div>
                <div class="flex regi-form__pas-2">
                    <h3 class="regi-form-pas-2__title">
                        Введите ваш пароль ещё раз
                    </h3>
                    <input class="input regi-form-pas-2__inp" type="password" name="password_2"  value="<?php echo
                    $data['password_2']; ?>">
                </div>
                <div class="flex regi-form__avto-autho">
                    <h3 class="regi-form-avto-autho__title">
                       Автоматически авторизоваться?
                    </h3>
                    <input type="checkbox" name="avto_autho" value="Yes" />
                </div>
                <div class="flex btn-reset regi-form__com">
                    <button class="regi-form-com__btn" name="du_signup" tyep="submit" >
                        Зарегестрироваться
                    </button>
                </div>
            </form>
        </section>
    </main>


</body>

</html>