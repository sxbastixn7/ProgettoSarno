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
    <title>Gestione Materie</title>
    <link rel="stylesheet" href="stile.css">
</head>
<body>

<header>
    <div>
    <a href="dashboard_admin.php" style="color: white; text-decoration: none; margin-right: 20px;">Home</a>
        <a href="gestione_utenti.php" style="color: white; text-decoration: none; margin-right: 20px;">Utenti</a>
        <a href="gestione_classi.php" style="color: white; text-decoration: none; margin-right: 20px;">Classi</a>
        <a href="gestione_insegnamento.php" style="color: white; text-decoration: none; margin-right: 20px;">Insegnamenti</a>
        <a href="gestione_studenti_classi.php" style="color: white; text-decoration: none;">Studenti-Classe</a>
    </div>
    <a href="logout.php" class="logout-button">Logout</a>
</header>

<main>
    <div class="card">
        <div class="card-header">Gestione Materie</div>
        <div class="card-content">
            <?php
            $result = $conn->query("SELECT * FROM materie ORDER BY id_materia DESC");
            echo "<table><tr><th>ID</th><th>Nome Materia</th><th>Azioni</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['id_materia']}</td>
                    <td>{$row['nome_materia']}</td>
                    <td><a href='edit_materia.php?id={$row['id_materia']}'>Modifica</a> | <a href='delete_materia.php?id={$row['id_materia']}'>Elimina</a></td>
                </tr>";
            }
            echo "</table>";
            ?>
            <form method="post" action="add_materia.php" class="form-inline" style="margin-top: 20px; display: flex; gap: 10px;">
                <input type="text" name="nome_materia" placeholder="Nome Materia" required>
                <button type="submit">Aggiungi Materia</button>
            </form>
        </div>
    </div>
</main>

</body>
</html>