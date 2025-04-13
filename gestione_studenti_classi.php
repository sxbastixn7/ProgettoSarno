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
    <title>Gestione Studenti-Classe</title>
    <link rel="stylesheet" href="stile.css">
</head>
<body>

<header>
    <div>
    <a href="dashboard_admin.php" style="color: white; text-decoration: none; margin-right: 20px;">Home</a>
        <a href="gestione_utenti.php" style="color: white; text-decoration: none; margin-right: 20px;">Utenti</a>
        <a href="gestione_classi.php" style="color: white; text-decoration: none; margin-right: 20px;">Classi</a>
        <a href="gestione_materie.php" style="color: white; text-decoration: none; margin-right: 20px;">Materie</a>
        <a href="gestione_insegnamento.php" style="color: white; text-decoration: none; margin-right: 20px;">Insegnamenti</a>
    </div>
    <a href="logout.php" class="logout-button">Logout</a>
</header>

<main>
    <div class="card">
        <div class="card-header">Gestione Studenti-Classe</div>
        <div class="card-content">
            <?php
            $sql = "SELECT sc.id_studente_classe, u.nome, u.cognome, u.email, c.nome_classe 
                    FROM studenti_classi sc 
                    JOIN utenti u ON sc.id_studente = u.id_utente 
                    JOIN classi c ON sc.id_classe = c.id_classe 
                    ORDER BY sc.id_studente_classe DESC";
            $result = $conn->query($sql);
            echo "<table><tr><th>ID</th><th>Studente</th><th>Email</th><th>Classe</th><th>Azioni</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['id_studente_classe']}</td>
                    <td>{$row['nome']} {$row['cognome']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['nome_classe']}</td>
                    <td><a href='edit_studente_classe.php?id={$row['id_studente_classe']}'>Modifica</a> | <a href='delete_studente_classe.php?id={$row['id_studente_classe']}'>Elimina</a></td>
                </tr>";
            }
            echo "</table>";
            ?>

            <form method="post" action="add_studente_classe.php" class="form-inline" style="margin-top: 20px; display: flex; flex-wrap: wrap; gap: 10px;">
                <select name="id_studente" required>
                    <option value="">Seleziona Studente</option>
                    <?php
                    $studenti = $conn->query("SELECT id_utente, nome, cognome FROM utenti WHERE tipo = 'studente'");
                    while ($studente = $studenti->fetch_assoc()) {
                        echo "<option value='{$studente['id_utente']}'>{$studente['nome']} {$studente['cognome']}</option>";
                    }
                    ?>
                </select>
                <select name="id_classe" required>
                    <option value="">Seleziona Classe</option>
                    <?php
                    $classi = $conn->query("SELECT id_classe, nome_classe FROM classi");
                    while ($classe = $classi->fetch_assoc()) {
                        echo "<option value='{$classe['id_classe']}'>{$classe['nome_classe']}</option>";
                    }
                    ?>
                </select>
                <button type="submit">Associa Studente</button>
            </form>
        </div>
    </div>
</main>

</body>
</html>
