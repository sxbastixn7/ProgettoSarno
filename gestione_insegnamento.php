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
    <title>Gestione Insegnamenti</title>
    <link rel="stylesheet" href="stile.css">
</head>
<body>

<header>
    <div>
        <a href="dashboard_admin.php" style="color: white; text-decoration: none; margin-right: 20px;">Home</a>
        <a href="gestione_utenti.php" style="color: white; text-decoration: none; margin-right: 20px;">Utenti</a>
        <a href="gestione_classi.php" style="color: white; text-decoration: none; margin-right: 20px;">Classi</a>
        <a href="gestione_materie.php" style="color: white; text-decoration: none; margin-right: 20px;">Materie</a>
        <a href="gestione_studenti_classi.php" style="color: white; text-decoration: none;">Studenti-Classe</a>
    </div>
    <a href="logout.php" class="logout-button">Logout</a>
</header>

<main>
    <div class="card">
        <div class="card-header">Gestione Insegnamenti</div>
        <div class="card-content">
            <?php
            $query = "
                SELECT i.id_insegnamento, u.nome AS nome_prof, u.cognome AS cognome_prof,
                       m.nome_materia, c.nome_classe
                FROM insegnamenti i
                JOIN utenti u ON i.id_professore = u.id_utente
                JOIN materie m ON i.id_materia = m.id_materia
                JOIN classi c ON i.id_classe = c.id_classe
                ORDER BY i.id_insegnamento DESC
            ";
            $result = $conn->query($query);
            echo "<table><tr><th>ID</th><th>Professore</th><th>Materia</th><th>Classe</th><th>Azioni</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['id_insegnamento']}</td>
                    <td>{$row['nome_prof']} {$row['cognome_prof']}</td>
                    <td>{$row['nome_materia']}</td>
                    <td>{$row['nome_classe']}</td>
                    <td><a href='edit_insegnamento.php?id={$row['id_insegnamento']}'>Modifica</a> | <a href='delete_insegnamento.php?id={$row['id_insegnamento']}'>Elimina</a></td>
                </tr>";
            }
            echo "</table>";
            ?>
            <form method="post" action="add_insegnamento.php" class="form-inline" style="margin-top: 20px; display: flex; flex-wrap: wrap; gap: 10px;">
                <select name="id_professore" required>
                    <option value="">-- Seleziona Professore --</option>
                    <?php
                    $professori = $conn->query("SELECT id_utente, nome, cognome FROM utenti WHERE tipo = 'prof'");
                    while ($prof = $professori->fetch_assoc()) {
                        echo "<option value='{$prof['id_utente']}'>{$prof['nome']} {$prof['cognome']}</option>";
                    }
                    ?>
                </select>
                <select name="id_materia" required>
                    <option value="">-- Seleziona Materia --</option>
                    <?php
                    $materie = $conn->query("SELECT id_materia, nome_materia FROM materie");
                    while ($mat = $materie->fetch_assoc()) {
                        echo "<option value='{$mat['id_materia']}'>{$mat['nome_materia']}</option>";
                    }
                    ?>
                </select>
                <select name="id_classe" required>
                    <option value="">-- Seleziona Classe --</option>
                    <?php
                    $classi = $conn->query("SELECT id_classe, nome_classe FROM classi");
                    while ($cls = $classi->fetch_assoc()) {
                        echo "<option value='{$cls['id_classe']}'>{$cls['nome_classe']}</option>";
                    }
                    ?>
                </select>
                <button type="submit">Aggiungi Insegnamento</button>
            </form>
        </div>
    </div>
</main>

</body>
</html>