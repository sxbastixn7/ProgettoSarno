<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] !== 'amministratore') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styleDboardA.css">
</head>
<body>
<header>
    Benvenuto, amministratore <?php echo $_SESSION["nome"] . " " . $_SESSION["cognome"]; ?>
    <a href="logout.php" class="logout-button">Logout</a>
</header>

<main>
    <div class="card-container">

        <a href="gestione_utenti.php" class="card-link">
            <div class="card">
                <div class="card-header">Gestione Utenti</div>
            </div>
        </a>

        <br>

        <a href="gestione_classi.php" class="card-link">
            <div class="card">
                <div class="card-header">Gestione Classi</div>
            </div>
        </a>

        <br>

        <a href="gestione_materie.php" class="card-link">
            <div class="card">
                <div class="card-header">Gestione Materie</div>
            </div>
        </a>

        <br>

        <a href="gestione_studenti_classi.php" class="card-link">
            <div class="card">
                <div class="card-header">Iscrizione Studenti alle Classi</div>
            </div>
        </a>

        <br>
        
        <a href="gestione_insegnamento.php" class="card-link">
            <div class="card">
                <div class="card-header">Gestione Insegnamenti</div>
            </div>
        </a>

    </div>
</main>
</body>
</html>
