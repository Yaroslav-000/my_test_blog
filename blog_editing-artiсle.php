<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование статьи</title>
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
        
        // Проверка был ли передан GET запрос
        if ( strlen($_GET['id'] .'1') !== 1 ) {

            // Поиск ключа нужной статьи в массиве всех статей $arti по id статьи, полученном из GET запроса
            $keys = array_keys(array_column($arti, 'id'), $_GET['id']);

            $data = $arti[$keys[0]];
            echo '1';
        }
      
            if ( strlen($_GET['edit_id'] .'1') !== 1 ) {

                // Поиск ключа нужной статьи в массиве всех статей $arti по id статьи, полученном из GET запроса
                $keys = array_keys(array_column($arti, 'id'), $_GET['edit_id']);

                $data['id'] = $arti[$keys[0]]['id'];

                $data['image'] = $arti[$keys[0]]['image'];

                $data['pubdate'] = $arti[$keys[0]]['pubdate'];
            
        } 
        if ( empty($_GET)) {

            // Перенаправление пользователя в личный кабинет если не был передан не GET не POST запрос
            // С сообщением об ошибке переданной через GET запрос
            echo ' <script> window.setTimeout(function() { window.location = "blog_my-page.php?errors-editing=1"; }, 2000) </script> ';

        }

        // Проверка, была ли отправленна форма
        if( isset($_POST['du_editing']) ) {

            // Перенос данных из  переменной $_POST, в переменную $data
            $data['categoris_id'] = $_POST['categoris_id'];
            $data['title'] = $_POST['title'];
            $data['text'] = $_POST['text'];

            // Создание массива для ошибок
            $errors = array();

            // Проверка выбрана ли категория статьи
            if( trim( $data['categoris_id']) == 'none' ){

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
            // Иначе запись о не выполнении одного из условий? если файла нет то просто остаёться старый
            if ( $_FILES['file_picture']['name'] == '' ) {
                
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

                // Задание переменных для SQL запроса
                $id = $data['id'];
                $categoris_id = $data['categoris_id'];
                $title = $data['title'];
                $text = $data['text'];
                $picture = $data['image'];

                // Сохранение картинки на "Сервер" 
                move_uploaded_file( $_FILES['file_picture']['tmp_name'], 
                'E:\PROGRAM\Vork\locHost\OSPanel\domains\blog\img\s'.$picture.'.png');

                // Объявление SQL переменной для SQL запроса к БД
                $sql = "UPDATE article SET categoris_id = '$categoris_id', title = '$title', text = '$text'  WHERE id = '$id' ";

                // Проверка удачный ли SQL запрос,
                if (mysqli_query($connection, $sql)) {

                    // Вывод сообщения об удачном создании статьи
                    echo '
                    <div class="flex cuc-creat">
                        Статья успешно <br> обновлена!
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

            
            // Проверка прошло ли с момента создания статьи больше суток
            if(  strtotime( $data['pubdate']) <  strtotime (date("Y-m-d H:i")) - 86400 )
            {
                //Если да, перенаправление пользователя в его личный кабинет
                echo '
                <script> window.setTimeout(function() { window.location = "blog_my-page.php"; }, 2000) </script>
                ';
            }
            
        ?>
        <!-- HTML форма создания статьи -->
        <section class="flex creature">
            <h2 class="title creature-title">
                Редактирование статьи 
            </h2>
            <form class="flex creature__form" action="/blog_editing-artiсle.php?edit_id=<?php echo $data['id'] ?>" method="post" enctype="multipart/form-data">
                <div class="flex creature-form__categ">
                    <h3 class="creature-form-categ__title">
                        Категоря 
                    </h3>
                    <select class="select creature-form-categ__sel" name="categoris_id">
                        <?php 
                         echo'<option class="select creature-form-categ-sel_opt" value="none">Выбор категории</option>';
                        $i = 0;

                        // Цикл в котором создаються пункты выбора категории
                        while( $i <= array_key_last($cat) )
                        {
                            if( $cat[$i]['id'] == $data['categoris_id'] ){
                                echo'
                                    <option selected="selected" class="select creature-form-categ-sel_opt" value="'. $cat[$i]['id'] .'">'. $cat[$i]['name'] .'</option>
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
                    <div class="creature-form-file__preview">
                        <img class="creature-form-file-preview__img" src="/img/s<?php echo $data['image'] ?>.png" alt="">
                    </div>
                    <input class="input creature-form-file__inp-custom" type="file" placeholder="Обновить картинку" name="file_picture" value="<?php 
                    $_FILES; ?>">
                </div>
                <div class="flex creature-form__text">
                    <h3 class="creature-form-text__title">
                        Текст 
                    </h3>
                    <textarea class="creature-form-text__textarea" resize type="text" name="text" ><?php echo $data['text']; ?></textarea>
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
                    <button class="creature-form-com__btn" name="du_editing" tyep="submit" >
                        Сохранить
                    </button>
                </div>
            </form> 
        </section>
            
        </main>

</body>

</html>