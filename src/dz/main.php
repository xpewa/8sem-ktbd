<?php
	if(!isset($_SESSION)) 
		session_start();


	include_once('header.php');
	include_once('mysql_con.php');
	include_once('table.php');
	include_once('admin.php');

	//function echo_main_page() {
		echo 
		'<html>
			<head>
				<meta charset="utf-8">
				<title>АСУ/Главная страница</title>
				<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
				<link rel="stylesheet" href="table.css">
				<!--<link rel="stylesheet" href="modal.css">-->
				<link href="styles.css" rel="stylesheet">
			</head>
		<body style="background-color: #fff;">';

		echo_header();

		echo '<div class="container-fluid"><div class="row" style="height: 92%;">';

		if (!isset($_SESSION['role']))
			die();

		if (!isset($GLOBALS['mysql']) and isset($_SESSION['login']) and isset($_SESSION['password'])) {
			connect_to_db($_SESSION['login'], $_SESSION['password']);
		}

		echo_table_header();

		echo '<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">';

		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			if (!empty($_GET['all'])) {
				echo_all_tables_data();
			}
			if (!empty($_GET['table_name'])) {
				echo_table_data($_GET['table_name']);
			}
			if (!empty($_GET['user'])) {
				echo_table_data('mysql.default_roles');
			}
			//if (!empty($_GET['qr'])) {
			//	generate_qr($_GET['qr']);
			//}
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$json_body = json_decode(file_get_contents('php://input'));
			if ($json_body && $json_body->type == 'update') {
				$table_name = $json_body->table_name;
				if ($table_name != 'mysql.default_roles') {
					$cols = get_column_data($table_name)->fetch_all();
					if (!isset($GLOBALS[$table_name])) {
						set_table_data($table_name);
					}
					$col_name = $cols[$json_body->col][0];
					echo 'col_name: '.$col_name.'   
							';
					$old_data = '';
					foreach ($cols as $col) {
						$cell_val = $GLOBALS[$table_name][$json_body->row][$col[0]];
						if (!is_null($cell_val)) {
							$old_data = $old_data.$col[0].'="'.$cell_val.'" AND ';
						}
						else {
							$old_data = $old_data.$col[0].' IS NULL AND ';
						}
					}
					$old_data = substr($old_data, 0, -5);
					$new_val = $json_body->value;
					if ($new_val!="") {
						$cell_val = $GLOBALS[$table_name][$json_body->row][$col[0]];
						$new_data = $col_name.'="'.$new_val.'"';
					}
					else {
						$new_data = $col_name.'=NULL';
					}
					update_row_data($table_name, $old_data, $new_data);
				}
				else {
					if ($json_body->col == 0){
						set_table_data($table_name);
						$cell_user = $GLOBALS[$table_name][$json_body->row]["USER"];
						//$cell_role = $GLOBALS[$table_name][$json_body->row]["DEFAULT_ROLE_USER"];
						update_row_data("mysql.user", $cell_user, $json_body->value);
					}
					if ($json_body->col == 1){
						set_table_data($table_name);
						$cell_user = $GLOBALS[$table_name][$json_body->row]["USER"];
						//$cell_role = $GLOBALS[$table_name][$json_body->row]["DEFAULT_ROLE_USER"];
						update_row_data("mysql.default_roles", $cell_user, $json_body->value, 1);
					}
				}
			}

			if (isset($_POST['delete'])) {
				$table_name = $_POST['table_name'];
				$row_data = '';
				if ($table_name == 'mysql.default_roles') {
					$cols = get_column_data($table_name);
				}
				else {
					$cols = get_column_data($table_name)->fetch_all();
				}
				if (!isset($GLOBALS[$table_name])) {
					set_table_data($table_name);
				}
				foreach ($cols as $col) {
					if ($table_name == 'mysql.default_roles') {
						$col_name = $col;
					}
					else {
						$col_name = $col[0];
					}
					$cell_val = $GLOBALS[$table_name][$_POST['delete']][$col_name];
					if (!is_null($cell_val)) {
						$row_data = $row_data.$col_name.'="'.$GLOBALS[$table_name][$_POST['delete']][$col_name].'" AND ';
					}
					else {
						$row_data = $row_data.$col_name.' IS NULL AND ';
					}
				}
				$row_data = substr($row_data, 0, -5);
				delete_row($_POST['table_name'], $row_data);
			}

			if (isset($_POST['insert'])) {
				$table_name = $_POST['table_name'];
				if ($table_name != 'mysql.default_roles') {
					$cols = "";
					$values = "";
					foreach ($_POST as $key => $value) {
						if ($key != 'insert' and $key != 'table_name') {
							$cols = $cols.$key.', ';
							if ($value != "") {
								$values = $values.'"'.$value.'", ';
							}
							else {
								$values = $values.'NULL, ';
							}
						}

					}
					$cols = substr($cols, 0, -2);
					$values = substr($values, 0, -2);
				}
				else {
					$cols = [];
					$values = [];
					foreach ($_POST as $key => $value) {
						if ($key != 'insert' and $key != 'table_name') {
							if ($value != "") {
								array_push($values, $value);
							}
							else {
								array_push($values, NULL);
							}
						}

					}
				}
				insert_row($table_name, $cols, $values);
			}
		}

		echo
		'	
		</main>
		</div>
		</div>
		
		<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
		<script src="table.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
		</body>
		</html>';
	//}

?>