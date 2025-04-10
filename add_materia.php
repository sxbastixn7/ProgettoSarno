<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_materia = trim($_POST["nome_materia"]);

    if (empty($nome_materia)) {
        $error = "Il nome della materia è obbligatorio.";
    } else {
        // Prepara la query per l'inserimento
        $sql = "INSERT INTO materie (nome_materia) VALUES (?)";

        if ($stmt = $conn->prepare($sql)) {
            // Binda i parametri alla query preparata
            $stmt->bind_param("s", $nome_materia);

            // Esegue la query
            if ($stmt->execute()) {
                // Inserimento riuscito, reindirizza alla dashboard
                header("Location: dashboard_admin.php");
                exit();
            } else {
                $error = "Si è verificato un errore durante l'inserimento della materia.";
            }

            // Chiudi lo statement
            $stmt->close();
        } else {
            $error = "Si è verificato un errore nella preparazione della query.";
        }
    }

    // Se c'è un errore, puoi mostrarlo o reindirizzare con un messaggio
    if (isset($error)) {
        // Potresti memorizzare l'errore in sessione e reindirizzare
        $_SESSION['error'] = $error;
        header("Location: dashboard_admin.php"); // Oppure una pagina dedicata agli errori
        exit();
    }

    // Chiudi la connessione
    $conn->close();
} else {
    // Se si tenta di accedere direttamente a questo file senza POST, reindirizza
    header("Location: dashboard_admin.php");
    exit();
}
?>