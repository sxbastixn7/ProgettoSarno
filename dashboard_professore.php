<?php
session_start();
require_once 'config.php';

// Verifica autenticazione e tipo utente
if (!isset($_SESSION['id_utente']) || $_SESSION['tipo'] != 'prof') {
    header("Location: login.php");
    exit();
}

$id_professore = $_SESSION['id_utente'];
$messaggio = '';

// GESTIONE INSERIMENTO ORARIO
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aggiungi_orario'])) {
    $id_insegnamento = $_POST['id_insegnamento'] ?? null;
    $giorno = $_POST['giorno'] ?? null;
    $ora = $_POST['ora'] ?? null;

    if ($id_insegnamento && $giorno && $ora) {
        $stmt = $conn->prepare("SELECT id_classe, id_materia FROM insegnamenti WHERE id_insegnamento = ? AND id_professore = ?");
        $stmt->bind_param("ii", $id_insegnamento, $id_professore);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_classe = $row['id_classe'];
            $id_materia = $row['id_materia'];
            
            $insert = $conn->prepare("INSERT INTO orari (id_insegnamento, id_classe, id_professore, giorno, ora) 
                                     VALUES (?, ?, ?, ?, ?)");
            $insert->bind_param("iiisi", $id_insegnamento, $id_classe, $id_professore, $giorno, $ora);
            
            if ($insert->execute()) {
                $messaggio = "Orario aggiunto con successo!";
            } else {
                $messaggio = "Errore: " . $conn->error;
            }
        } else {
            $messaggio = "Insegnamento non valido!";
        }
    } else {
        $messaggio = "Tutti i campi sono obbligatori!";
    }
}

// GESTIONE INSERIMENTO VOTI
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aggiungi_voto'])) {
    $id_studente = $_POST['id_studente'] ?? null;
    $id_insegnamento = $_POST['id_insegnamento_voto'] ?? null;
    $voto = $_POST['voto'] ?? null;
    $note = $_POST['note'] ?? '';

    if ($id_studente && $id_insegnamento && $voto) {
        $stmt = $conn->prepare("SELECT i.id_materia 
                               FROM studenti_classi sc
                               JOIN insegnamenti i ON sc.id_classe = i.id_classe
                               WHERE sc.id_studente = ? AND i.id_insegnamento = ? AND i.id_professore = ?");
        $stmt->bind_param("iii", $id_studente, $id_insegnamento, $id_professore);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_materia = $row['id_materia'];
            
            $insert = $conn->prepare("INSERT INTO voti (id_studente, id_materia, id_professore, voto, data_voto, note) 
                                    VALUES (?, ?, ?, ?, CURDATE(), ?)");
            $insert->bind_param("iiids", $id_studente, $id_materia, $id_professore, $voto, $note);
            
            if ($insert->execute()) {
                $messaggio = "Voto inserito con successo!";
            } else {
                $messaggio = "Errore: " . $conn->error;
            }
        } else {
            $messaggio = "Non puoi inserire voti per questo studente!";
        }
    } else {
        $messaggio = "Compila tutti i campi obbligatori!";
    }
}

// RECUPERA DATI PER DASHBOARD
$insegnamenti = $conn->query("SELECT i.id_insegnamento, m.nome_materia, c.nome_classe 
                             FROM insegnamenti i
                             JOIN materie m ON i.id_materia = m.id_materia
                             JOIN classi c ON i.id_classe = c.id_classe
                             WHERE i.id_professore = $id_professore
                             ORDER BY c.nome_classe, m.nome_materia");

$orario_prof = $conn->query("SELECT o.giorno, o.ora, m.nome_materia, c.nome_classe 
                            FROM orari o
                            JOIN insegnamenti i ON o.id_insegnamento = i.id_insegnamento
                            JOIN materie m ON i.id_materia = m.id_materia
                            JOIN classi c ON i.id_classe = c.id_classe
                            WHERE o.id_professore = $id_professore
                            ORDER BY FIELD(o.giorno, 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì'), o.ora");

$studenti = $conn->query("SELECT u.id_utente, u.nome, u.cognome, c.nome_classe 
                         FROM utenti u
                         JOIN studenti_classi sc ON u.id_utente = sc.id_studente
                         JOIN classi c ON sc.id_classe = c.id_classe
                         JOIN insegnamenti i ON c.id_classe = i.id_classe
                         WHERE i.id_professore = $id_professore AND u.tipo = 'studente'
                         ORDER BY c.nome_classe, u.cognome, u.nome");

$classi_prof = $conn->query("SELECT DISTINCT c.id_classe, c.nome_classe 
                            FROM classi c
                            JOIN insegnamenti i ON c.id_classe = i.id_classe
                            WHERE i.id_professore = $id_professore
                            ORDER BY c.nome_classe");
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Professore</title>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f7fa;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        header {
            background-color: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h1 {
            color: var(--secondary-color);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s ease;
        }

        .card-header:hover {
            background-color: #2980b9;
        }

        .card-header.active {
            background-color: var(--secondary-color);
        }

        .card-header h2 {
            color: white;
            margin: 0;
            font-size: 1.2rem;
        }

        .toggle-icon {
            transition: transform 0.3s ease;
        }

        .card-header.active .toggle-icon {
            transform: rotate(180deg);
        }

        .card-content {
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transform: translateY(-10px);
            transition: 
                max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1),
                opacity 0.4s ease,
                transform 0.4s ease,
                padding 0.3s ease;
            padding: 0 20px;
        }

        .card-content.expanded {
            max-height: 5000px;
            opacity: 1;
            transform: translateY(0);
            padding: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-color);
        }

        select, input, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 16px;
            transition: var(--transition);
        }

        select:focus, input:focus, textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: var(--transition);
        }

        button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .alert {
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            font-weight: 500;
        }

        .success {
            background-color: #d4edda;
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .error {
            background-color: #f8d7da;
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }

        .logout-btn {
            background-color: var(--danger-color);
            padding: 10px 15px;
            text-decoration: none;
            color: white;
            border-radius: var(--border-radius);
            display: inline-block;
            transition: var(--transition);
        }

        .logout-btn:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        .class-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .class-item {
            background-color: #f8f9fa;
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .class-item:hover {
            background-color: #e9ecef;
            transform: translateY(-3px);
        }

        .class-item h3 {
            color: var(--secondary-color);
            margin-bottom: 10px;
        }

        .class-item ul {
            padding-left: 20px;
            margin-top: 8px;
        }

        .class-item li {
            margin-bottom: 5px;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            header {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
            
            .card-container {
                grid-template-columns: 1fr;
            }
            
            .class-grid {
                grid-template-columns: 1fr;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div>
                <h1>Dashboard Professore</h1>
                <p>Benvenuto, <?php echo htmlspecialchars($_SESSION['nome'] . ' ' . $_SESSION['cognome']); ?></p>
            </div>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>
        
        <?php if (!empty($messaggio)): ?>
            <div class="alert <?php echo strpos($messaggio, 'success') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($messaggio); ?>
            </div>
        <?php endif; ?>
        
        <div class="card-container">
            <!-- Card Gestione Orario -->
            <div class="card">
                <div class="card-header" onclick="toggleCard(this)">
                    <h2>Gestione Orario</h2>
                    <span class="toggle-icon">▼</span>
                </div>
                <div class="card-content">
                    <form method="post">
                        <div class="form-group">
                            <label>Insegnamento:
                                <select name="id_insegnamento" required>
                                    <?php while ($row = $insegnamenti->fetch_assoc()): ?>
                                        <option value="<?php echo $row['id_insegnamento']; ?>">
                                            <?php echo htmlspecialchars($row['nome_materia'] . ' - ' . $row['nome_classe']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label>Giorno:
                                <select name="giorno" required>
                                    <option value="Lunedì">Lunedì</option>
                                    <option value="Martedì">Martedì</option>
                                    <option value="Mercoledì">Mercoledì</option>
                                    <option value="Giovedì">Giovedì</option>
                                    <option value="Venerdì">Venerdì</option>
                                </select>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label>Ora (1-6):
                                <input type="number" name="ora" min="1" max="6" required>
                            </label>
                        </div>
                        
                        <button type="submit" name="aggiungi_orario">Aggiungi Orario</button>
                    </form>
                    
                    <h3>Il tuo orario</h3>
                    <table>
                        <tr>
                            <th>Giorno</th>
                            <th>Ora</th>
                            <th>Materia</th>
                            <th>Classe</th>
                        </tr>
                        <?php while ($row = $orario_prof->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['giorno']); ?></td>
                            <td><?php echo htmlspecialchars($row['ora']); ?></td>
                            <td><?php echo htmlspecialchars($row['nome_materia']); ?></td>
                            <td><?php echo htmlspecialchars($row['nome_classe']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>
            
            <!-- Card Inserimento Voti -->
            <div class="card">
                <div class="card-header" onclick="toggleCard(this)">
                    <h2>Inserimento Voti</h2>
                    <span class="toggle-icon">▼</span>
                </div>
                <div class="card-content">
                    <form method="post">
                        <div class="form-group">
                            <label>Studente:
                                <select name="id_studente" required>
                                    <?php while ($row = $studenti->fetch_assoc()): ?>
                                        <option value="<?php echo $row['id_utente']; ?>">
                                            <?php echo htmlspecialchars($row['cognome'] . ' ' . $row['nome'] . ' (' . $row['nome_classe'] . ')'); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label>Insegnamento:
                                <select name="id_insegnamento_voto" required>
                                    <?php $insegnamenti->data_seek(0); ?>
                                    <?php while ($row = $insegnamenti->fetch_assoc()): ?>
                                        <option value="<?php echo $row['id_insegnamento']; ?>">
                                            <?php echo htmlspecialchars($row['nome_materia'] . ' - ' . $row['nome_classe']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label>Voto (1-10):
                                <input type="number" name="voto" min="1" max="10" step="0.25" required>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label>Note:
                                <textarea name="note"></textarea>
                            </label>
                        </div>
                        
                        <button type="submit" name="aggiungi_voto">Inserisci Voto</button>
                    </form>
                </div>
            </div>
            
            <!-- Card Visualizzazione Classi -->
            <div class="card">
                <div class="card-header" onclick="toggleCard(this)">
                    <h2>Le tue Classi</h2>
                    <span class="toggle-icon">▼</span>
                </div>
                <div class="card-content">
                    <div class="class-grid">
                        <?php while ($classe = $classi_prof->fetch_assoc()): ?>
                            <div class="class-item">
                                <h3><?php echo htmlspecialchars($classe['nome_classe']); ?></h3>
                                <p>Materie insegnate:</p>
                                <ul>
                                    <?php
                                    $materie_classe = $conn->query("SELECT m.nome_materia 
                                                                   FROM materie m
                                                                   JOIN insegnamenti i ON m.id_materia = i.id_materia
                                                                   WHERE i.id_classe = {$classe['id_classe']} 
                                                                   AND i.id_professore = $id_professore");
                                    while ($materia = $materie_classe->fetch_assoc()): ?>
                                        <li><?php echo htmlspecialchars($materia['nome_materia']); ?></li>
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function toggleCard(header) {
        const card = header.parentElement;
        const content = card.querySelector('.card-content');
        const icon = card.querySelector('.toggle-icon');
        
        // Se la card è già aperta, la chiudo
        if (content.classList.contains('expanded')) {
            content.classList.remove('expanded');
            header.classList.remove('active');
            icon.textContent = '▼';
            return;
        }
        
        // Chiudo tutte le altre card aperte
        document.querySelectorAll('.card-content.expanded').forEach(openContent => {
            if (openContent !== content) {
                openContent.classList.remove('expanded');
                openContent.previousElementSibling.classList.remove('active');
                openContent.previousElementSibling.querySelector('.toggle-icon').textContent = '▼';
            }
        });
        
        // Apro la card cliccata
        content.classList.add('expanded');
        header.classList.add('active');
        icon.textContent = '▲';
        
        // Scroll smooth alla card aperta
        content.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Apri la prima card di default
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.card-header').click();
    });
    </script>
</body>
</html>