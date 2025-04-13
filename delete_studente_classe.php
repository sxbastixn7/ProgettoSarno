<?php
require_once 'config.php';

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $conn->query("DELETE FROM studenti_classi WHERE id_studente_classe = $id");
}

header("Location: dashboard_admin.php");
exit;
?>