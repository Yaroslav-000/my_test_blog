<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_GET['title'] ?></title>
    <link rel="stylesheet" href="/css/blog_global.css">
    <link rel="stylesheet" href="/css/blog_global_media.css">
    <link rel="stylesheet" href="/css/blog_header.css">
    <link rel="stylesheet" href="/css/blog_header_media.css">
    <link rel="stylesheet" href="/css/blog_sidbar.css">
    <link rel="stylesheet" href="/css/blog_sidbar_media.css">
    <link rel="stylesheet" href="/css/blog_article.css">
    <link rel="stylesheet" href="/css/blog_article_media.css">
    <link rel="icon" href="/img/favicon.png" type="image/x-icon">
</head>

<body>
    <?php
    
    //Копирование кода из файла blog_header.php
    include "blog_header.php";

    // Проверка данных при создании комментария
    // Проверяеться отправленна ли форма коментария
    if( isset($_POST['du_my-comments']) ) {
        
        // Создаёться массив для ошибок
        $errors = array();

        // Проверка наличия текста
        if( trim( $_POST['text']) == '' ){

            // Добавление записи об отсуцтвие текста если его нет
            $errors[] = 'Введите текст!';
        }

        //Проверка что текст короче 150 символов
        if(  mb_strlen( $_POST['text']) > 150 ){

            // Добавление записи о том что текст слишком длинный
            $errors[] = 'Текст длинее 150 символов';
        }


        // Проверка наличия ошибок
        if( empty($errors) ){
            
            // Объявление переменных для SQL запроса и создания записи в БД
            $author_id = $_SESSION['acount']['id'];
            $article_id = $_GET['id'];
            $text = $_POST['text'];

            // SQL запрос в БД и создание записи
            $sql = "INSERT INTO coments (author_id, article_id, text) VALUES (' $author_id ', ' $article_id ', ' $text ' )";
            $mysqli_query = mysqli_query($connection, $sql);
           
        }
        
    }

    if( isset($_POST['du_my-like'] )) {
        
        // Создаёться массив для ошибок
        $errors = array();

        // Проверка существования аккаунта 
        if( !isset($_SESSION['acount']) ){

            // Добавление записи об отсуцтвие текста если его нет
            $errors[] = 'Войдите в аккаунт';
        }

        // Проверка есть ли строка с лайком данным пользователем данной статьи
        foreach ($lik_e as &$lik) 
        {
                    
            if( $lik['id_user'] == $_SESSION['acount']['id']) 
            {
                if( $lik['id_article'] == $_GET['id'])  
                {
                    $my_like['id'] = $lik['id'];
                    $my_like['exist'] = '1' ;
                }
                        
            }
        }

        print_r($my_like['id'] );
        print_r($my_like['exist'] );

        // Проверка наличия ошибок
        if( empty($errors) ){

            if (strlen($my_like['exist'] . '1') == 1)
            {
                // Объявление переменных для SQL запроса и создания записи в БД
                $author_id = $_SESSION['acount']['id'];
                $article_id = $_GET['id'];

                // SQL запрос в БД и создание записи лайка
                $sql = "INSERT INTO lik_e (id_user, id_article ) VALUES (' $author_id ', ' $article_id ' )";
                $mysqli_query = mysqli_query($connection, $sql);   

                // Перенаправление для сброса пост запроса 
                echo '<script> window.setTimeout(function() { window.location = "blog_article.php?id='.$_GET['id'].'&title='.$_GET['title'].'"; }, 2000) </script>';
            } 
            else 
            {
                $id = $my_like['id'];  
                
                $sql = "DELETE FROM `lik_e` WHERE `id` = '$id'";
                $mysqli_query = mysqli_query($connection, $sql);   

                // Перенаправление для сброса пост запроса 
                echo '<script> window.setTimeout(function() { window.location = "blog_article.php?id='.$_GET['id'].'&title='.$_GET['title'].'"; }, 2000) </script>';
            }
            
            
        }
        else 
        {
             // Вывод сообщения об ошибке при заполнении формы
             echo '
             <div class="flex errors">
                 '. array_shift($errors) .'
             </div>
             ';
        }
        
        $_POST['du_my-like'] = 1;
    }

    ?>
    <main>
        <?php
        // Копирование кода из файла blog_sidbar.php
        include "blog_sidbar.php";


        ?>
        <?php
            
            // Поиск ключа нужной статьи в массиве всех статей $arti по id статьи, полученном из GET запроса
            $keys = array_keys(array_column($arti, 'id'), $_GET['id']);

            // Поиск ключа ячейки в массиве всех лайков по id статьи 
            $likey = array_search( $_GET['id'], array_column($arts_max_likes, 'id'));

            //Увеличение просмотров на 1 при загрузке страницы и перезапись просмотров в БД
            $id = $_GET['id'];
            $views = $arti[$keys[0]]['views']+1;
            $sql = "UPDATE article SET views = ' $views ' WHERE id = ' $id ' ";
            mysqli_query( $connection, $sql ) or die(mysqli_error($connection));
            
            // Проверка существования ссесии аккаунта 
            if (isset($_SESSION['acount']))
            {
                $like_stst = 0;

                // Проверка, лайкну та ли данная статья пользователем
                foreach ($my_liks as &$my_likse) 
                {
                    
                    if( $arti[$keys[0]]['id'] == $my_likse) 
                    {
                        
                        $like_stst = 1;
                        
                    }
                }
            }

            // HTML код страницы с вставками из массива $arti по ключу нужной статьи
            echo'
            <section class="flex article">
            <div class="flex article__base">
                <h2 class="title article-base__title">
                ' . $arti[$keys[0]]['title'] . '
                </h2>
                <div class="article-base__picture">
                    <img class="article-base-picture__img" src="/img/s' . $arti[$keys[0]]['image'] . '.png" alt="">
                </div>
                <div class="article-base__text">
                ' . $arti[$keys[0]]['text'] . '
                </div>
                <div class="flex article-base__bottom">
                    <div class="flex article-base-bottom__like">
                        <form class="flex article-base-bottom-like__form" action="/blog_article.php?id='.$_GET['id'].'&title='. $_GET['title'] .'#coments" method="post" enctype="multipart/form-data">
                            <button class="btn-reset article-base-bottom-like__btn" name="du_my-like" tyep="submit">
                                '; 
                                    // Проверка лайкну та ли данная статья пользователем, и если да, изменение картинки лайка
                                    if( $like_stst == 1 )
                                    {
                                        ?> <img class="article-base-bottom-like-btn__img" src="/img/like-on.png" alt=""> <?php
                                    } 
                                    else
                                    {
                                        ?> <img class="article-base-bottom-like-btn__img" src="/img/like-off.png" alt=""> <?php
                                    }
                                echo '
                            </button>
                            <div class="article-base-bottom-like__text">
                            ' . $arts_max_likes[$likey]['like'] . '
                            </div>
                        </form>
                    </div>
                    <div class="flex article-base-bottom__views">
                        <div class="article-base-bottom-views__picture">
                            <svg class="svg article-base-bottom-views-picture__svg" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="442.04px" height="442.04px" viewBox="0 0 442.04 442.04" style="enable-background:new 0 0 442.04 442.04;" xml:space="preserve">
                                <path d="M221.02,341.304c-49.708,0-103.206-19.44-154.71-56.22C27.808,257.59,4.044,230.351,3.051,229.203
                                        c-4.068-4.697-4.068-11.669,0-16.367c0.993-1.146,24.756-28.387,63.259-55.881c51.505-36.777,105.003-56.219,154.71-56.219
                                        c49.708,0,103.207,19.441,154.71,56.219c38.502,27.494,62.266,54.734,63.259,55.881c4.068,4.697,4.068,11.669,0,16.367
                                        c-0.993,1.146-24.756,28.387-63.259,55.881C324.227,321.863,270.729,341.304,221.02,341.304z M29.638,221.021
                                        c9.61,9.799,27.747,27.03,51.694,44.071c32.83,23.361,83.714,51.212,139.688,51.212s106.859-27.851,139.688-51.212
                                        c23.944-17.038,42.082-34.271,51.694-44.071c-9.609-9.799-27.747-27.03-51.694-44.071
                                        c-32.829-23.362-83.714-51.212-139.688-51.212s-106.858,27.85-139.688,51.212C57.388,193.988,39.25,211.219,29.638,221.021z" />
                                <path d="M221.02,298.521c-42.734,0-77.5-34.767-77.5-77.5c0-42.733,34.766-77.5,77.5-77.5c18.794,0,36.924,6.814,51.048,19.188
                                        c5.193,4.549,5.715,12.446,1.166,17.639c-4.549,5.193-12.447,5.714-17.639,1.166c-9.564-8.379-21.844-12.993-34.576-12.993
                                        c-28.949,0-52.5,23.552-52.5,52.5s23.551,52.5,52.5,52.5c28.95,0,52.5-23.552,52.5-52.5c0-6.903,5.597-12.5,12.5-12.5
                                        s12.5,5.597,12.5,12.5C298.521,263.754,263.754,298.521,221.02,298.521z" />
                                <path d="M221.02,246.021c-13.785,0-25-11.215-25-25s11.215-25,25-25c13.786,0,25,11.215,25,25S234.806,246.021,221.02,246.021z" />
                            </svg>
                        </div>
                        <div class="article-base-bottom-views__text">
                        ' . $arti[$keys[0]]['views'] . '
                        </div>
                    </div>
                    <div class="flex article-base-bottom__author">
                        <div class="article-base-bottom-author__avatar">
                            <img class="article-base-bottom-author-avatar__img" src="/img/a' .  $autho[$arti[$keys[0]]['author']-1]['avatar']  . '.png" alt="">
                        </div>
                        <div class="article-base-bottom-author__name">
                        ' .  $autho[$arti[$keys[0]]['author']-1]['name']  . '
                        </div>
                    </div>
                </div>
            </div>
            <div class="article__comments">
            ';
                // Нахождение всех ключей сомментариев для данной статьи по id статьи
                $keys = array_keys(array_column($com, 'article_id'), $_GET['id']);

                $a=0;

                // Колличество переменный в массиве $keys, -1 так как массив начинаеться с 0
                $b=sizeof($keys)-1;
                
                // Цикл отображения комментариев по ключам коментариев к данной статье из массива $keys
                while($a <= $b)
                {
                    echo'
                    <div class="flex article-comments__post">
                        <div class="article-comments-post__picture">
                            <img class="article-comments-post-picture__img" src="/img/a' .  $autho[$com[$keys[$a]]['author_id']-1]['avatar'] . '.png" alt="avatar">
                        </div>
                        <div class="article-comments-post__right">
                            <div class="article-comments-post-right__name">
                            ' .  $autho[$com[$keys[$a]]['author_id']-1]['name'] . '
                            </div>
                            <div class="article-comments-post-right__data">
                            ' .   substr($com[$keys[$a]]['pubdate'], 0, 16). '
                            </div>
                            <div class="article-comments-post-right__text">
                            ' .  $com[$keys[$a]]['text'] . '
                            </div>
                        </div>
                    </div>
                    ';
                    $a++;
                }

                echo'
            </div>';

            // Проверка существования ссесиии для отображения формы комментария, а так же вывода ошибки\
            // успеха создания комментария
            if( $_SESSION['acount']['id'] != ''){
                echo'
                    <div class="article__my-comments">
                    ';
                    $data = $_POST;
                    
                    // Проверка отправлена ли форма комментария
                    if( isset($data['du_my-comments']) ) {
            
                        // Проверка наличия ошибок если форма отправленна
                        if( empty($errors) ){
                            
                            // Проверка успешного создания в базе БД, если нет ошибок в форме
                            if ( $mysqli_query == true ) {

                                // Сообщение об успешном создании комментария
                                echo '
                                <div class="flex cuc-coments">
                                    Комментарий <br> успешно создан!
                                </div>
                                ';
            
                            } else {

                                // Вывод ошибки если форма правильная, но не произошла запись в БД
                                echo '
                                <div class="flex errors">
                                    '. "Error: " . $sql . "<br>" . mysqli_error($connection) .'
                                </div>
                                ';
                            }
            
                        } else {

                            // Вывод сообщения об ошибке при заполнении формы
                            echo '
                            <div class="flex errors">
                                '. array_shift($errors) .'
                            </div>
                            ';
                            
                        }
            
                    }
                            //  HTML Форма для написания и отправки комментария
                            echo'
                            <a id="coments"></a>
                            <div class="flex article-my-comments__post">
                                <div class="article-my-comments-post__picture">
                                    <img class="article-my-comments-post-picture__img" src="/img/a' .  $_SESSION['acount']['avatar'] . '.png" alt="avatar">
                                </div>
                                <div class="article-my-comments-post__right">
                                    <div class="article-my-comments-post-right__name">
                                    ' .  $_SESSION['acount']['name'] . '
                                    </div>
                                    <div class="article-my-comments-post-right__data">
                                    ' .    date("Y-m-d H:i") . '
                                    </div>
                                    <form class="flex article-my-comments-post__form" action="/blog_article.php?id='.$_GET['id'].'&title='. $_GET['title'] .'#coments" method="post" enctype="multipart/form-data">
                                        <div class="flex article-my-comments-post-form__text">
                                            <textarea class="article-my-comments-post-form-text__textarea" resize type="text" name="text" >'.$data['text'].'</textarea>
                                            <div class="article-my-comments-post-form-text__symbols">
                                                ';
                                                    $symbols = mb_strlen( $data['text']);
                                                    echo'
                                                        Количество символов = '. $symbols .'     
                                                    ';
                                                echo'
                                            </div>
                                        </div>
                                        <div class="flex btn-reset article-my-comments-post-form__com">
                                            <button class="article-my-comments-post-form-com__btn" name="du_my-comments" tyep="submit" >
                                                Создать
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                    </div>
                </section>    
                '; 
            }
        ?>
    </main>


</body>

</html>