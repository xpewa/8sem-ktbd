<?php
    if(!isset($_SESSION)) 
	    session_start();

    //global $mysql;
    //include('main.php');
    include_once('mysql_con.php');
    include_once('admin.php');


    function go_to_main() {
        ob_start();
        header('Location: main.php');
        ob_end_flush();
        exit();
    }


    function echo_login_form() {
        echo 
            '<html>
            <head>
                <meta charset="utf-8">
                <title>АСУ/Вход/Регистрация</title>
                <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
                <link href="styles.css" rel="stylesheet">
            </head>
            <body class="text-center loginBody">
                <main class="form-signin">
                    <form action="login.php" method="post"> 
                        <h1 class="h3 mb-3 fw-normal">Пожалуйста, войдите или зарегистрируйтесь</h1>
                    
                        <div class="form-floating">
                            <input type="text" name="login" class="form-control" id="loginInput" placeholder="login">
                            <label for="loginInput">Логин: </label>
                        </div>
                        <div class="form-floating mt-2">
                            <input type="password" name="password" class="form-control" id="passwordInput" placeholder="password">
                            <label for="passwordInput">Пароль: </label>
                        </div>

                        <button class="mt-3 w-100 btn btn-lg btn-secondary" type="submit" name="btn_login" value="btn_login">Войти</button>
                        <button class="mt-3 w-100 btn btn-lg btn-secondary" type="submit" name="btn_register" value="btn_register">Зарегистрироваться</button>
                    </form>
                </main>
                <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
            </body>
            </html>';
    }


    function set_user_role() {
        //global $mysql;
        try {
            if (isset($_SESSION['role']))
                return true;

            if (!isset($GLOBALS['mysql'])) {
                $GLOBALS['mysql'] = new mysqli("mysql", "root", "root", "dz");
            }

            $role = $GLOBALS['mysql']->query("SELECT CURRENT_ROLE()");
            foreach ($role as $r) {
                if ($r['CURRENT_ROLE()'] == "`super_admin`@`%`") {
                    $_SESSION['role'] = 'admin';
                    return true;
                } 
                elseif ($r['CURRENT_ROLE()'] == '`super_staff`@`%`') {
                    $_SESSION['role'] = 'staff';
                }
                elseif ($r['CURRENT_ROLE()'] == '`super_viewer`@`%`' and empty($_SESSION['role'])) {
                    $_SESSION['role'] = 'viewer';
                }
                else {
                    echo "
                        <html>
                        <head>
                            <meta charset='utf-8'>
                            <title>АСУ</title>
                            <link href='bootstrap/css/bootstrap.min.css' rel='stylesheet'>
                            <link href='styles.css' rel='stylesheet'>
                        </head>
                        <body>
                        <div class='m-3'>
                            <p class='text-danger'>Ваша роль странная: " .$r['CURRENT_ROLE()']. ". Уходите.</p>
                            <form action='login.php' method='post'> 
                                <button type='submit' name='logout' value='logout' class='btn btn-secondary mt-1'>Выйти</button>
                            </form>
                        </div>";
                    return false;
                }
            }
            return true;
        } catch (Exception $e)  {
            echo "<p>Произошла ошибка определения роли, попробуйте перезайти.</p>";
            return false;
        }
    }

    function get_count_users_with_login($login) {
        $mysql = new mysqli("mysql", "root", "root", "dz");
        $result = $mysql->query('SELECT COUNT(User) FROM mysql.user WHERE User="'.$login.'";');
        return (int)$result->fetch_row()[0];
    }



    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["logout"])) {
            if (isset($GLOBALS['mysql'])) {
                mysqli_close($GLOBALS['mysql']);
                unset($GLOBALS['mysql']);
            }
            unset($_SESSION["login"]);
            unset($_SESSION["role"]);
        }

        if (!empty($_POST["btn_login"])) {
            if (!connect_to_db($_POST["login"], $_POST["password"])) {
                if (!get_count_users_with_login($_POST["login"])) {
                    echo "<section>
                            <p class='text-danger'>Вы не смогли подключиться к базе данных. Попробуйте ещё раз.</p>
                        </section>";
                }
                else {
                    $_SESSION['login'] = $_POST['login'];
                    $_SESSION['password'] = $_POST['password'];
                }
            }
        }

        elseif (!empty($_POST["btn_register"])) {
            $count_users = get_count_users_with_login($_POST["login"]);
            if ($count_users === 0) {
                try {
                    $mysql = new mysqli("mysql", "root", "root", "dz");
                    $result = $mysql->query("CREATE USER ".$_POST['login']." IDENTIFIED WITH caching_sha2_password BY '".$_POST['password']."';"); 
                    $result = $GLOBALS['mysql']->query("GRANT 'none' TO '".$_POST['login']."';"); 
                    $result = $GLOBALS['mysql']->query("SET DEFAULT ROLE 'none' TO '".$_POST['login']."';");
                    if ($result == 1) {
                        echo "<div class='m-3'><p class='text-success'>Регистрация прошла успешно.</p></div>";
                        $_SESSION['login'] = $_POST['login'];
                        $_SESSION['password'] = $_POST['password'];
                    } 
                } catch (Exception $e)  {
                    print_r($e);
                    echo "<section>
                            <p class='text-danger'>При регистрации произошла неизвестная ошибка.</p>
                        </section>";
                }
            }
            else {
                print_r("<p class='text-danger'>Не удалось зарегистрироваться. Пользователь с таким логином уже существует.");
            }
        }
    }

    if (isset($_SESSION['login'])) {
        if (set_user_role()) {
            go_to_main();
        }
    }
    else {
        echo_login_form();
    }	
?>