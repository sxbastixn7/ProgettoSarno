<?php
require_once 'config.php';

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $conn->query("DELETE FROM classi WHERE id_classe = $id");
}

header("Location: dashboard_admin.php");
exit;
?>