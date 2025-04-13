<?php
session_start();
require_once "config.php";

if (!isset($_SESSION["id_utente"]) || $_SESSION["tipo"] !== "studente") {
    header("Location: login.php");
    exit;
}

$id_studente = $_SESSION["id_utente"];

// Ottieni la classe dello studente
$sqlClasse = "SELECT c.id_classe, c.nome_classe FROM studenti_classi sc
              JOIN classi c ON sc.id_classe = c.id_classe
              WHERE sc.id_studente = ?";
$stmtClasse = $conn->prepare($sqlClasse);
$stmtClasse->bind_param("i", $id_studente);
$stmtClasse->execute();
$resultClasse = $stmtClasse->get_result();
$classe = $resultClasse->fetch_assoc();

// Ottieni professori della classe
$sqlProfessori = "SELECT DISTINCT u.id_utente, u.nome, u.cognome
                  FROM insegnamenti i
                  JOIN utenti u ON i.id_professore = u.id_utente
                  WHERE i.id_classe = ?";
$stmtProfessori = $conn->prepare($sqlProfessori);
$stmtProfessori->bind_param("i", $classe['id_classe']);
$stmtProfessori->execute();
$resultProfessori = $stmtProfessori->get_result();
$professori = [];
while ($row = $resultProfessori->fetch_assoc()) {
    $professori[] = $row;
}

// Ottieni materie della classe
$sqlMaterie = "SELECT DISTINCT m.id_materia, m.nome_materia
               FROM insegnamenti i
               JOIN materie m ON i.id_materia = m.id_materia
               WHERE i.id_classe = ?";
$stmtMaterie = $conn->prepare($sqlMaterie);
$stmtMaterie->bind_param("i", $classe['id_classe']);
$stmtMaterie->execute();
$resultMaterie = $stmtMaterie->get_result();
$materie = [];
while ($row = $resultMaterie->fetch_assoc()) {
    $materie[] = $row;
}

// Filtro professore
$professore_selezionato = isset($_GET['professore']) ? (int) $_GET['professore'] : null;
$materia_selezionata = isset($_GET['materia']) ? (int) $_GET['materia'] : null;

// Query orario modificata
if (!empty($professore_selezionato)) {
    $sqlOrario = "SELECT o.*, m.nome_materia, u.nome AS nome_prof, u.cognome AS cognome_prof, c.nome_classe AS classe
                  FROM orari o
                  JOIN insegnamenti i ON o.id_insegnamento = i.id_insegnamento
                  JOIN materie m ON i.id_materia = m.id_materia
                  JOIN utenti u ON o.id_professore = u.id_utente
                  JOIN classi c ON c.id_classe = o.id_classe
                  WHERE o.id_professore = ?
                  ORDER BY FIELD(o.giorno, 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì'), o.ora";
    $stmtOrario = $conn->prepare($sqlOrario);
    $stmtOrario->bind_param("i", $professore_selezionato);
} else {
    $sqlOrario = "SELECT o.*, m.nome_materia, u.nome AS nome_prof, u.cognome AS cognome_prof, c.nome_classe AS classe
                  FROM orari o
                  JOIN insegnamenti i ON o.id_insegnamento = i.id_insegnamento
                  JOIN materie m ON i.id_materia = m.id_materia
                  JOIN utenti u ON o.id_professore = u.id_utente
                  JOIN classi c ON c.id_classe = o.id_classe
                  WHERE o.id_classe = ?
                  ORDER BY FIELD(o.giorno, 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì'), o.ora";
    $stmtOrario = $conn->prepare($sqlOrario);
    $stmtOrario->bind_param("i", $classe['id_classe']);
}
$stmtOrario->execute();
$resultOrario = $stmtOrario->get_result();

// Voti dello studente
$sqlVoti = "SELECT v.*, m.nome_materia, u.nome AS nome_prof, u.cognome AS cognome_prof
            FROM voti v
            JOIN materie m ON v.id_materia = m.id_materia
            JOIN utenti u ON v.id_professore = u.id_utente
            WHERE v.id_studente = ?";
if (!empty($materia_selezionata)) {
    $sqlVoti .= " AND v.id_materia = ?";
}
$sqlVoti .= " ORDER BY v.data_voto DESC";

$stmtVoti = $conn->prepare($sqlVoti);
if (!empty($materia_selezionata)) {
    $stmtVoti->bind_param("ii", $id_studente, $materia_selezionata);
} else {
    $stmtVoti->bind_param("i", $id_studente);
}
$stmtVoti->execute();
$resultVoti = $stmtVoti->get_result();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Studente</title>
    <link rel="stylesheet" href="stile.css">
</head>
<body>
<main>
<h1>Benvenuto, <?= htmlspecialchars($_SESSION["nome"]) ?>!</h1>
<a href="logout.php">LOGOUT</a>

<section class="card">
    <div class="card-header">Classe</div>
    <div class="card-content">
        <?= htmlspecialchars($classe["nome_classe"]) ?>
    </div>
</section>

<section class="card">
    <div class="card-header">Professori</div>
    <div class="card-content">
        <ul>
        <?php foreach ($professori as $prof): ?>
            <li><?= htmlspecialchars($prof['nome']) ?> <?= htmlspecialchars($prof['cognome']) ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
</section>

<section class="card">
    <div class="card-header">Orario delle lezioni</div>
    <div class="card-content">

        <form method="GET" style="margin-bottom: 1em;">
            <label for="professore">Filtra per professore:</label>
            <select name="professore" id="professore" onchange="this.form.submit()">
                <option value="">Tutti</option>
                <?php foreach ($professori as $prof): ?>
                    <option value="<?= $prof['id_utente'] ?>" <?= ($prof['id_utente'] == $professore_selezionato) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($prof['nome'] . ' ' . $prof['cognome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($materia_selezionata)): ?>
                <input type="hidden" name="materia" value="<?= $materia_selezionata ?>">
            <?php endif; ?>
        </form>

        <?php if ($resultOrario->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Giorno</th>
                        <th>Ora</th>
                        <th>Classe</th>
                        <th>Professore</th>
                        <th>Materia</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $resultOrario->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['giorno']) ?></td>
                        <td><?= htmlspecialchars($row['ora']) ?></td>
                        <td><?= htmlspecialchars($row['classe']) ?></td>
                        <td><?= htmlspecialchars($row['nome_prof'] . ' ' . $row['cognome_prof']) ?></td>
                        <td><?= htmlspecialchars($row['nome_materia']) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nessuna lezione trovata.</p>
        <?php endif; ?>
    </div>
</section>

<section class="card">
    <div class="card-header">I tuoi voti</div>
    <div class="card-content">

        <form method="GET" style="margin-bottom: 1em;">
            <label for="materia">Filtra per materia:</label>
            <select name="materia" id="materia" onchange="this.form.submit()">
                <option value="">Tutte le materie</option>
                <?php foreach ($materie as $mat): ?>
                    <option value="<?= $mat['id_materia'] ?>" <?= ($mat['id_materia'] == $materia_selezionata) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($mat['nome_materia']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($professore_selezionato)): ?>
                <input type="hidden" name="professore" value="<?= $professore_selezionato ?>">
            <?php endif; ?>
        </form>

        <?php if ($resultVoti->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Materia</th>
                        <th>Professore</th>
                        <th>Voto</th>
                        <th>Data</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($voto = $resultVoti->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($voto['nome_materia']) ?></td>
                        <td><?= htmlspecialchars($voto['nome_prof'] . ' ' . $voto['cognome_prof']) ?></td>
                        <td><?= $voto['voto'] ?></td>
                        <td><?= date("d/m/Y", strtotime($voto['data_voto'])) ?></td>
                        <td><?= htmlspecialchars($voto['note']) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nessun voto disponibile.</p>
        <?php endif; ?>
    </div>
</section>

</main>
</body>
</html>
