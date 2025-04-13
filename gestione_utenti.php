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
    <title>Gestione Utenti</title>
    <link rel="stylesheet" href="stile.css">
</head>
<body>

<header>
    <div>
        <a href="dashboard_admin.php" style="color: white; text-decoration: none; margin-right: 20px;">Home</a>
        <a href="gestione_classi.php" style="color: white; text-decoration: none; margin-right: 20px;">Classi</a>
        <a href="gestione_materie.php" style="color: white; text-decoration: none; margin-right: 20px;">Materie</a>
        <a href="gestione_insegnamento.php" style="color: white; text-decoration: none; margin-right: 20px;">Insegnamenti</a>
        <a href="gestione_studenti_classi.php" style="color: white; text-decoration: none;">Studenti-Classe</a>
    </div>
    <a href="logout.php" class="logout-button">Logout</a>
</header>

<main>
    <div class="card">
        <div class="card-header">Gestione Utenti</div>
        <div class="card-content">
            <?php
            $result = $conn->query("SELECT * FROM utenti ORDER BY id_utente DESC");
            echo "<table><tr><th>ID</th><th>Email</th><th>Nome</th><th>Cognome</th><th>Tipo</th><th>Azioni</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['id_utente']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['cognome']}</td>
                    <td>{$row['tipo']}</td>
                    <td><a href='edit_utente.php?id={$row['id_utente']}'>Modifica</a> | <a href='delete_utente.php?id={$row['id_utente']}'>Elimina</a></td>
                </tr>";
            }
            echo "</table>";
            ?>
            <form method="post" action="add_utente.php" class="form-inline" style="margin-top: 20px; display: flex; flex-wrap: wrap; gap: 10px;">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="text" name="nome" placeholder="Nome" required>
                <input type="text" name="cognome" placeholder="Cognome" required>
                <select name="tipo" required>
                    <option value="">Tipo utente</option>
                    <option value="amministratore">Amministratore</option>
                    <option value="prof">Professore</option>
                    <option value="studente">Studente</option>
                </select>
                <button type="submit">Aggiungi Utente</button>
            </form>
        </div>
    </div>
</main>

</body>
</html>
