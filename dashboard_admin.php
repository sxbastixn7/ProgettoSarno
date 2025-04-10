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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const headers = document.querySelectorAll(".card-header");
            headers.forEach(header => {
                header.addEventListener("click", () => {
                    const card = header.parentElement;
                    card.classList.toggle("open");
                });
            });
        });
    </script>
</head>
<body>
<header>
    Benvenuto, amministratore <?php echo $_SESSION["nome"] . " " . $_SESSION["cognome"]; ?>
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
            <form method="post" action="add_utente.php" class="form-inline">
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="password" placeholder="Password" required>
                <input type="text" name="nome" placeholder="Nome" required>
                <input type="text" name="cognome" placeholder="Cognome" required>
                <select name="tipo" required>
                    <option value="">Tipo utente</option>
                    <option value="amministratore">Amministratore</option>
                    <option value="professore">Professore</option>
                    <option value="studente">Studente</option>
                </select>
                <button type="submit">Aggiungi Utente</button>
            </form>
        </div>
    </div>

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
            <form method="post" action="add_classe.php">
                <input type="text" name="nome_classe" placeholder="Nome classe" required>
                <button type="submit">Aggiungi Classe</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Gestione Materie</div>
        <div class="card-content">
            <?php
            $result = $conn->query("SELECT * FROM materie ORDER BY id_materia");
            echo "<table><tr><th>ID</th><th>Materia</th><th>Azioni</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['id_materia']}</td>
                    <td>{$row['nome_materia']}</td>
                    <td><a href='edit_materia.php?id={$row['id_materia']}'>Modifica</a> | <a href='delete_materia.php?id={$row['id_materia']}'>Elimina</a></td>
                </tr>";
            }
            echo "</table>";
            ?>
            <form method="post" action="add_materia.php">
                <input type="text" name="nome_materia" placeholder="Nome materia" required>
                <button type="submit">Aggiungi Materia</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Iscrizione Studenti alle Classi</div>
        <div class="card-content">
            <?php
            $query = "SELECT sc.id_studente_classe, u.nome, u.cognome, c.nome_classe
                      FROM studenti_classi sc
                      JOIN utenti u ON sc.id_studente = u.id_utente
                      JOIN classi c ON sc.id_classe = c.id_classe";
            $result = $conn->query($query);
            echo "<table><tr><th>ID</th><th>Studente</th><th>Classe</th><th>Azioni</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['id_studente_classe']}</td>
                    <td>{$row['nome']} {$row['cognome']}</td>
                    <td>{$row['nome_classe']}</td>
                    <td><a href='delete_studente_classe.php?id={$row['id_studente_classe']}'>Elimina</a></td>
                </tr>";
            }
            echo "</table>";
            ?>
            <form method="post" action="add_studente_classe.php" class="form-inline">
                <select name="id_studente" required>
                    <option value="">Studente</option>
                    <?php
                    $res = $conn->query("SELECT id_utente, nome, cognome FROM utenti WHERE tipo='studente'");
                    while ($r = $res->fetch_assoc()) echo "<option value='{$r['id_utente']}'>{$r['nome']} {$r['cognome']}</option>";
                    ?>
                </select>
                <select name="id_classe" required>
                    <option value="">Classe</option>
                    <?php
                    $res = $conn->query("SELECT id_classe, nome_classe FROM classi");
                    while ($r = $res->fetch_assoc()) echo "<option value='{$r['id_classe']}'>{$r['nome_classe']}</option>";
                    ?>
                </select>
                <button type="submit">Iscrivi Studente</button>
            </form>
        </div>
    </div>

    <div class="card">
    <div class="card-header">Gestione Insegnamenti</div>
    <div class="card-content">
        <?php
        $query = "SELECT i.id_insegnamento, u.nome AS prof_nome, u.cognome AS prof_cognome, m.nome_materia, c.nome_classe
                  FROM insegnamenti i
                  JOIN utenti u ON i.id_professore = u.id_utente
                  JOIN materie m ON i.id_materia = m.id_materia
                  JOIN classi c ON i.id_classe = c.id_classe";
        $result = $conn->query($query);
        echo "<table><tr><th>ID</th><th>Professore</th><th>Materia</th><th>Classe</th><th>Azioni</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id_insegnamento']}</td>
                <td>{$row['prof_nome']} {$row['prof_cognome']}</td>
                <td>{$row['nome_materia']}</td>
                <td>{$row['nome_classe']}</td>
                <td><a href='delete_insegnamento.php?id={$row['id_insegnamento']}'>Elimina</a></td>
            </tr>";
        }
        echo "</table>";
        ?>
        <form method="post" action="add_insegnamento.php" class="form-inline">
            <select name="id_professore" required>
                <option value="">Professore</option>
                <?php
                $res = $conn->query("SELECT id_utente, nome, cognome FROM utenti WHERE tipo='professore'");
                while ($r = $res->fetch_assoc()) {
                    echo "<option value='{$r['id_utente']}'>{$r['nome']} {$r['cognome']}</option>";
                }
                ?>
            </select>
            <select name="id_materia" required>
                <option value="">Materia</option>
                <?php
                $res = $conn->query("SELECT id_materia, nome_materia FROM materie");
                while ($r = $res->fetch_assoc()) echo "<option value='{$r['id_materia']}'>{$r['nome_materia']}</option>";
                ?>
            </select>
            <select name="id_classe" required>
                <option value="">Classe</option>
                <?php
                $res = $conn->query("SELECT id_classe, nome_classe FROM classi");
                while ($r = $res->fetch_assoc()) echo "<option value='{$r['id_classe']}'>{$r['nome_classe']}</option>";
                ?>
            </select>
            <button type="submit">Aggiungi Insegnamento</button>
        </form>
    </div>
</div>


</main>
</body>
</html>