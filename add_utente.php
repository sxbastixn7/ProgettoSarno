<?php
include 'config.php';

$nome = $_POST['nome'];
$cognome = $_POST['cognome'];
$email = $_POST['email'];
$password = $_POST['password'];
$tipo = $_POST['tipo'];

$query = "INSERT INTO utenti (nome, cognome, email, password, tipo) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("sssss", $nome, $cognome, $email, $password, $tipo);
$stmt->execute();

header("Location: gestione_utenti.php");
exit;
?>
