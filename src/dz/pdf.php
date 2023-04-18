<?php
    if(!isset($_SESSION)) 
        session_start();

    require('fpdf/fpdf.php');
    include_once('table.php');
    include_once('mysql_con.php');

    define('FPDF_FONTPATH', 'fpdf/font/');


    function get_widths_cells($table_name, $pdf) {
        $widths = [];
        $column_data = get_column_data($table_name);
        $j = 0;
        if ($table_name != 'mysql.default_roles') {
            while ($i = $column_data->fetch_assoc()) {
                $widths[$j] = 0;
                $j++;
            }
        }
        else {
            foreach ($column_data as $i) {
                $widths[$j] = 0;
                $j++;
            }
        }
        $column_data = get_column_data($table_name);
        $j = 0;
        if ($table_name != 'mysql.default_roles') {
            while ($i = $column_data->fetch_assoc()) {
                $cellWidth = $pdf->GetStringWidth($i["Field"]);
                if ($cellWidth > $widths[$j])
                    $widths[$j] = $cellWidth;
                $j++;
            }
        }
        else {
            foreach ($column_data as $i) {
                $cellWidth = $pdf->GetStringWidth($i);
                if ($cellWidth > $widths[$j])
                    $widths[$j] = $cellWidth;
                $j++;
            }
        }
        foreach($GLOBALS[$table_name] as $row) {
            $j = 0;
            foreach($row as $column) {
                $cellWidth = $pdf->GetStringWidth($column);
                if ($cellWidth > $widths[$j])
                    $widths[$j] = $cellWidth;
                $j++;
            }
        }
        return $widths;

    }


    class PDF extends FPDF
    {
        function Header()
        {
            global $table_name;
            $this->AddFont('TimesNewRomanPSMT','B','times.php');
            $this->SetFont('TimesNewRomanPSMT','B',12);
            // Move to the right
            $this->Cell(80);
            $this->Cell(40, 10, $table_name, 1, 0, 'C');
            $this->Ln(20);
        }
    }


    //function table_to_pdf($table_name) {

        if (!isset($GLOBALS['mysql']) and isset($_SESSION['login']) and isset($_SESSION['password'])) {
			connect_to_db($_SESSION['login'], $_SESSION['password']);
		}

        global $table_name;
        $table_name = $_GET['table'];

        $pdf = new PDF();
        $pdf->AddFont('TimesNewRomanPSMT','B','times.php');
        $pdf->AddPage();
        $pdf->SetFont('TimesNewRomanPSMT','B',12);	

        if (!isset($GLOBALS[$table_name])) {
			set_table_data($table_name);
		}

        $widths = get_widths_cells($table_name, $pdf);

        $column_data = get_column_data($table_name);


        $j = 0;
        if ($table_name != 'mysql.default_roles') {
            while ($i = $column_data->fetch_assoc()) {
                $text = iconv('utf-8', 'windows-1251', $i["Field"]);
                $pdf->Cell($widths[$j] + 5, 10, $text, 1);
                $j++;
            }
        }
        else {
            foreach ($column_data as $i) {
                $text = iconv('utf-8', 'windows-1251', $i);
                $pdf->Cell($widths[$j] + 5, 10, $text, 1);
                $j++;
            }
        }
        foreach($GLOBALS[$table_name] as $row) {
            $j = 0;
            $pdf->Ln();
            foreach($row as $column) {
                if ($column)
                    $text = iconv('utf-8', 'windows-1251', $column);
                else
                    $text = $column;
                $pdf->Cell($widths[$j] + 5, 10, $text, 1);
                $j++;
            }
        }
        $pdf->Output();
    //}
?>