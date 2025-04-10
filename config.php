<?php
$host = "localhost";
$user = "root";  // Cambia se usi un altro utente MySQL
$pass = "sarno123";      // Metti la tua password MySQL
$dbname = "registro";

$conn = new mysqli($host, $user, $pass, $dbname);

// Controllo connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}
?>
