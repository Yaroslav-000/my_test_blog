

<body>
    <!-- Секция сидбар -->
    <section class="flex sidebar">
        <!-- Блок выбора случайно статьи -->
        <div class="sidebar__block-random">
            <h2 class="title sidebar-block-random-title">
                Случайная запись
            </h2>
            <?php

                // Выбор случайной статьи ($arti - многомерный массив со статьями)
                $rand_arts = array_rand($arti, 1);
                

                ?>
                <!-- Ссылка на случайно выбранную статью -->
                <a href="/blog_article.php?id=<?php echo $arti[$rand_arts]['id'] ?>&title=<?php echo $arti[$rand_arts]['title'] ?>">
                    <button class="btn-reset sidebar-block-random__btn">
                        <img class="sidebar-block-random-btn__img" src="/img/cat-black-hole.png" alt="">
                    </button>
                </a>
                
            
        </div>
        <?php

            
            // Подсчёт лайков всех статей
            // Первый цикл работает с таблицей статей а второй с таблицей лайков.
            // Во втором цикле с каждой итерацией идёт проверка id выбранной строки таблицы статей 
            // и id выбранной в данной итерации строки таблицы лайков и в случаи успеха добавляет +1 к 
            // переменной $l (индикатор кол-ва лайков).
            // После конца итераций второго цикла кол-во лайков и id статьи заносяться в многомерный массив
            // для использования позже, переменная $a используеться как счётчик итераций первого цикла
            // для выбора конкретной стать при сравнении (без $a не работает, второй цикл не зависим 
            // от первого) и для указания конкретного места в многомерном массиве $ar содержащим в себе 
            // под конец итераций id статей и кол-во их лайков.
            $a = 0;
            
            foreach ($arti as &$art) 
            {
                
                $l = 0;

                foreach ($lik_e as &$lik) 
                {
                    
                    if( $lik['id_article'] == $arti[$a]['id']) 
                    {
                        
                        $l++;   
                        
                    }
                }

                $ar[$a]['like'] = $l;
                $ar[$a]['id'] = $arti[$a]['id'];

                $a++;
                
            }
            
            $arts_max_likes = $ar;
                
            // Функция для сортировки многомерного массива $arts_max_likes по полю лайки
            function arts_max_likes($a, $b) 
            { 
                return strnatcmp($b["like"], $a["like"]); 
            } 
                 
            // Сортировка многомерного массива $arts_max_likes
            usort($arts_max_likes, "arts_max_likes");


            // $arti многомерный массив со статьями
            $arts_max_views = $arti;
            
            // Функция для сортировки многомерного массива $arts_max_views по полю просмотры
            function arts_max_views($a, $b) 
            { 
                return strnatcmp($b["views"], $a["views"]); 
            } 
                    
            // Сортировка многомерного массива $arts_max_views
            usort($arts_max_views, "arts_max_views");

            // Проверка существования ссесии аккаунта
            if (isset($_SESSION['acount']))
            {
                // Создание масива с id всех лайкнутых статей пользователем
                $my_liks = array();
            
                $i = 0;

                // Обход всего массива с лайками статей и занесение лайкнутых статей пользователем в отдельный массив 
                foreach ($lik_e as &$lik) 
                {
                    
                    if( $lik['id_user'] == $_SESSION['acount']['id']) 
                    {
                        
                        $my_liks[$i] = $lik['id_article'];

                        $i++;
                        
                    }
                }
            }



            // Кол-во блоков в сидбаре -1
            $block = 1;

            // Кол-во катрочек в блоке -1
            $card = 2;

            // Создание массива $sidbar, в нём будут находиться класс блока, название блока, все статьи блока
            $sidbar = array();

            // Внесение в массив $sidbar класс и название первого блока
            $sidbar[0] = 'favourite';
            $sidbar[1] = 'Самое любимое';
            
            // Внесение в массив  $sidbar id первых 3 статей из массива $arts_max_likes
            $i = 0;
            while( $i <= $card )
            {
                $sidbar[$i + 2] = $arts_max_likes[$i]['id'];
                $i++;
            }

            // Подсчёт всех имеющихся в массиве элементов 
            $key_quantity = count($sidbar);
            
            // Внесение в массив $sidbar класс и название второго блока
            $sidbar[$key_quantity] = 'famous';
            $sidbar[$key_quantity + 1] = 'Самое известное';
            
            // Внесение в массив  $sidbar id первых 6 статей из массива $arts_max_views
            $i = 0;
            while( $i <= $card )
            {
                $sidbar[$key_quantity + 2 + $i] = $arts_max_views[$i]['id'];
                $i++;
            }

            $b = 0;

            $m = 0;

            // Создание блоков с картачками в сидбаре.
            // С каждым циклом создаёться новый блок.
            // Кол-во блоков зависит от переменной $block.
            // Сделанно именно так с заделом на возможное в будущем управление сибаром через интерфейс
            // на пример через админ панель.
            while( $b <=  $block)
            {   
                
                // Переменная $m отражает на каком блоке в данный момент находиться массив
                // и какие ячейки массива $sidbar будут использоваться
                // $m = $b * ( 3 + $card );
                
                
                ?>
                <!-- Ввывод блока, класс зависит от того какой это блок
                так что не смотря на то что блоки создаються циклом их можно стилизовать уникально -->
                <div class="sidebar__block-<?php echo $sidbar[$m] ?>">
                    <!-- Заголовок блока -->
                    <h2 class="title sidebar-block-<?php echo $sidbar[$m] ?>__title">
                        <?php echo $sidbar[$m+1] ?>
                    </h2>
                    <?php
                    
                        $i = 0;
                        
                        // Ввывод карточек блока.
                        // С каждым циклом создаёться новая карточка.
                        // Кол-во карточек зависит от переменной $card
                        while( $i <= $card )
                        {
                        
                            // Вытаскивание необходимого ключа из массива $arti,
                            //  используя при этом id находящийся в массиве $sidbar.
                            $key = array_search( $sidbar[$i + $m + 2], array_column($arti, 'id'));
                            
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
                            <!-- Ссылка обрамляющая карточку статьи, через GET запрос передаёться id -->
                            <a href="/blog_article.php?id=<?php echo $arti[$key]['id'] ?>&title=<?php echo $arti[$key]['title'] ?>">
                                <button class="post sidebar-block-<?php echo $sidbar[$m] ?>__post">
                                    <!-- Верхняя часть карточки, 
                                    в ней находяться обложка и блок текста -->
                                    <div class="flex post__top sidebar-block-<?php echo $sidbar[$m] ?>-post__top">
                                        <!-- Обложка статьи -->
                                        <div class="post-top__picture sidebar-block-<?php echo $sidbar[$m] ?>-post-top__picture">
                                            <img class="post-top-picture__img sidebar-block-<?php echo $sidbar[$m] ?>-post-top-picture__img" src="/img/s<?php echo $arti[$key]['image'] ?>.png" alt="">
                                        </div>
                                        <!-- Блок текста, в нём название, дата и превью статьи -->
                                        <div class="post-top__text sidebar-block-<?php echo $sidbar[$m] ?>-post-top__text">
                                            <!-- Название статьи -->
                                            <h3 class="post-top-text__title sidebar-block-<?php echo $sidbar[$m] ?>-post-top-text__title">
                                                <?php echo $arti[$key]['title'] ?>
                                            </h3>
                                            <!-- Время публикации статьи -->
                                            <time class="post-top-text__time sidebar-block-<?php echo $sidbar[$m] ?>-post-top-text__time" datetime="2022-12-9-12:35">
                                                <?php echo substr($arti[$key]['pubdate'], 0, 16) ?>
                                            </time>
                                            <!-- превью статьи -->
                                            <div class="post-top-text__preview sidebar-block-<?php echo $sidbar[$m] ?>-post-top-text__preview">
                                                <?php echo $arti[$key]['text'] ?>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Нижний блок карточки, в нём кол-во лайков и просмотров а так же автор  -->
                                    <div class="flex post__bottom sidebar-block-<?php echo $sidbar[$m] ?>-post__bottom">
                                        <!-- Блок лайков -->
                                        <div class="flex post-bottom__like sidebar-block-<?php echo $sidbar[$m] ?>-post-bottom__like">
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
                                            <div class="post-bottom-like__text sidebar-block-<?php echo $sidbar[$m] ?>-post-bottom-like__text">
                                                <?php echo $arts_max_likes[$i]['like'] ?>
                                            </div>
                                        </div>
                                        <!-- Блок просмотров -->
                                        <div class="flex post-bottom__views sidebar-block-<?php echo $sidbar[$m] ?>-post-bottom__views">
                                            <svg class="svg post-bottom-views__svg sidebar-block-<?php echo $sidbar[$m] ?>-post-bottom-views__svg" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="442.04px" height="442.04px" viewBox="0 0 442.04 442.04" style="enable-background:new 0 0 442.04 442.04;" xml:space="preserve">
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
                                            <div class="post-bottom-views__text sidebar-block-<?php echo $sidbar[$m] ?>-post-bottom-views__text">
                                                <?php echo $arti[$key]['views'] ?>
                                            </div>
                                        </div>
                                        <!-- Автор статьи -->
                                        <div class="post-bottom__author sidebar-block-<?php echo $sidbar[$m] ?>-post-bottom__author">
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
           <?php
            $b++;
            $m= $m + 2 + $i;
            }
        ?>
        
    </section>
