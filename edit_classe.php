<?php
require_once 'config.php';

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $res = $conn->query("SELECT * FROM classi WHERE id_classe = $id");
    $classe = $res->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"];
    $nome_classe = $_POST["nome_classe"];

    $stmt = $conn->prepare("UPDATE classi SET nome_classe=? WHERE id_classe=?");
    $stmt->bind_param("si", $nome_classe, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: dashboard_admin.php");
    exit;
}
?>

<form method="post">
    <input type="hidden" name="id" value="<?php echo $classe['id_classe']; ?>">
    <input type="text" name="nome_classe" value="<?php echo $classe['nome_classe']; ?>" required>
    <button type="submit">Salva</button>
</form>