<?php
require_once "../../config/conexion.php";

$id = $_GET['id'];

$conexion->query("
    UPDATE usuarios 
    SET estado = IF(estado='activo','inactivo','activo')
    WHERE id_usuario = $id
");

header("Location: listar.php");
