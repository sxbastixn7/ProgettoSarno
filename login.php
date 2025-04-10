<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!empty($email) && !empty($password)) {
        $sql = "SELECT id_utente, email, password, nome, cognome, tipo FROM utenti WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();

                if ($password === $row['password']) { // In produzione: password_verify
                    $_SESSION["id_utente"] = $row["id_utente"];
                    $_SESSION["nome"] = $row["nome"];
                    $_SESSION["cognome"] = $row["cognome"];
                    $_SESSION["tipo"] = $row["tipo"];

                    switch ($row["tipo"]) {
                        case 'amministratore':
                            header("Location: dashboard_admin.php");
                            break;
                        case 'prof':
                            header("Location: dashboard_professore.php");
                            break;
                        case 'studente':
                            header("Location: dashboard_studente.php");
                            break;
                    }
                    exit;
                } else {
                    $errore = "Password errata.";
                }
            } else {
                $errore = "Utente non trovato.";
            }
        } else {
            $errore = "Errore nella query.";
        }
        $stmt->close();
    } else {
        $errore = "Inserisci email e password.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Accesso | Portale</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 40px;
            background-color: white;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .login-wrapper h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
            font-size: 24px;
            font-weight: 600;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #333;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            background-color: #fff;
        }

        input:focus {
            border-color: #5b78c7;
            outline: none;
            box-shadow: 0 0 0 2px rgba(91, 120, 199, 0.2);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #2c3e50;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #1f2d3d;
        }

        .error-message {
            background: #fdecea;
            color: #c0392b;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 480px) {
            .login-wrapper {
                padding: 30px 20px;
                border-radius: 0;
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <h2>Accesso al registro elettronico</h2>
        <?php if (isset($errore)) echo "<div class='error-message'>$errore</div>"; ?>
        <form method="post" action="">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Accedi</button>
        </form>
    </div>
</body>
</html>
