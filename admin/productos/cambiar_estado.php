<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}
require_once "../../config/conexion.php";

$id = $_GET['id'];

$conexion->query("
    UPDATE comidas 
    SET estado = IF(estado='activo','inactivo','activo')
    WHERE id_comida = $id
");

header("Location: listar.php");
