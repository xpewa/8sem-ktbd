<?php
    function connect_to_db($login, $password) {
        try {
            $GLOBALS['mysql'] = new mysqli("mysql", $login, $password, "dz");
            if ($GLOBALS['mysql'] && !$GLOBALS['mysql']->connect_error) {
                $_SESSION['login'] = $login;
                $_SESSION['password'] = $password;
                mysqli_set_charset($GLOBALS['mysql'], "utf8");
                return true;
            }
        } catch (Exception $e)  {
            return false;
        }
    }
?>