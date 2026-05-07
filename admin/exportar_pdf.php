<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php?redirigido=true");
    exit();
}

require_once '../includes/conexion.php';
require_once '../includes/fpdf/fpdf.php';

// Obtener productos
$sql = "SELECT cod, nombre_corto, marca, nivel, forma, peso, pvp, exclusiva FROM producto";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$productos = $stmt->fetchAll();

$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

// Título
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Administracion de productos', 0, 1, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 6, 'Fecha: ' . date('d/m/Y'), 0, 1, 'C');
$pdf->Ln(4);

// Cabeceras
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

// Filas
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 8);
$fill = false;

foreach ($productos as $p) {
    $sql_stock = "SELECT unidades FROM stock WHERE producto = :cod AND tienda = 1";
    $stmt_stock = $conexion->prepare($sql_stock);
    $stmt_stock->bindValue(':cod', $p['cod']);
    $stmt_stock->execute();
    $stock = $stmt_stock->fetch();
    $unidades = $stock ? $stock['unidades'] : 0;

    $pdf->SetFillColor(235, 245, 255);

    $pdf->Cell(25,  7, $p['cod'],                             1, 0, 'C', $fill);
    $pdf->Cell(50,  7, $p['nombre_corto'],                    1, 0, 'L', $fill);
    $pdf->Cell(30,  7, $p['marca'],                           1, 0, 'C', $fill);
    $pdf->Cell(25,  7, $p['nivel'],                           1, 0, 'C', $fill);
    $pdf->Cell(25,  7, $p['forma'],                           1, 0, 'C', $fill);
    $pdf->Cell(20,  7, $p['peso'] . ' g',                     1, 0, 'C', $fill);
    $pdf->Cell(20,  7, number_format($p['pvp'], 2) . ' EUR',  1, 0, 'C', $fill);
    $pdf->Cell(20,  7, ($p['exclusiva'] ? 'Si' : 'No'),       1, 0, 'C', $fill);
    $pdf->Cell(25,  7, $unidades . ' ud',                     1, 1, 'C', $fill);

    $fill = !$fill;
}

$pdf->Output('I', 'productos.pdf');
$pdf->Output('I', 'productos.pdf'); // I = mostrar en navegador, D = descargar