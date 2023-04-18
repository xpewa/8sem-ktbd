<?php
    //function generate_qr($table_name) {

        if (!isset($GLOBALS['mysql']) and isset($_SESSION['login']) and isset($_SESSION['password'])) {
			connect_to_db($_SESSION['login'], $_SESSION['password']);
		}
        $table_name = $_GET['table'];

       echo '<img src="http://chart.apis.google.com/chart?cht=qr&chs=300x300&chl=http://127.0.0.1/dz/main.php?table_name='."$table_name".'">';
    //}
?>
