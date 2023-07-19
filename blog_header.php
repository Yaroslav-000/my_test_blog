<?php session_start(); // Запуск ссесии
setcookie('acount'); // Создание куки 

// Подключение к бд
$host = '127.0.0.1';
$username = 'root';
$pass = '';
$bd = 'blog_bd';
$connection = mysqli_connect($host, $username, $pass, $bd);


// Объектно-ориентированный способ подключения БД
// $connection = new mysqli($host, $username, $pass, $bd);

// Проверка удачно ли подключение к БД
if( $connection == false )
{
    echo 'Не удаёться подключиться к БД! <br>';
    echo mysqli_connect_errno();
    exit;

}

// Mod categoris,  Мод категорий, производит смещение категорий в зависимости от того на какой странице пользователь
$mc = 0; 

// Mod block cards, Мод блоков карточек, определяет сколько карточек отображаеться на странице
$mbc = 3; 

// Проверка существования GET запроса с модом категорий, если да, то 
if( isset($_GET['mc']))
{
    $mc = $_GET['mc'] * $mbc;
}

// Запрос на информацию из таблицы статьи отсортирован по дате
$pubdate = "SELECT * FROM article ORDER BY pubdate DESC";
$article_pubdate = mysqli_query($connection, $pubdate);

// Запрос на информацию из таблицы Категории статей
$categoris = "SELECT * FROM article_categoris";
$article_categoris = mysqli_query($connection, $categoris);

// Запрос на информацию из таблицы аккаунты
$account = "SELECT * FROM account";
$author = mysqli_query($connection, $account);

// Запрос на информацию из таблицы комментарии отсортирован по дате
$coments = "SELECT * FROM coments ORDER BY pubdate DESC";
$coment = mysqli_query($connection, $coments);

// Запрос на информацию из таблицы лайки
$like_bd = "SELECT * FROM lik_e ";
$like_mysqli = mysqli_query($connection, $like_bd);

// Переннос данных из таблицы категории в массив
$a = 0;

while ($cats = mysqli_fetch_assoc($article_categoris)) 
{

    $cat[$a] = $cats;

    $a++;
}

// Переннос данных из таблицы статьи в массив
$b = 0;

while ($articles = mysqli_fetch_assoc($article_pubdate)) 
{

    $arti[$b] = $articles;

    $b++;
}

// Переннос данных из таблицы аккаунты в массив
$c = 0;

while ($authors = mysqli_fetch_assoc($author)) 
{

    $autho[$c] = $authors;

    $c++;
}

// Переннос данных из таблицы комментарии в массив
$d = 0;

while ($commentary = mysqli_fetch_assoc($coment)) 
{

    $com[$d] = $commentary;

    $d++;
}

// Переннос данных из таблицы лайки в массив
$f = 0;

while ($like_while = mysqli_fetch_assoc($like_mysqli)) 
{

    $lik_e[$f] = $like_while;

    $f++;
}

// Проверка существования куки или ссесии акаунт
// Для автороизации пользователей и сохранения статуса авторизации
// $_SESSION['acount']['remember'] == 'Yes\No' попытка реализовать запоминание пользователя (не работает)
// Так как ссесия уничтожаеться только после выключения бразерра а не закрытия вкладки

// Проверка существования ссесии
if($_SESSION['acount']['name'] != '' and  $_SESSION['acount']['remember'] == 'Yes' )
{

    // Проверка равенства куки и ссесии акаунт
    if($_COOKIE['acount'] == $_SESSION['acount'])
    {	

        // Продление куки
        setcookie("acount", time()+60*60*24*30);
      
      }
      else
      {

        // Задание куки акаунт равными ссесия акаунт
        setcookie("acount", json_encode($_SESSION['acount']), time()+60*60*24*30);
      }
    
     // Проверка существования ссесии акаунт         
} else 
    if(isset($_COOKIE['acount']))
    {

        // Проверка корректности куки акаунт
        foreach ($autho as &$acco) 
        {
            if( $acco['name'] == $_COOKIE['acount']['name'] and 
            $acco['password'] == $_COOKIE['acount']['password'] and 
            $acco['id'] == $_COOKIE['acount']['id'])
            {
                
                // Присвоение ссесии акаунт данных куки акаунт
                $_SESSION['acount'] = $_COOKIE['acount'];
                
            } 
            else 
            {

                // Уничтожение куки акаунт
                unset($_COOKIE['acount']); 
                setcookie('acount', null, -1, '/'); 

            }
        
        }
    }

// Удаление куки если пользователь не хочет сохраняться в системе
if($_SESSION['acount']['remember'] == 'No')
{
    unset($_COOKIE['acount']); 
    setcookie('acount', null, -1, '/'); 
}

?>

<body>
    <!-- Шапка страницы -->
    <header class="flex header">
        <!-- Верхний блок шапки -->
        <div class="flex header__top">
            <!-- Блок картинки лого -->
            <div class="header-top__logo">
                <a class="header-top-logo__link" href="/blog_main.php" >
                    <img class="header-top-logo-link__img" src="/img/singularis-1.png" alt="Лого">
                </a>
            </div>
            <!-- Название страницы -->
            <h1 class="header-top__heading">
                Сингулярность <br> Путь в бесконечность
            </h1>
        </div>
        <!-- Нижний блок шапки -->
        <div class="flex header__bottom">
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
            <!-- Блок кнопки mp3 плеера (Пока не работает) -->
            <div class="header-bottom__the-player">
                <button class="btn-reset header-bottom-the-player__note">
                    <svg class="svg" viewBox="0 -0.5 17 17" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="si-glyph si-glyph-music-note">
                        <path d="M6.021,2.188 L6.021,11.362 C5.46,11.327 4.843,11.414 4.229,11.663 C2.624,12.312 1.696,13.729 2.155,14.825 C2.62,15.924 4.294,16.284 5.898,15.634 C7.131,15.134 7.856,14.184 7.965,13.272 L7.958,4.387 L15.02,3.028 L15.02,9.406 C14.422,9.343 13.746,9.432 13.076,9.703 C11.471,10.353 10.544,11.77 11.004,12.866 C11.467,13.964 13.141,14.325 14.746,13.675 C15.979,13.174 16.836,12.224 16.947,11.313 L16.958,0.00199999998 L6.021,2.188 L6.021,2.188 Z"></path>
                    </svg>
                </button>
            </div>
            <?php
                // Проверка существования ссесии акаунт
                if(isset($_SESSION['acount'] ))
                {
                    ?>
                    <!-- Вывод кнопки перехода в личный кабинет -->
                    <a href="/blog_my-page.php">
                        <div class="header-bottom__my-page">
                            <button class="btn-reset header-bottom-my-page__btn">
                                <img class="header-bottom-my-page-btn__img " src="/img/a<?php echo $_SESSION['acount']['avatar'] ?>.png" alt="">
                            </button>
                        </div>
                    </a>
                    
                <?php
                } 
                else 
                {
                    ?>
                    <!--  Вывод кнопок регистрации и авторизации -->
                    <a href="/blog_signup.php" >
                        <div class="header-bottom__user">
                            <button class="btn-reset header-bottom-user__btn">
                                <svg class="svg" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="32px" height="32px" viewBox="0 0 459 459" style="enable-background:new 0 0 459 459;" xml:space="preserve">
                                    <path d="M229.5,0C102.53,0,0,102.845,0,229.5C0,356.301,102.719,459,229.5,459C356.851,459,459,355.815,459,229.5
                                        C459,102.547,356.079,0,229.5,0z M347.601,364.67C314.887,393.338,273.4,409,229.5,409c-43.892,0-85.372-15.657-118.083-44.314
                                        c-4.425-3.876-6.425-9.834-5.245-15.597c11.3-55.195,46.457-98.725,91.209-113.047C174.028,222.218,158,193.817,158,161
                                        c0-46.392,32.012-84,71.5-84c39.488,0,71.5,37.608,71.5,84c0,32.812-16.023,61.209-39.369,75.035
                                        c44.751,14.319,79.909,57.848,91.213,113.038C354.023,354.828,352.019,360.798,347.601,364.67z" />
                                </svg>
                            </button>
                        </div>
                    </a>
                    <a href="/blog_login.php">
                        <div class="header-bottom__login">
                            <button class="btn-reset header-bottom-login__btn">
                                <svg class="svg" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                        viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                                    <path  d="M379.955,269.328c0.229-0.341,0.427-0.696,0.635-1.046c0.192-0.322,0.394-0.634,0.57-0.965
                                        c0.194-0.362,0.357-0.731,0.53-1.099c0.162-0.342,0.333-0.678,0.477-1.029c0.15-0.36,0.27-0.728,0.4-1.093
                                        c0.134-0.373,0.278-0.741,0.394-1.123c0.112-0.37,0.194-0.744,0.288-1.118c0.098-0.386,0.205-0.766,0.283-1.16
                                        c0.086-0.437,0.139-0.875,0.202-1.315c0.046-0.336,0.11-0.666,0.144-1.006c0.157-1.578,0.157-3.166,0-4.744
                                        c-0.034-0.341-0.098-0.672-0.146-1.01c-0.061-0.437-0.114-0.877-0.2-1.31c-0.078-0.395-0.186-0.776-0.285-1.165
                                        c-0.093-0.371-0.174-0.744-0.286-1.112c-0.115-0.384-0.261-0.755-0.395-1.131c-0.13-0.362-0.248-0.726-0.397-1.083
                                        c-0.147-0.355-0.32-0.694-0.483-1.04c-0.171-0.363-0.333-0.73-0.523-1.086c-0.179-0.336-0.386-0.656-0.579-0.982
                                        c-0.206-0.344-0.402-0.694-0.627-1.03c-0.245-0.368-0.52-0.715-0.786-1.067c-0.205-0.272-0.394-0.552-0.61-0.818
                                        c-0.506-0.614-1.037-1.206-1.6-1.768l-71.994-71.995c-9.37-9.373-24.566-9.373-33.941,0c-9.373,9.373-9.373,24.568,0,33.941
                                        l31.032,31.032H24c-13.254,0-24,10.744-24,24c0,13.254,10.746,24,24,24h278.054l-31.029,31.029c-9.373,9.373-9.373,24.568,0,33.941
                                        c4.686,4.686,10.829,7.03,16.97,7.03c6.142,0,12.285-2.342,16.97-7.03l71.997-71.995c0.56-0.56,1.091-1.15,1.594-1.765
                                        c0.226-0.272,0.419-0.56,0.629-0.838C379.445,270.027,379.714,269.688,379.955,269.328z"/>
                                    <path  d="M488,472H184c-13.254,0-24-10.746-24-24v-96.002c0-13.256,10.746-24,24-24s24,10.744,24,24V424h256
                                        V88H208v72c0,13.254-10.746,24-24,24s-24-10.746-24-24V64c0-13.256,10.746-24,24-24h304c13.254,0,24,10.744,24,24v384
                                        C512,461.256,501.254,472,488,472z"/>
                                </svg>
                            </button>
                        </div>
                    </a>
                    <?php
                }
                    
                
            ?>
            
        </div>
    </header>
</body>

</html>