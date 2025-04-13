<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_studente = $_POST["id_studente"];
    $id_classe = $_POST["id_classe"];

    $stmt = $conn->prepare("INSERT INTO studenti_classi (id_studente, id_classe) VALUES (?, ?)");
    $stmt->bind_param("ii", $id_studente, $id_classe);
    $stmt->execute();
    $stmt->close();
}

header("Location: gestione_studenti_classi.php");
exit;
?>