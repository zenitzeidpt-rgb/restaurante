<?php
require_once "../../config/conexion.php";

$id = $_GET['id'];
$conexion->query("DELETE FROM usuarios WHERE id_usuario = $id");

header("Location: listar.php");
