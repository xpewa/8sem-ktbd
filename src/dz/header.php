<?php
    function echo_header() {
        if (isset($_SESSION["login"])) {
            echo
            '
            <header class="navbar sticky-top flex-md-nowrap p-0 shadow">
                <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 text-center" href="main.php">АСУ</a>
                    <span class="fs-4">Добро пожаловать, ' . $_SESSION["login"] . '! Ваша роль : ' . $_SESSION["role"] . '</span>
                <div class="navbar-nav">
                    <div class="nav-item text-nowrap">
                        <form action="login.php" method="post"> 
                            <button type="submit" name="logout" value="logout" class="btn px-3 mt-3">Выйти</button>
                        </form>
                    </div>
                </div>
            </header>
            ';
        }
        else {
            echo
            '
            <header class="navbar sticky-top flex-md-nowrap p-0 shadow">
                <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 text-center" href="main.php">АСУ</a>
                    <span class="fs-4"></span>
                <div class="navbar-nav">
                    <div class="nav-item text-nowrap">
                        <form action="login.php" method="post"> 
                            <button type="submit" name="logout" value="logout" class="btn px-3 mt-3">Выйти</button>
                        </form>
                    </div>
                </div>
            </header>
            ';
        }
    }
?>