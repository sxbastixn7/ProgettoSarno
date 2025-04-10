<?php
require_once 'config.php';

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $stmt = $conn->prepare("DELETE FROM insegnamenti WHERE id_insegnamento = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: dashboard_admin.php");
exit;
?>