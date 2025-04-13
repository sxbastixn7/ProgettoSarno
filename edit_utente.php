<?php
require 'config.php';
$id = $_GET["id"];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $nome = $_POST["nome"];
    $cognome = $_POST["cognome"];
    $tipo = $_POST["tipo"];
    $stmt = $conn->prepare("UPDATE utenti SET email=?, nome=?, cognome=?, tipo=? WHERE id_utente=?");
    $stmt->bind_param("ssssi", $email, $nome, $cognome, $tipo, $id);
    $stmt->execute();
    header("Location: dashboard_admin.php");
} else {
    $res = $conn->query("SELECT * FROM utenti WHERE id_utente=$id");
    $row = $res->fetch_assoc();
    echo "<form method='post'>
        Email: <input type='email' name='email' value='{$row['email']}' required><br>
        Nome: <input type='text' name='nome' value='{$row['nome']}' required><br>
        Cognome: <input type='text' name='cognome' value='{$row['cognome']}' required><br>
        Tipo: <select name='tipo' required>
            <option value='amministratore'" . ($row['tipo']=='amministratore'?" selected":"") . ">Amministratore</option>
            <option value='professore'" . ($row['tipo']=='professore'?" selected":"") . ">Professore</option>
            <option value='studente'" . ($row['tipo']=='studente'?" selected":"") . ">Studente</option>
        </select><br>
        <button type='submit'>Salva Modifiche</button>
    </form>";
}
?>