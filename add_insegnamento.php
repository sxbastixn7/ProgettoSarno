<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_professore = $_POST["id_professore"];
    $id_materia = $_POST["id_materia"];
    $id_classe = $_POST["id_classe"];

    $stmt = $conn->prepare("INSERT INTO insegnamenti (id_professore, id_materia, id_classe) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $id_professore, $id_materia, $id_classe);
    $stmt->execute();
    $stmt->close();
}

header("Location: gestione_insegnamento.php");
exit;
?>