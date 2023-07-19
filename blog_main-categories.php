<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сингулярность</title>
    <link rel="stylesheet" href="/css/blog_main-categories.css">
    <link rel="stylesheet" href="/css/blog_global.css">
    <link rel="stylesheet" href="/css/blog_global_media.css">
    <link rel="stylesheet" href="/css/blog_header.css">
    <link rel="stylesheet" href="/css/blog_header_media.css">
    <link rel="stylesheet" href="/css/blog_sidbar.css">
    <link rel="stylesheet" href="/css/blog_sidbar_media.css">
    <link rel="stylesheet" href="/css/blog_main.css">
    <link rel="stylesheet" href="/css/blog_main_media.css">
    <link rel="stylesheet" href="/css/blog_footer.css">
    <link rel="icon" href="/img/favicon.png" type="image/x-icon">
</head>

<body>
    <?php
    include "blog_header.php";
    ?>
    <main>
        <?php
        include "blog_sidbar.php";
        ?>
        <?php 

            // Кол-во блоков на странице -1
            $block = 0;

            // Кол-во катрочек в блоке -1
            $card = 2;

            // Mod page article -мод страници статей, используеться если пользователь на 2,3,4... страницах, 
            // для смещение отображаемых статей
            $mpa = $_GET['mpa'];
            
            // Создание массива $main_cat, в нём будут находиться класс блока, название блока, все статьи блока
            $main_cat = array();

            if( $_GET['categoris'] == 0)
            {
                // Внесение в массив $main_cat класс и название сатегории статей
                $main_cat[0] = 'new';
                $main_cat[1] = 'Новое';
            }
            else
            {
                // Внесение в массив $main_cat класс и название первого блока на 2,3,4....
                $main_cat[0] = $cat[$_GET['categoris'] - 1]['name_class'];
                $main_cat[1] = $cat[$_GET['categoris'] - 1]['name'];
            }

            // Проверка на номер страницы
            if( $mpa == 0)
            {
                if($_GET['categoris'] == 0)
                {
                    // Внесение в массив  $main_cat id первых 24 статей из массива $arti
                    $i = 0;
                    while( $i <= $card )
                    {
                        $main_cat[$i + 2] =  $arti[$i]['id'];
                        $i++;
                    }
                }
                else
                {
                    // Внесение в массив  $main_cat id первых 24 статей из массива $arti с нужным id 
                    $i = 0;
                    $keys = array_keys(array_column($arti, 'categoris_id'), $_GET['categoris']);
                    while( $i <= $card )
                    {
                        $main_cat[$i + 2] =  $arti[$keys[$i]]['id'];
                        $i++;
                    }  
                }
                
            } 
            else 
            {
                // Счётчик страницы, смещает статьи на 2,3,4... страницах
                $p = $mpa * ($card+1) +1;

                if($_GET['categoris'] == 0)
                {
                    // Внесение в массив  $main_cat id первых 24 статей из массива $arti
                    $i = 0;
                    while( $i <= $card )
                    {
                        $main_cat[$i + 2] =  $arti[$p]['id'];
                        $i++;
                        $p++;
                    }
                }
                else
                {
                    // Внесение в массив  $main_cat id первых 24 статей из массива $arti с нужным id 
                    $i = 0;
                    $keys = array_keys(array_column($arti, 'categoris_id'), $_GET['categoris']);
                    while( $i <= $card )
                    {
                        $main_cat[$i + 2] =  $arti[$keys[$p]]['id'];
                        $i++;
                        $p++;
                    }  
                }
            }

            $b = 0;

            // Переменная $m отражает на каком блоке в данный момент находиться массив
            // и какие ячейки массива $main_cat будут использоваться
            $m = 0;

            // Создание блоков с картачками в сидбаре.
            // С каждым циклом создаёться новый блок.
            // Кол-во блоков зависит от переменной $block.
            // Сделанно именно так с заделом на возможное в будущем управление сибаром через интерфейс
            // на пример через админ панель.
            while( $b <=  $block)
            {   

                // Переменная $m отражает на каком блоке в данный момент находиться массив
                // и какие ячейки массива $main_cat будут использоваться
                // $m = $b * ( 4 + $card )
                ?>
                <!-- Ввывод блока, класс зависит от того какой это блок
                так что не смотря на то что блоки создаються циклом их можно стилизовать уникально -->
                <section class="flex content">
                    <div class="content__block content__<?php echo $main_cat[$m] ?>">
                        <!-- Заголовок блока -->
                        <h2 class="title content-<?php echo $main_cat[$m] ?>__title">
                            <?php echo $main_cat[$m + 1] ?>
                        </h2>
                        <?php
                        
                            $i = 0;
                            
                            // Ввывод карточек блока.
                            // С каждым циклом создаёться новая карточка.
                            // Кол-во карточек зависит от переменной $card
                            while( $i <= $card )
                            {
                            
                                // Вытаскивание необходимого ключа из массива $arti,
                                //  используя при этом id находящийся в массиве $main_cat.
                                $key = array_search( $main_cat[$i + $m + 2], array_column($arti, 'id'));

                                // Поиск ключа ячейки в массиве всех лайков по id статьи 
                                $likey = array_search( $main_cat[$i + $m + 2], array_column($arts_max_likes, 'id'));

                                // Проверка существует ли ключ
                                // Нужен если карточек меньше чем нужно
                                // Выполненна таким образом так как при проверках ноль равен пустая строчка
                                // И и было не возможно отделить ключ ноль от пустой строчки
                                if (strlen($key . '1') == 1)
                                {
                                    break;
                                }

                                 // Проверка существования ссесии аккаунта 
                                if (isset($_SESSION['acount']))
                                {
                                    $like_stst = 0;

                                    // Проверка, лайкну та ли данная статья пользователем
                                    foreach ($my_liks as &$my_likse) 
                                    {
                                        
                                        if( $arti[$key]['id'] == $my_likse) 
                                        {
                                            
                                            $like_stst = 1;
                                            
                                        }
                                    }
                                }

                                ?>
                                <!-- Ссылка обрамляющая карточку статьи, через GET запрос передаёться id  и название статьи-->
                                <a href="/blog_article.php?id=<?php echo $arti[$key]['id'] ?>&title=<?php echo $arti[$key]['title'] ?>">
                                    <button class="post content-<?php echo $main_cat[$m] ?>__post">
                                        <!-- Верхняя часть карточки, 
                                        в ней находяться обложка и блок текста -->
                                        <div class="flex post__top content-<?php echo $main_cat[$m] ?>-post__top">
                                            <!-- Обложка статьи -->
                                            <div class="post-top__picture content-<?php echo $main_cat[$m] ?>-post-top__picture">
                                                <img class="post-top-picture__img content-<?php echo $main_cat[$m] ?>-post-top-picture__img" src="/img/s<?php echo $arti[$key]['image'] ?>.png" alt="">
                                            </div>
                                            <!-- Блок текста, в нём название, дата и превью статьи -->
                                            <div class="post-top__text content-<?php echo $main_cat[$m] ?>-post-top__text">
                                                <!-- Название статьи -->
                                                <h3 class="post-top-text__title content-<?php echo $main_cat[$m] ?>-post-top-text__title">
                                                    <?php echo $arti[$key]['title'] ?>
                                                </h3>
                                                <!-- Время публикации статьи -->
                                                <time class="post-top-text__time content-<?php echo $main_cat[$m] ?>-post-top-text__time" datetime="2022-12-9-12:35">
                                                    <?php echo substr($arti[$key]['pubdate'], 0, 16) ?>
                                                </time>
                                                <!-- превью статьи -->
                                                <div class="post-top-text__preview content-<?php echo $main_cat[$m] ?>-post-top-text__preview">
                                                    <?php echo $arti[$key]['text'] ?>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Нижний блок карточки, в нём кол-во лайков и просмотров а так же автор  -->
                                        <div class="flex post__bottom content-<?php echo $main_cat[$m] ?>-post__bottom">
                                            <!-- Блок лайков -->
                                            <div class="flex post-bottom__like content-<?php echo $main_cat[$m] ?>-post-bottom__like">
                                                <?php 
                                                    // Проверка лайкну та ли данная статья пользователем, и если да, изменение картинки лайка
                                                    if( $like_stst == 1 )
                                                    {
                                                        ?> <img class="post-bottom-like__img" src="/img/like-on.png" alt=""> <?php
                                                    } 
                                                    else
                                                    {
                                                        ?> <img class="post-bottom-like__img" src="/img/like-off.png" alt=""> <?php
                                                    }
                                                ?>
                                                <div class="post-bottom-like__text content-<?php echo $main_cat[$m] ?>-post-bottom-like__text">
                                                    <?php echo $arts_max_likes[$likey]['like'] ?>
                                                </div>
                                            </div>
                                            <!-- Блок просмотров -->
                                            <div class="flex post-bottom__views content-<?php echo $main_cat[$m] ?>-post-bottom__views">
                                                <svg class="svg post-bottom-views__svg content-<?php echo $main_cat[$m] ?>-post-bottom-views__svg" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="442.04px" height="442.04px" viewBox="0 0 442.04 442.04" style="enable-background:new 0 0 442.04 442.04;" xml:space="preserve">
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
                                                <div class="post-bottom-views__text content-<?php echo $main_cat[$m] ?>-post-bottom-views__text">
                                                    <?php echo $arti[$key]['views'] ?>
                                                </div>
                                            </div>
                                            <!-- Автор статьи -->
                                            <div class="post-bottom__author content-<?php echo $main_cat[$m] ?>-post-bottom__author">
                                                <?php echo $autho[$arti[$key]['author']-1]['name'] ?>
                                            </div>
                                        </div>
                                    </button>
                                </a>                           
                                <?php
                                $i++;
                                
                            }  
                        ?>
                    </div>
                </section> 
            <?php
            $b++;
            $m= $m + 2 + $i;
            }
            
            // Счётчик статей данной категории
            $ar = 0;

            // Проверка какая категория открыта, и подсчёт статей в данной категории 
            if( $_GET['categoris'] == 0)
            {
                foreach ($arti as &$art) 
                {
                    $ar++;
                }
            }
            else
            {
                foreach ($arti as &$art) 
                {
                    
                    if( $art['categoris_id'] == $_GET['categoris']) 
                    {
                        
                        $ar++;   
                        
                    }
                }
            }

            // Определение колличества страниц
            $num = ceil($ar / ($card + 1));

            // Выввод ссылки на первую страницу
            ?>
                <section class="flex numbering">
                    <div class="flex numbering__block">
                        <div class="numbering__first">
                            <a class="links numbering-first__link" href="/blog_main-categories.php?categoris=<?php echo $_GET['categoris'] ?>&mpa=<?php echo '0' ?>">
                                Первая
                            </a>
                        </div>
            <?php

            // Проверка, на какой странице пользователь, и не отображение страниц дальше -3 от текущей
            if( $mpa < 4 )
            {
                $i = 1;
            }
            else
            {
                $i = $mpa - 2;
                ?>
                    <div class="numbering__initial-ellipsis">
                        ...
                    </div>
                <?php
            }

            // Цикл создания номеров страниц
            while( $i <= $num )
            {

                // Проверка на какой странице сейчас пользователь, и если в данной итерации отображаеться текущая страница,
                // поменять ей цвет
                if( $mpa + 1 == $i )
                {
                    ?>
                        <div class="numbering__current-positions">
                            <a class="links numbering-current-positions__link" href="/blog_main-categories.php?categoris=<?php echo $_GET['categoris'] ?>&mpa=<?php echo $i -1 ?>">
                                <?php echo $i ?>
                            </a>
                        </div>
                    <?php
                }
                else
                {
                    ?>
                        <div class="numbering__positions">
                            <a class="links numbering-positions__link" href="/blog_main-categories.php?categoris=<?php echo $_GET['categoris'] ?>&mpa=<?php echo $i -1 ?>">
                                <?php echo $i ?>
                            </a>
                        </div>
                    <?php 
                }

                // Проверка на верхний порог отображаемый страниц, выход из цикла если уже отображенно +3 страницы от текущей
                if( $mpa + 3 < $i )
                {
                    ?>
                        <div class="numbering__finite-ellipsis">
                            ...
                        </div>
                    <?php

                    break;
                }

                $i++;
            }

            // Выввод ссылки на последную страницу
            ?>
                        <div class="numbering__last">
                            <a class="links snumbering-last__link" href="/blog_main-categories.php?categoris=<?php echo $_GET['categoris'] ?>&mpa=<?php echo $num - 1 ?>">
                                Последняя
                            </a>
                        </div>
                    </div>
                </section>
                
            <?php
        
        ?>
        </main>
    </body>

    <?php
    include "blog_footer.php";
    ?>

</html>