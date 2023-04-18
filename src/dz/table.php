<?php
    include_once('create.php');


    function echo_table_data($table_name) {
        
        if (!isset($GLOBALS['mysql'])) {
            echo "Что-то пошло не так... Пропало подключение.
                </body>
                </html>";
            return;
        }

        echo "<div class='d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom'>
                <h1 class='h2'>Таблица $table_name</h1>
                <div class='btn-toolbar mb-2 mb-md-0'>";
                    if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staff') {
                        echo "<button type='button' class='btn btn-sm btn-outline-secondary mx-1' data-bs-toggle='modal' data-bs-target='#modal$table_name'>Добавить запись</button>";
                    }
        echo "
                    <a class='btn btn-sm btn-outline-secondary mx-1' href='pdf.php?table=$table_name' target='_blank'>Сохранить в PDF</a>
                    <a class='btn btn-sm btn-outline-secondary mx-1' href='qrcode.php?table=$table_name' target='_blank'>QR-код</a>
                </div>
            </div>";
        
        if (!isset($GLOBALS[$table_name])) {
            set_table_data($table_name);
        }
        $column_data = get_column_data($table_name);

        echo "<table border=1 id='$table_name' class='table table-bordered table-hover'><thead><tr>";
        if ($table_name == 'mysql.default_roles') {
            foreach (get_column_data($table_name) as $field) {
                echo '<th class='.$field.'><center>'.$field.'</center></th>';
            }
        }
        else {
            while ($i = $column_data->fetch_assoc()) {
                echo '<th class='.$i["Field"].'><center>'.$i["Field"].'</center></th>';
            }
        }
        
        if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staff')
            echo '<th><center>Удалить</center></th>';
        echo '</thead>';
        echo '<tbody>';

        $i = 0;
        foreach ($GLOBALS[$table_name] as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td style='height: 100%; padding: 0;'>
                        <div class='fullSize' style='display: flex; justify-content: center; align-items: center;'>", $value, "</div>
                      </td>";
            }

            if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staff') {
                echo 
                "<td><center class='fullSize' style='height: 100%; padding: 0;'>
                    <form action='main.php' method='post' class='fullSize' style='margin: 0;'>
                        <input name='table_name' value=$table_name style='display: none;'>
                        <button name='delete' value=$i class='btn btn-light btn-block fullSize'>-</button>
                    </form>
                </center></td>";
            }
            
            echo "</tr>";
            $i++;
        }
        echo '</tbody></table>';

        echo_modal_insert($table_name);
    }

    function echo_all_tables_data() {
        if (!isset($GLOBALS['mysql'])) {
            echo 
            '<html>
                <head>
                    <meta charset="utf-8">
                    <title>АСУ/Главная страница</title>
                </head>
            <body>
                Что-то пошло не так... Пропало подключение.
            </body>
            </html>';
            return;
        }
        $tables = $GLOBALS['mysql']->query("SHOW TABLES FROM dz");
        foreach ($tables as $table) {

            $table_name = $table["Tables_in_dz"];

            echo_table_data($table_name);
        }
        if ($_SESSION['role'] == 'admin') {
            echo_table_data('mysql.default_roles');
        }
    }


    function get_column_data($table_name) {
        if (!isset($GLOBALS['mysql'])) {
            return;
        }

        if ($table_name == 'mysql.default_roles') {
            return ['USER', 'DEFAULT_ROLE_USER'];
        }
        else {
            return $GLOBALS['mysql']->query("SHOW COLUMNS FROM " . $table_name);
        }
    }


    function set_table_data($table_name) {
        if (!isset($GLOBALS['mysql'])) {
            return;
        }

        if ($table_name == 'mysql.default_roles') {
            $all_table_data = $GLOBALS['mysql']->query("SELECT USER, DEFAULT_ROLE_USER FROM " . $table_name);
        }
        else {
            $all_table_data = $GLOBALS['mysql']->query("SELECT * FROM " . $table_name);
        }

        $GLOBALS[$table_name] = $all_table_data->fetch_all(MYSQLI_ASSOC);
    }


    function update_row_data($table_name, $old_data, $new_data, $role=0) {
        if (!isset($GLOBALS['mysql']))
            return;
        if (!($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staff'))
            return;

        if ($table_name != 'mysql.default_roles' and $table_name != 'mysql.user') {
            ("Запрос:print_r UPDATE " . $table_name . " SET " . $new_data . " WHERE " . $old_data . ";\n");
            $result = $GLOBALS['mysql']->query("UPDATE " . $table_name . " SET " . $new_data . " WHERE " . $old_data . ";");
            print_r("Ответ: ".$result);
        }
        else {
            if ($role) {
                print_r("Запрос: REVOKE IF EXISTS ALL PRIVILEGES, GRANT OPTION FROM '$old_data'@'%';");
                $result = $GLOBALS['mysql']->query("REVOKE IF EXISTS ALL PRIVILEGES, GRANT OPTION FROM $old_data;"); 
                print_r("Запрос: GRANT '$new_data' TO '$old_data';");
                $result = $GLOBALS['mysql']->query("GRANT '$new_data' TO '$old_data';"); 
                print_r("Запрос: SET DEFAULT ROLE '$new_data' TO '$old_data';");
                $result = $GLOBALS['mysql']->query("SET DEFAULT ROLE '$new_data' TO '$old_data';");
            }
            else {
                print_r("Запрос: RENAME USER '$old_data' TO '$new_data';");
                $result = $GLOBALS['mysql']->query("RENAME USER '$old_data' TO '$new_data';");
            }
        }
    }


    function delete_row($table_name, $row_data) {
        if (!isset($GLOBALS['mysql']))
            return;
        if (!($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staff'))
            return;

        if ($table_name == 'mysql.default_roles') {
            $new_row_data = '';
            $i = 0;
            while ($row_data[$i] != '=') {
                $i++;
            }
            $i++;
            while ($row_data[$i] != ' ') {
                $new_row_data = $new_row_data . $row_data[$i];
                $i++;
            }
            try {
                $result = $GLOBALS['mysql']->query("DROP USER $new_row_data;");
                if ($result == 1) {
                    echo "<p class='text-success'>Пользователь успешно удалён.</p>";
                    if (substr($new_row_data, 1, -1) == $_SESSION['login']) {
                        mysqli_close($GLOBALS['mysql']);
                        unset($GLOBALS['mysql']);
                        unset($_SESSION["login"]);
                        unset($_SESSION["role"]);
                        return;
                    }
                }
            } catch (Exception $e)  {
                print_r("<p class='text-danger'>Не удалось удалить пользователя.</p>");
            }
        }
        else {
            try {
                $result = $GLOBALS['mysql']->query("DELETE FROM ".$table_name." WHERE ".$row_data.";");
                if ($result == 1) {
                    echo "<p class='text-success'>Строка успешно удалена.</p>";
                }
            } catch (Exception $e)  {
                print_r("<p class='text-danger'>Не удалось удалить строку. Проверьте, не связана ли информация в ней с другой таблицей.</p>");
            }
        }

        set_table_data($table_name);
        echo_table_data($table_name);
    }

    function insert_row($table_name, $cols, $values) {
        if (!isset($GLOBALS['mysql']))
            return;
        if (!($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staff'))
            return;
        
        if ($table_name != 'mysql.default_roles') {
            try {
                $result = $GLOBALS['mysql']->query("INSERT $table_name ($cols) VALUES ($values)");
                if ($result == 1) {
                    echo "<p class='text-success'>Строка успешно добавлена.</p>";
                }
            } catch (Exception $e)  {
                print_r("<p class='text-danger'>Не удалось добавить строку. Проверте правильность введённых данных.</p>");
            }

            set_table_data($table_name);
            echo_table_data($table_name);
        }
        else {
            try {
                $result = $GLOBALS['mysql']->query("CREATE USER $values[0] IDENTIFIED WITH caching_sha2_password BY '$values[1]';");
                if ($result == 1) {
                    echo "<p class='text-success'>Пользователь создан.</p>";
                }
            } catch (Exception $e)  {
                print_r("<p class='text-danger'>Не удалось создать пользователя. Проверте, нет ли пользователя с таким же логином.</p>");
                set_table_data($table_name);
                echo_table_data($table_name);
                return;
            }
            try {
                $result = $GLOBALS['mysql']->query("GRANT '$values[2]' TO '$values[0]';");
                if ($result == 1) {
                    echo "<p class='text-success'>Пользователю успешно назначена роль.</p>";
                }
            } catch (Exception $e)  {
                try {
                    $result = $GLOBALS['mysql']->query("GRANT 'none' TO '$values[0]';");
                    print_r("<p class='text-danger'>Не удалось назначить пользователю заданную роль. Возможно, введённой роли не существует. Пользователю назначена роль 'none'.</p>");
                } catch (Exception $e)  {}
            }
            try {
                $result = $GLOBALS['mysql']->query("SET DEFAULT ROLE '$values[2]' to '$values[0]';");
                if ($result == 1) {
                    echo "<p class='text-success'>Роль назначена как роль по-умолчанию.</p>";
                }
            } catch (Exception $e)  {
                try {
                    $result = $GLOBALS['mysql']->query("SET DEFAULT ROLE 'none' to '$values[0]';");
                    print_r("<p class='text-danger'>По-умолчанию назначена роль 'none'.</p>");
                } catch (Exception $e)  {}
            }

            set_table_data($table_name);
            echo_table_data($table_name);
        }
    }
?>