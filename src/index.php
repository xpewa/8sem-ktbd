<?php
	session_start();
?>

<html>
	<head>
		<meta charset="utf-8">
	</head>
<body>


<!-- task 3 -->

	<?php
		global $mysql, $login, $password;

		function connect_to_db() {
			global $mysql, $login, $password;
			try {
				if (isset($_SESSION['login']) && isset($_SESSION['password'])) {
					$login = $_SESSION["login"];
					$password = $_SESSION["password"];
				}
				$mysql = new mysqli("mysql", $login, $password, "db");
				if ($mysql && !$mysql->connect_error) {
					$_SESSION['login'] = $login;
					$_SESSION['password'] = $password;
				}
			} catch (Exception $e)  {
					echo "<p>Вы не смогли подключиться к базе данных. Попробуйте ещё раз.</p>";
				}
		}

		function echo_login_form() {
			echo 
				'<form action="index.php" method="post"> 
					<p>Login: <input type="text" name="login"></p>
					<p>Password: <input type="text" name="password"></p>
					<input type="submit" value="Войти">
				</form>';
		}

		function echo_logout_button() {
			echo 
				'<form action="index.php" method="post"> 
				<input type="submit" name="logout" value="Выйти">
				</form>';
		}

		function echo_table_data() {
			global $mysql;
			if (!$mysql) {
				connect_to_db();
			}
			if ($mysql) {
				$tables = $mysql->query("SHOW TABLES FROM db");
				foreach ($tables as $table) {
					$table_name = $table["Tables_in_db"];
					//echo "<p><h3>Таблица ", $table_name, "</h></p>";
					$all_table_data = $mysql->query("SELECT * FROM " . $table_name);
					$column_data = $mysql->query("SHOW COLUMNS FROM " . $table_name);

					echo "<p><table border=1><thead><tr>";
        			while ($i = $column_data->fetch_assoc()) {
            				echo '<th><center>'.$i["Field"].'</center></th>';
        			}

					foreach ($all_table_data as $row) {
						echo "<tr>";
						foreach ($row as $value) {
							echo "<td><center>", $value, "</center></td>";
						}
						echo "</tr>";
					}
					echo "<p><h3>Таблица ", $table_name, "</h></p>";
				}
				
			}
		}

		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if (!empty($_POST["logout"])) {
				if ($mysql) {
					mysqli_close($mysql);
					$mysql = null;
				}
				unset($_SESSION["login"]);
				unset($_SESSION["password"]);
			}
			elseif (!empty($_POST["login"]) && !empty($_POST["password"])) {
				$login = $_POST["login"];
				$password = $_POST["password"];
				connect_to_db();
			}
		}
		if (isset($_SESSION['login'])) {
			echo '<p>Добро пожаловать!</p>';
			echo_logout_button();
			echo_table_data();
		}
		else {
			echo_login_form();
		}	
	?>



<!-- task 1 -->
	<!--
	<?php
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if (!empty($_POST["animals"])) {
				$favorite_animal = $_POST["animals"];
				if ($favorite_animal == "cat") {
					$message = "Вы кошколюб";
				}
				elseif ($favorite_animal == "dog") {
					$message = "Вы собаколюб";
				}
				elseif ($favorite_animal == "catAndDog") {
					$message = "Вы хороший человек";
				}
			}
			else {
				$message = "Сделайте выбор";
			}
		}
		else {
			$message = "Вам предстоит трудный выбор";
		}
		echo "<p>", $message, "</p>";
	?>

	<form action="index.php" method="POST">
		<p><select name="animals">
			<option value="" selected disabled>Кошки или собаки?</option>
			<option value="cat">Кошки, конечно</option>
			<option value="dog">Собаки, несомненно</option>
			<option value="catAndDog">Не могу определиться</option>
		</select></p>
		<p>
			<input type="submit" value="Ответить">
		</p>
	</form>
	-->
	


<!-- example 1 -->
	<!--
	<center><b>Пример 1: Варианты объявления PHP скрипта</b></center>
	<? echo "1. Простейший способ, но возможен конфликт при использовании XML"; ?> <br>
	<?php echo("2. Наиболее распространенный способ"); ?> <br>
	<% echo ("3. Начиная с PHP 3.0.4 можно факультативно применять ASP-теги, но они не обрабатываются"); %> <br>
	<script language="php">
		echo("4. Используется для лучшей совместимости с HTML редакторами, но не отображается на экране");
	</script>
	-->


<!-- example 2 -->
	<!--
	<?php 
	$a = 1;
	$b = 2;
	$c = $a + $b;
	echo "Результат сложения переменных а=1 и b=2 равен ", $c; 
	?>
	-->


<!-- example 3 -->
	<!--
	<form action="index.php" method="post">
		Name: <input type="text" name="name"><br>
		<input type="submit">
	</form>

	<?php
		echo "name = ", $_POST["name"];
	?>
	-->


<!-- example 4 -->
	<!--
	<?php
		echo "<P>";
		echo "name = ", $_GET["name"];
		echo "<P>";
		echo "e-mail = ", $_GET["e-mail"];
	?>

	<form action="index.php" method="get">
		Name: <input type="text" name="name"><br>
		E-mail: <input type="text" name="e-mail"><br>
			  <input type="submit">
	</form>
	-->


<!-- example 5 -->
	<!--
	<?php
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			echo "Мой любимый предмет: <i>" , $_POST["kurs"], "</i><br>";
			
			$favorite_time = count($_POST["times"]);
			if ($favorite_time <= 1) {
				$message = "не ботан";
			}
			elseif ($favorite_time > 1 && $favorite_time < 4) {
				$message = "ботаю иногда";
			}
			else {
				$message = "ботан";
			}
			echo "Я <i>" , $message, "</i><br>";
		}
	?>

	<form action="index.php" method="POST">
		Любимый предмет:<br>
		<input type="radio" name="kurs" value="KTБД">Конструкторско-технологические базы данных<br>
		<input type="radio" name="kurs" value="СФМ">Системы функционального моделирования<br>
		<input type="radio" name="kurs" value="СИИ">Системы искусственного интеллекта<br>

		Когда вы предпочитаете его изучать:<br>
		<input type="checkbox" name="times[]" value="m">За завтраком
		<input type="checkbox" name="times[]" value="n">В обед
		<input type="checkbox" name="times[]" value="d">За ужином
		<input type="checkbox" name="times[]" value="l">Поздно почью

		<P>
		<input type=hidden name="stage" value="results">
		<input type=submit value="Всегда!">
	</form>
	-->


<!-- example 6 -->
	<!--
	<?php
	$mysql = new mysqli("mysql", "root", "root", "db");
	
	$result = $mysql->query("SELECT * FROM ex6");

	foreach ($result as $row) {
		echo "id = ", $row["id"], " object_name = ", $row["object_name"], " object_type = ", $row["object_type"], '<br>';
	}

	mysqli_close($mysql);
	?>
	-->


<!-- example 7 -->
	<!--
	<?php
	$mysql = new mysqli("mysql", "root", "root", "db");
	
	$result = $mysql->query("SELECT * FROM ex6");

	echo "<center><table border=1>
		  <tr>
		  <td><center>id</center>
		  <td><center>object_name</center>
		  </td><td><center>object_type</center></td>
		  </tr>";
	foreach ($result as $row) {
		echo "<tr><td>", $row["id"], "</td>", 
		"<td>", $row["object_name"], "</td>",
		"<td>", $row["object_type"], "</td></tr>";
	}
	echo "</table></center>";

	mysqli_close($mysql);
	?>
	-->


<!-- example 8 and task 2 -->
	<!--
	<?php
	$mysql = new mysqli("mysql", "root", "root", "db");
	
	$result = $mysql->query("SELECT ROUND(SIN(3.14), 5) AS _sin, ROUND(COS(3.14), 5) AS _cos, ROUND(SQRT(3.14), 5) AS _sqrt FROM dual");
	$row = $result->fetch_assoc();
	echo "sin(3.14) = ", htmlentities($row['_sin']), "<br>",
		 "cos(3.14) = ", htmlentities($row['_cos']), "<br>",
		 "sqrt(3.14) = ", htmlentities($row['_sqrt']);

	mysqli_close($mysql);
	?>
	-->


</body>
</html>
