<?php
include 'config.php';
session_start();
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'amministratore') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Classi</title>
    <link rel="stylesheet" href="stile.css">
</head>
<body>

<header>
    <div>
        <a href="dashboard_admin.php" style="color: white; text-decoration: none; margin-right: 20px;">Home</a>
        <a href="gestione_utenti.php" style="color: white; text-decoration: none; margin-right: 20px;">Utenti</a>
        <a href="gestione_materie.php" style="color: white; text-decoration: none; margin-right: 20px;">Materie</a>
        <a href="gestione_insegnamento.php" style="color: white; text-decoration: none; margin-right: 20px;">Insegnamenti</a>
        <a href="gestione_studenti_classi.php" style="color: white; text-decoration: none;">Studenti-Classe</a>
    </div>
    <a href="logout.php" class="logout-button">Logout</a>
</header>

<main>
    <div class="card">
        <div class="card-header">Gestione Classi</div>
        <div class="card-content">
            <?php
            $result = $conn->query("SELECT * FROM classi ORDER BY id_classe DESC");
            echo "<table><tr><th>ID</th><th>Nome Classe</th><th>Azioni</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['id_classe']}</td>
                    <td>{$row['nome_classe']}</td>
                    <td><a href='edit_classe.php?id={$row['id_classe']}'>Modifica</a> | <a href='delete_classe.php?id={$row['id_classe']}'>Elimina</a></td>
                </tr>";
            }
            echo "</table>";
            ?>
            <form method="post" action="add_classe.php" class="form-inline" style="margin-top: 20px; display: flex; gap: 10px;">
                <input type="text" name="nome_classe" placeholder="Nome Classe" required>
                <button type="submit">Aggiungi Classe</button>
            </form>
        </div>
    </div>
</main>

</body>
</html>
