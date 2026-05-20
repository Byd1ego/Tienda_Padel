<?php
// Inicia la sesión para poder leer los datos del usuario logueado
session_start();

// Si el usuario no es admin, lo manda al login
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php?redirigido=true");
    exit();
}

// Carga la conexión a la base de datos y la librería para crear PDFs
require_once '../includes/conexion.php';
require_once '../includes/fpdf/fpdf.php';

// Obtiene todos los productos de la base de datos
$sql = "SELECT cod_producto, nombre_corto, marca, nivel, forma, peso, pvp, exclusiva FROM producto";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$productos = $stmt->fetchAll();

// Crea un PDF en horizontal (L), en milímetros, tamaño A4
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

// Título centrado del documento
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Administracion de productos', 0, 1, 'C');

// Muestra la fecha actual debajo del título
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 6, 'Fecha: ' . date('d/m/Y'), 0, 1, 'C');
$pdf->Ln(4);

// Cabecera de la tabla con fondo azul y texto blanco
$pdf->SetFillColor(41, 128, 185);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 9);

$pdf->Cell(25,  8, 'Codigo',     1, 0, 'C', true);
$pdf->Cell(50,  8, 'Nombre',     1, 0, 'C', true);
$pdf->Cell(30,  8, 'Marca',      1, 0, 'C', true);
$pdf->Cell(25,  8, 'Nivel',      1, 0, 'C', true);
$pdf->Cell(25,  8, 'Forma',      1, 0, 'C', true);
$pdf->Cell(20,  8, 'Peso (g)',   1, 0, 'C', true);
$pdf->Cell(20,  8, 'PVP (EUR)',  1, 0, 'C', true);
$pdf->Cell(20,  8, 'Exclusiva',  1, 0, 'C', true);
$pdf->Cell(25,  8, 'Stock',      1, 1, 'C', true);

// Vuelve al color negro para las filas de datos
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 8);

// $fill alterna entre true y false para pintar filas de distinto color
$fill = false;

// Recorre cada producto y lo pinta en una fila
foreach ($productos as $p) {

    // Consulta el stock de ese producto en la tienda 1
    $sql_stock = "SELECT unidades FROM stock WHERE cod_producto = :cod AND cod_tienda = 1";
    $stmt_stock = $conexion->prepare($sql_stock);
    $stmt_stock->bindValue(':cod', $p['cod_producto']);
    $stmt_stock->execute();
    $stock = $stmt_stock->fetch();

    // Si no hay registro de stock, se considera 0
    $unidades = $stock ? $stock['unidades'] : 0;

    // Fondo azul claro para las filas alternas
    $pdf->SetFillColor(235, 245, 255);

    $pdf->Cell(25,  7, $p['cod_producto'],                             1, 0, 'C', $fill);
    $pdf->Cell(50,  7, $p['nombre_corto'],                    1, 0, 'L', $fill);
    $pdf->Cell(30,  7, $p['marca'],                           1, 0, 'C', $fill);
    $pdf->Cell(25,  7, $p['nivel'],                           1, 0, 'C', $fill);
    $pdf->Cell(25,  7, $p['forma'],                           1, 0, 'C', $fill);
    $pdf->Cell(20,  7, $p['peso'] . ' g',                     1, 0, 'C', $fill);
    $pdf->Cell(20,  7, number_format($p['pvp'], 2) . ' EUR',  1, 0, 'C', $fill);
    $pdf->Cell(20,  7, ($p['exclusiva'] ? 'Si' : 'No'),       1, 0, 'C', $fill);
    $pdf->Cell(25,  7, $unidades . ' ud',                     1, 1, 'C', $fill);

    // Cambia el valor de $fill para la siguiente fila
    $fill = !$fill;
}

// Muestra el PDF directamente en el navegador
// Opciones: 'I' = mostrar en navegador, 'D' = forzar descarga
$pdf->Output('I', 'productos.pdf');