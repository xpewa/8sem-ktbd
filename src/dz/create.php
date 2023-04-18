<?php
    include_once('table.php');

    $arr_int = ['int', 'tinyint unsigned'];
    $arr_char = ['varchar(255)', 'varchar(10)', 'varchar(20)', 'char(32)', 'char(255)'];
    $arr_long = ['varchar(1000)'];
    $arr_datetime = ['datetime'];


    function _echo_fields($fields_array, $fields_name_array, $table_name) {
        global $arr_int;
        global $arr_char;
        global $arr_long;
        global $arr_datetime;
    
        echo "<form action='main.php' method='post'>";

        $i = 0;
        foreach ($fields_array as $field) {
            $field_name = $fields_name_array[$i];
            echo "<div class='row mt-3'>
                <div class='col'>
                    <label>$field_name ($field): </label>
                </div>
                <div class='col'>";

            if (in_array($field, $arr_int)) {
                echo "<input type='number' name='$field_name'>";
            }

            if (in_array($field, $arr_char)) {
                echo "<input type='text' name='$field_name'>";
            }

            if (in_array($field, $arr_long)) {
                echo "<input type='text' name='$field_name'>";
            }

            if (in_array($field, $arr_datetime)) {
                echo "<input type='datetime-local' name='$field_name'>";
            }
            echo "</div>
                </div>";
            $i++;
        }

        echo 
            "<div class='row mt-4 mb-1'>
                <div class='col'></div>
                <div class='col'>
                    <input name='table_name' value=$table_name style='display: none;'>
                    <button class='btn btn-dark' name='insert'>Сохранить</button>
                </div>
                <div class='col'></div>
            </div>";

        echo "</form>";
    }

    function echo_modal_insert($table_name) {
        echo 
            "<div id='modal$table_name' class='modal fade'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h3 class='modal-title'>Добавление новой строки</h3>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Закрыть'></button>
                        </div>
                        <div class='modal-body'>";

                            if ($table_name != 'mysql.default_roles') {
                                $cols = get_column_data($table_name)->fetch_all();
                                $fields_array = [];
                                foreach ($cols as $col) {
                                    array_push($fields_array, $col[1]);
                                }
                                $fields_name_array = [];
                                foreach ($cols as $col) {
                                    array_push($fields_name_array, $col[0]);
                                }
                                _echo_fields($fields_array, $fields_name_array, $table_name);
                            }
                            else {
                                _echo_fields(['char(32)', 'char(255)', 'char(255)'], ['login', 'password', 'DEFAULT_ROLE_USER'], $table_name);
                            }
        echo
                        "</div>
                    </div>
                </div>
            </div>";
    }
?>