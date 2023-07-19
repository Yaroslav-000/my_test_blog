<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link rel="stylesheet" href="/css/blog_global.css">
    <link rel="stylesheet" href="/css/blog_global_media.css">
    <link rel="stylesheet" href="/css/blog_header.css">
    <link rel="stylesheet" href="/css/blog_header_media.css">
    <link rel="stylesheet" href="/css/blog_sidbar.css">
    <link rel="stylesheet" href="/css/blog_sidbar_media.css">
    <link rel="stylesheet" href="/css/blog_login.css">
    <link rel="icon" href="/img/favicon.png" type="image/x-icon">
</head>

<body>
    <?php

    // Подключение файла blog_header.php
    include "blog_header.php";
    ?>
    <main>
        <?php

        // Подключение файла blog_sidbar.php
        include "blog_sidbar.php";

        $data = $_POST;

        // Проверка отправлена ли форма
        if( isset($data['du_login']) ) {

            // Создание массива для ошибок
            $errors = array();

            $UID = 0;

            // Проверка введино ли имя
            if( trim( $data['name']) == '' ){

                // Запись о том что не введено имя
                $errors[] = 'Введите имя!';
            }

            // Проверка введён ли пароль
            if( $data['password'] == '' ){

                // Запись о том что не введён пароль
                $errors[] = 'Введите пароль!';
            }

            // Обход всего цикла $acco
            foreach ($autho as &$acco) {
                
                // Проверка соотвецтвия имени из БД с введёным именем
                if( $acco['name'] == $data['name'] ){
                    
                    // Проверка совпадают ли пароль из БД с введёным паролем
                    if( $acco['password'] == $data['password'] ){
                    
                       $UID = $acco['id'];
                       $log = 1;

                    } else {

                        // Запись о том что пароль введён не верно
                        $errors[] = 'Пароль введён не верно.';
                    } 

                } else {

                    // Запись о том что пользователя с таким именем не существует
                    $errors[] = 'Такого пользователя не существует.';

                }
            
            }

            // Проверка, успешна ли аутинтификация
            if( $log == 0 ){
                
                // Ввывод ошибок при заполнении формы
                echo '
                <div class="flex errors">
                    '. array_shift($errors) .'
                </div>
                ';

            }else {

                // Обход массива $acco
                foreach ($autho as &$acco) {
                    
                    // Проверка соотвецтвия UID введёного пользвателя с UID в массиве
                    if( $acco['id'] == $UID ){
                        
                        // Запись в ссесию данных аккаунта
                        $_SESSION['acount'] = $acco;
                        
                        // Проверка нужно ли запоминать пользователя
                        if( $data['remember'] == 'Yes' ){
                            
                            // Запись в ссесию что пользователя нужно запомнить
                            $_SESSION['acount']['remember'] = 'Yes';
                            
                        }else{

                            // Запись в ссесию что пользователя не нужно запоминать
                            $_SESSION['acount']['remember'] = 'No';

                        }

                    } 
                
                }
                
                    // Оповещение об удачной авторизации, и перенаправление на страницу профиля
                    echo '
                    <div class="flex cuc-regi">
                        Вы успешно <br> авторизовались!
                    </div>
                    <script> window.setTimeout(function() { window.location = "blog_my-page.php"; }, 2000) </script>
                    '; 
            }
        }            
       ?>
        <!-- HTML код вормы авторизации -->
        <section class="flex log">
            <h2 class="title log-title">
                Авторизация
            </h2>
            <form class="flex log__form" action="/blog_login.php?log=<?php echo $log ?>" method="post">
                <div class="flex log-form__name">
                    <h3 class="log-form-name__title">
                        Ваше имя
                    </h3>
                    <input class="input log-form-name__inp" type="text" name="name" value="<?php echo
                    $data['name']; ?>">
                </div>
                <div class="flex log-form__pas">
                <h3 class="log-form-pas__title">
                        Ваш пароль
                    </h3>
                    <input class="input log-form-pas__inp" type="password" name="password"  value="<?php echo
                    $data['password']; ?>">
                </div>
                <div class="flex log-form__remember">
                    <h3 class="log-form-remember__title">
                       Запомнить меня
                    </h3>
                    <input type="checkbox" name="remember" value="Yes" />
                </div>
                <div class="flex btn-reset log-form__com">
                    <button class="log-form-com__btn" name="du_login" tyep="submit" >
                       Авторизоваться
                    </button>
                </div>
            </form>
        </section>
    </main>


</body>

</html>