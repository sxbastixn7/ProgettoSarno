<?php
require_once 'config.php';

// Inizializza le variabili
$nome_materia = "";
$error = "";
$id_materia = null;

// Verifica se l'ID della materia è stato passato tramite GET
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $id_materia = trim($_GET["id"]);

    // Prepara la query per selezionare la materia da modificare
    $sql = "SELECT nome_materia FROM materie WHERE id_materia = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id_materia);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $nome_materia = $row["nome_materia"];
            } else {
                $error = "Materia non trovata.";
            }
        } else {
            $error = "Si è verificato un errore durante l'esecuzione della query.";
        }
        $stmt->close();
    } else {
        $error = "Si è verificato un errore nella preparazione della query.";
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Processa l'aggiornamento del form
    $id_materia = trim($_POST["id_materia"]);
    $nome_materia = trim($_POST["nome_materia"]);

    if (empty($nome_materia)) {
        $error = "Il nome della materia è obbligatorio.";
    } else {
        // Prepara la query per l'aggiornamento
        $sql = "UPDATE materie SET nome_materia = ? WHERE id_materia = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("si", $nome_materia, $id_materia);
            if ($stmt->execute()) {
                // Aggiornamento riuscito, reindirizza alla gestione
                header("Location: gestione_materie.php");
                exit();
            } else {
                $error = "Si è verificato un errore durante l'aggiornamento della materia.";
            }
            $stmt->close();
        } else {
            $error = "Si è verificato un errore nella preparazione della query.";
        }
    }
} else {
    // Se l'ID non è valido, reindirizza
    header("Location: dashboard_admin.php");
    exit();
}

// Chiudi la connessione
$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Materia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; margin: 20px; background-color: #f4f6f9; }
        h2 { color: #333; }
        form { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 400px; margin: 20px auto; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #2c3e50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #1a242f; }
        .error-message { color: red; margin-top: 10px; }
    </style>
</head>
<body>
    <h2>Modifica Materia</h2>
    <?php if (!empty($error)): ?>
        <p class="error-message"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="id_materia" value="<?php echo $id_materia; ?>">
        <div>
            <label for="nome_materia">Nome Materia:</label>
            <input type="text" id="nome_materia" name="nome_materia" value="<?php echo htmlspecialchars($nome_materia); ?>" required>
        </div>
        <button type="submit">Aggiorna Materia</button>
        <p><a href="dashboard_admin.php">Torna alla Dashboard</a></p>
    </form>
</body>
</html>
