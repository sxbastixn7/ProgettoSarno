<?php
require_once 'config.php';

// Verifica se l'ID della materia è stato passato tramite GET
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $id_materia = trim($_GET["id"]);

    // Prepara la query per l'eliminazione
    $sql = "DELETE FROM materie WHERE id_materia = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Binda i parametri alla query preparata
        $stmt->bind_param("i", $id_materia);

        // Esegue la query
        if ($stmt->execute()) {
            // Eliminazione riuscita, reindirizza alla dashboard con un messaggio
            $_SESSION['success'] = "Materia eliminata con successo.";
            header("Location: dashboard_admin.php");
            exit();
        } else {
            // Errore durante l'eliminazione
            $_SESSION['error'] = "Si è verificato un errore durante l'eliminazione della materia.";
            header("Location: dashboard_admin.php");
            exit();
        }

        // Chiudi lo statement
        $stmt->close();
    } else {
        // Errore nella preparazione della query
        $_SESSION['error'] = "Si è verificato un errore nella preparazione della query di eliminazione.";
        header("Location: dashboard_admin.php");
        exit();
    }

    // Chiudi la connessione
    $conn->close();
} else {
    // Se l'ID non è valido, reindirizza con un messaggio di errore
    $_SESSION['error'] = "ID materia non valido.";
    header("Location: dashboard_admin.php");
    exit();
}
?>