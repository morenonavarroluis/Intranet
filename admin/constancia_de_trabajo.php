<?php

require "config/conexionProvi.php";
require ('fpdf/fpdf.php');


$id = $_GET['edi'];
$sql = "SELECT NAME, SURNAME, CEDULA, ASSIGNED_AREA FROM user_datos WHERE IDDATOS = '$id'";

$resultado = $mysqli->query($sql);

class PDF extends FPDF
{
    // Cargar los datos
function LoadData($file)
{
    // Leer las líneas del fichero
    $lines = file($file);
    $data = array();
    foreach($lines as $line)
        $data[] = explode(';',trim($line));
    return $data;
}

// Cabecera de página
function Header()
{
    // Logo
    
    $this->Image('i.jpg' , 5,8, 200, -150,'JPG');
    $this->Ln(20);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    
     $this->Cell(90);
    // Título
    // $this->Cell($this->GetPageWidth(),10,'Constancia de trabajo',1,0,'C');
     $this->Cell(1,10,'Constancia de trabajo',0,1,'C');
    // Salto de línea
    $this->Ln(3);
}

// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Número de página
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
}


// Creación del objeto de la clase heredada
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
$pdf->MultiCell(0, 7, utf8_decode("Quien suscribe, NELSON SANCHEZ GONZALEZ, Director General (E) de la Oficina de
Gestión Humana de Industria Canaima C.A., hace constar mediante el presente documento
y, a petición de la parte interesada, que el (la) ciudadano(a) presta sus servicios para esta"), 0, 'C');

$pdf->Cell(19);
$pdf->Cell(0,7,'institucion, la cual se detalla a continuacion:',0,1, 'L');

$pdf->Ln(10);
while ($row = $resultado->fetch_assoc()) {
$pdf->SetFont('Times','B',10);
$pdf->Cell(19);
$pdf->MultiCell(0, 0, utf8_decode("APELLIDOS Y NOMBRES:") , 0, 'L');
$pdf->Cell(65);
$pdf->MultiCell(0, 0, utf8_decode($row['NAME'] ) , 0,'L');
$pdf->Cell(90);
$pdf->MultiCell(0, 0, utf8_decode($row['SURNAME'] ) , 0,'L');
$pdf->Ln(6);
$pdf->Cell(19);
$pdf->MultiCell(0, 0, utf8_decode("CEDULA IDENTIDAD Nº :") , 0, 'L');
$pdf->Cell(65);
$pdf->MultiCell(0, 0, utf8_decode($row['CEDULA']) , 0, 'L');
$pdf->Ln(3);
$pdf->Cell(19);
$pdf->MultiCell(0, 7, utf8_decode("FECHA DE INGRESO :"), 0, 'L');
$pdf->Ln(3);
$pdf->Cell(19);
$pdf->MultiCell(0, 0, utf8_decode("CARGO :"), 0, 'L');
$pdf->Cell(35);
$pdf->MultiCell(0, 0, utf8_decode($row['ASSIGNED_AREA']), 0, 'L');
$pdf->Ln(3);
$pdf->Cell(19);
$pdf->MultiCell(0, 7, utf8_decode("UBICACION ADMINISTRATIVA:"), 0, 'L');
}
$pdf->Ln(3);
$pdf->SetFont('Times','',10);
$pdf->Cell(19);
$pdf->Cell(0,10,'Asimismo se indica que devenga una remuneracion mensual, tal y como se detalla a continuacion ',0,1);
$pdf->Cell(0,10,'',0,1);

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(55);
$pdf->Cell(50,5,'Concepto',1,0,'L');
$pdf->Cell(40,5,'Monto',1,1 ,'R');
$pdf->SetFont('Times', '', 10);
$pdf->Cell(55);
$pdf->Cell(50,5,'Salario Base',1,0,'L');
$pdf->Cell(40,5,'194,00',1,1,'R');
$pdf->Cell(55);
$pdf->Cell(50,5,'Prima de Profesionalizacion',1,0,'L');
$pdf->Cell(40,5,'0,00',1,1,'R');
$pdf->Cell(55);
$pdf->Cell(50,5,'Prima de Antiguedad',1,0,'L');
$pdf->Cell(40,5,'7,76',1,1,'R');
$pdf->Cell(55);
$pdf->Cell(50,5,'Prima por hijos',1,0,'L');
$pdf->Cell(40,5,'25,00',1,1,'R');
$pdf->Cell(55);
$pdf->Cell(50,5,'Complemente Especial TIC',1,0,'L');
$pdf->Cell(40,5,'19,40',1,1,'R');
$pdf->Cell(55);
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(50,5,'TOTAL',1,0,'L');
$pdf->Cell(40,5,'226,76',1,1,'R');
$pdf->SetFont('Times', '', 10);
$pdf->Ln(10);
$pdf->SetFont('Times','',12);
$pdf->MultiCell(0, 4, utf8_decode("Asimismo, se percibe el Cestacket Socialista y en Bono Contra la Guerra Económico de"), 0, 'C');
$pdf->MultiCell(0, 4, utf8_decode("conformidad a los establecido en la Gaceta Oficial Extraordinaria N° 6471 de fecha 02 "), 0, 'C');
$pdf->Cell(8);
$pdf->MultiCell(0, 4, utf8_decode("de mayo de 2023, Decreto N° 4.805, de la fecha 01 de mayo del 2023 los cuales se le harán"), 0, 'C');
$pdf->Cell(8);
$pdf->MultiCell(0, 4, utf8_decode("ajustes mensuales, de acuerdo a lo establecido en los artículos 1, 2 y 5 de la Gaceta Oficial."), 0, 'C');


$pdf->Ln(5);
$pdf->Cell(8);
$pdf->MultiCell(0, 4, utf8_decode("Constancia que se expide a petición de la parte interesada, en la ciudad de Caracas al día 21"), 0, 'C');
$pdf->Cell(20);
$pdf->MultiCell(0, 4, utf8_decode("de noviembre de 2023"), 0, 'L');
$pdf->Ln(15);


$pdf->SetFont('Times', 'B', 12);
$pdf->MultiCell(0, 4, utf8_decode("NELSON SANCHEZ GONZALEZ"), 0, 'C');
$pdf->SetFont('Times', 'B', 8);
$pdf->MultiCell(0, 4, utf8_decode("DIRECTOR GENERAL (E) OFICINA DE GESTION HUMANO"), 0, 'C');
$pdf->MultiCell(0, 4, utf8_decode("PROVIDENCIA ADMINISTRATIVA Nº MPPCYT-IC- PRES 004/11/2023 DEL 01/11/2023"), 0, 'C');
$pdf->Ln(6);
$pdf->SetFont('Times', '', 8);
$pdf->MultiCell(0, 4, utf8_decode("BASE AREA GENERALISIMO FRANCISCO DE MIRANDA, ENTRE COMPLEJO TECNOLOGICO Y PRESCOLAR - "), 0, 'C');
$pdf->MultiCell(0, 4, utf8_decode("LA CARLOTA - CARACAS. "), 0, 'C');
$pdf->SetFont('Times', '', 8);
$pdf->MultiCell(0, 4, utf8_decode("ggh.canaima@gmail.com / 0212-234.41.76 / 0212-347.70.29 / 0426-5119448 "), 0, 'C');
$pdf->Output();




?>