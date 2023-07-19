
    <!-- Шапка страницы -->
    <footer class="flex footer">
        <div class="flex content__footer">
            <!-- Верхний блок шапки -->
            <div class="flex footer__top">
                <!-- Блок картинки лого -->
                <div class="footer-top__logo">
                    <a class="footer-top-logo__link" href="/blog_main.php" >
                        <img class="footer-top-logo-link__img" src="/img/singularis-1.png" alt="Лого">
                    </a>
                </div>
                <!-- Название страницы -->
                <h1 class="footer-top__heading">
                    Сингулярность <br> Путь в бесконечность
                </h1>
            </div>
            <!-- Нижний блок шапки -->
            <div class="flex footer__bottom">
                <!-- Слайдер с категориями -->
                <div class="flex header-bottom__categories">
                
                <?php
                    // Определение на какую страницу будет вести стрелочка, и возвращение на ту же страницу, 
                    // если пользователь на начальной странице
                    if( $mc == 0)
                    {
                        // Arrow change categoris left, стрелочка смены категорий левая, Значение ссылки для левой стрелочки
                        $accl = 0;
                    }
                    else
                    {
                        $accl = $mc / 3 - 1;
                    }
                ?>

                <!-- Левая стрелочка -->
                <a href="/blog_main.php?mc=<?php echo  $accl ?>">
                    <button class="btn-reset header-bottom-categories__left-btn">
                        <svg class="svg" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="32px" height="32px" viewBox="0 0 330 330" style="enable-background:new 0 0 330 330;" xml:space="preserve">
                            <path id="XMLID_224_" d="M325.606,229.393l-150.004-150C172.79,76.58,168.974,75,164.996,75c-3.979,0-7.794,1.581-10.607,4.394
                                l-149.996,150c-5.858,5.858-5.858,15.355,0,21.213c5.857,5.857,15.355,5.858,21.213,0l139.39-139.393l139.397,139.393
                                C307.322,253.536,311.161,255,315,255c3.839,0,7.678-1.464,10.607-4.394C331.464,244.748,331.464,235.251,325.606,229.393z" />
                        </svg>
                    </button>
                </a>
                <!-- Список категорий -->
                <ul class="flex list-reset header-bottom-categories__list">
                    
                    <?php
                        // Проверка на какой странце находиться пользователь, и если на первой, вывод категории новое
                        if( $mc == 0)
                        {
                            ?> 
                                <li class="header-bottom-categories-list__title">
                                    Новое
                                </li>
                            <?php
                        }
                        else
                        {
                            ?> 
                                <li class="header-bottom-categories-list__title">
                                    <?php echo $cat[$mc - 1]['name'] ?>
                                </li>
                            <?php
                        }

                        $i = 1;

                        // Цикл создания надписей категорий, кол во категорий зависит от $mbc
                        while( $i <= $mbc - 1)
                        {
                            ?> 
                                <li class="header-bottom-categories-list__title">
                                    <?php echo $cat[$mc -1 + $i]['name'] ?>
                                </li>
                            <?php

                            $i++; 
                        }
                    ?>
                    
                </ul>

                    <?php
                        // Определение на какую страницу будет вести стрелочка, и возвращение на ту же страницу, 
                        // если пользователь на начальной странице
                        if( $mc / 3 == (ceil((count($cat) + 1) / 3)) -1)
                        {
                            // Arrow change categoris right, стрелочка смены категорий правая, Значение ссылки для правой стрелочки
                            $accr = $mc / 3;
                        }
                        else
                        {
                            $accr = $mc / 3 + 1;
                        }
                    ?>

                <!-- Правая стрелочка -->
                <a href="/blog_main.php?mc=<?php echo  $accr ?>">
                    <button class="btn-reset header-bottom-categories__right-btn">
                        <svg class="svg" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="32px" height="32px" viewBox="0 0 330 330" style="enable-background:new 0 0 330 330;" xml:space="preserve">
                            <path id="XMLID_224_" d="M325.606,229.393l-150.004-150C172.79,76.58,168.974,75,164.996,75c-3.979,0-7.794,1.581-10.607,4.394
                                l-149.996,150c-5.858,5.858-5.858,15.355,0,21.213c5.857,5.857,15.355,5.858,21.213,0l139.39-139.393l139.397,139.393
                                C307.322,253.536,311.161,255,315,255c3.839,0,7.678-1.464,10.607-4.394C331.464,244.748,331.464,235.251,325.606,229.393z" />
                        </svg>
                    </button>
                </a>
            </div>
            </div>
        </div>
    </footer>
