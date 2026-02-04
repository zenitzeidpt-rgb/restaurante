<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}
require_once "../../config/conexion.php";

$id = $_GET['id'];
$conexion->query("DELETE FROM usuarios WHERE id_usuario = $id");

header("Location: listar.php");
	