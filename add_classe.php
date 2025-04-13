<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome_classe = $_POST["nome_classe"];

    $stmt = $conn->prepare("INSERT INTO classi (nome_classe) VALUES (?)");
    $stmt->bind_param("s", $nome_classe);
    $stmt->execute();
    $stmt->close();
}

header("Location: gestione_classi.php");
exit;
?>