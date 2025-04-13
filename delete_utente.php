<?php
require 'config.php';
$id = $_GET["id"];
$conn->query("DELETE FROM utenti WHERE id_utente=$id");
header("Location: dashboard_admin.php");
?>