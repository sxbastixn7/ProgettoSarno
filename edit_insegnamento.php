<?php
require_once 'config.php';

if (!isset($_GET["id"])) {
    header("Location: dashboard_admin.php");
    exit;
}

$id = $_GET["id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_professore = $_POST["id_professore"];
    $id_materia = $_POST["id_materia"];
    $id_classe = $_POST["id_classe"];

    $stmt = $conn->prepare("UPDATE insegnamenti SET id_professore=?, id_materia=?, id_classe=? WHERE id_insegnamento=?");
    $stmt->bind_param("iiii", $id_professore, $id_materia, $id_classe, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: dashboard_admin.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM insegnamenti WHERE id_insegnamento = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();
?>

<form method="post">
    <select name="id_professore" required>
        <option value="">Professore</option>
        <?php
        $res = $conn->query("SELECT id_utente, nome FROM utenti WHERE tipo='professore'");
        while ($r = $res->fetch_assoc()) {
            $selected = $r['id_utente'] == $data['id_professore'] ? "selected" : "";
            echo "<option value='{$r['id_utente']}' $selected>{$r['nome']}</option>";
        }
        ?>
    </select>
    <select name="id_materia" required>
        <option value="">Materia</option>
        <?php
        $res = $conn->query("SELECT id_materia, nome_materia FROM materie");
        while ($r = $res->fetch_assoc()) {
            $selected = $r['id_materia'] == $data['id_materia'] ? "selected" : "";
            echo "<option value='{$r['id_materia']}' $selected>{$r['nome_materia']}</option>";
        }
        ?>
    </select>
    <select name="id_classe" required>
        <option value="">Classe</option>
        <?php
        $res = $conn->query("SELECT id_classe, nome_classe FROM classi");
        while ($r = $res->fetch_assoc()) {
            $selected = $r['id_classe'] == $data['id_classe'] ? "selected" : "";
            echo "<option value='{$r['id_classe']}' $selected>{$r['nome_classe']}</option>";
        }
        ?>
    </select>
    <button type="submit">Aggiorna</button>
</form>
