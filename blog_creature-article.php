<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание статьи</title>
    <link rel="stylesheet" href="/css/blog_global.css">
    <link rel="stylesheet" href="/css/blog_global_media.css">
    <link rel="stylesheet" href="/css/blog_header.css">
    <link rel="stylesheet" href="/css/blog_header_media.css">
    <link rel="stylesheet" href="/css/blog_sidbar.css">
    <link rel="stylesheet" href="/css/blog_sidbar_media.css">
    <link rel="stylesheet" href="/css/blog_creature-article.css">
    <link rel="icon" href="/img/favicon.png" type="image/x-icon">
</head>

<body>
    <?php

    // Подключение файла blog_header.php
    include "blog_header.php";

    // Автоматическое перенаправление на главную страницу если пользователь не в аккаунте
    if( !isset($_SESSION['acount']['id'])){
        echo'
        <script> window.setTimeout(function() { window.location = "blog_main.php"; }, 0) </script>
        ';
    }
    ?>
    <main>
        <?php

        // Подключение файла blog_sidbar.php
        include "blog_sidbar.php";
        
        // Перенос данных из  переменной $_POST, в переменную $data
        $data = $_POST;

        // Проверка, была ли отправленна форма
        if( isset($data['du_creature']) ) {

            // Создание массива для ошибок
            $errors = array();

            // Проверка выбрана ли категория статьи
            if( trim( $data['categ']) == 'none' ){

                // Запись что не выбранна категория 
                $errors[] = 'Выберите категорию';
            }

            // Проверка на наличие названия 
            if( trim( $data['title']) == '' ){

                // Запись что не введено название
                $errors[] = 'Введите название!';
            }

            // Проверка что название длиннее 40 символов
            if(  mb_strlen( $data['title']) > 30 ){

                // Запись что название длиннее 40 символов
                $errors[] = 'Название длинее  40 символов';
            }

            // Проверки что есть добавленный файл и что он расширения png и что он меньше 2 мегабайт
            // Иначе запись о не выполнении одного из условий
            if ( $_FILES['file_picture']['name'] == '' ) {
                $errors[] = 'Добавте картинку!';
            } else    
                if ( $_FILES['file_picture']['type'] !=  "image/png" ) {
                $errors[] = 'Расщирение файла не png';
            } else 
                if ( $_FILES['file_picture']['size'] > 5242880 ) {
                    $errors[] = 'Размер файла больше 2 мегабайт';
            } 

            // Проверка на наличие текста 
            if( trim( $data['text']) == '' ){

                // Запись о отсуцтвии текста
                $errors[] = 'Введите текст!';
            }

            // Проверка длинее ли текст 1000 символов
            if(  mb_strlen( $data['text']) > 1000 ){

                // Запись о том что текст длинее 1000 символов
                $errors[] = 'Текст длинее 1000 символов';
            }

            // Проверка на отсуцтвие ошибок
            if( empty($errors) ){
                
                $pict = 0;

                // Проверка какие индексы картинок существуют
                // И присвоение новой картинке индекса, самая большая +1
                foreach ($arti as &$pic) {
                    if( $pic['image'] > $pict ){
                        
                        $pict = $pic['image'];
                    }            
                }
                $pict++;

                // Задание переменных для SQL запроса
                $categoris_id = $data['categ'];
                $title = $data['title'];
                $picture = $pict;
                $text = $data['text'];
                $author = $_SESSION['acount']['id'];
               
                // Сохранение картинки на "Сервер" 
                move_uploaded_file( $_FILES['file_picture']['tmp_name'], 
                'E:\PROGRAM\Vork\locHost\OSPanel\domains\blog\img\s'.$picture.'.png');

                // Создание SQL запроса к БД и создание новой записи со статьёй 
                $sql = "INSERT INTO article (categoris_id, title, image, text, author) VALUES (' $categoris_id', ' $title', '$picture', '$text', '$author')";
                
                // Проверка удачный ли SQL запрос,
                if (mysqli_query($connection, $sql)) {

                    // Вывод сообщения об удачном создании статьи
                    echo '
                    <div class="flex cuc-creat">
                        Статья успешно <br> создана!
                    </div>
                    <script> window.setTimeout(function() { window.location = "blog_my-page.php"; }, 2000) </script>
                    ';

                
                } else {

                    // Вывод ошибки SQL если запрос не совершился
                    echo "Error: " . $sql . "<br>" . mysqli_error($connection);
                }

            } else 
            {

                // Вывод сообщения с ошибкой при заполнении формы
                echo '
                <div class="flex errors">
                    '. array_shift($errors) .'
                </div>
                ';
            }

            
        }

        ?>
        <!-- HTML форма создания статьи -->
        <section class="flex creature">
            <h2 class="title creature-title">
                Создание статьи
            </h2>
            <form class="flex creature__form" action="/blog_creature-article.php" method="post" enctype="multipart/form-data">
                <div class="flex creature-form__categ">
                    <h3 class="creature-form-categ__title">
                        Категоря 
                    </h3>
                    <select class="select creature-form-categ__sel" name="categ">
                        <?php 
                         echo'<option class="select creature-form-categ-sel_opt" value="none">Выбор категории</option>';
                        $i = 0;
                        
                        // Цикл в котором создаються пункты выбора категории
                        while( $i <= array_key_last($cat) )
                        {
                            if( $cat[$i]['id'] == $data['categoris_id'] ){
                                echo'
                                    <option selected class="select creature-form-categ-sel_opt" value="'. $cat[$i]['id'] .'">'. $cat[$i]['name'] .'</option>
                                ';
                            } else {
                                echo'
                                    <option class="select creature-form-categ-sel_opt" value="'. $cat[$i]['id'] .'">'. $cat[$i]['name'] .'</option>
                                ';
                            }
                            $i++;
                        }
                        ?>

                    </select>
                </div>
                <div class="flex creature-form__name">
                    <h3 class="creature-form-name__title">
                        Название
                    </h3>
                    <input class="input creature-form-name__inp" type="text" name="title" value="<?php echo
                    $data['title']; ?>">
                </div>
                <div class="flex creature-form__file">
                    <h3 class="creature-form-file__title">
                        Картинка 
                    </h3>
                    <input class="input creature-form-file__inp" type="file" name="file_picture" value="<?php 
                    $_FILES; ?>">
                </div>
                <div class="flex creature-form__text">
                    <h3 class="creature-form-text__title">
                        Текст 
                    </h3>
                    <textarea class="creature-form-text__textarea" resize type="text" name="text" ><?php echo
                    $data['text']; ?></textarea>
                    <div class="creature-form-text__symbols">
                        <?php
                            $symbols = mb_strlen( $data['text']);
                            echo'
                                Количество символов = '. $symbols .'     
                            ';
                        ?>
                    </div>
                </div>
            
                <div class="flex btn-reset creature-form__com">
                    <button class="creature-form-com__btn" name="du_creature" tyep="submit" >
                        Создать
                    </button>
                </div>
            </form> 
        </section>
            
        </main>

</body>

</html>