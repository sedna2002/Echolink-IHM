<?php

// config.php
$host = '127.0.0.1';
$db   = 'echolink';
$user = 'root';
$pass = 'rootroot'; // mettre le mot de passe réel
$charset = 'utf8mb4';

// DSN pour PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Options PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Erreur connexion BDD : " . $e->getMessage());
}

// Requête pour récupérer les connexions les plus récentes (dernier 100)
$stmt = null;
$connections = null;

try {
    $stmt = $pdo->query("
        SELECT 
            B.date_connexion,
            A.indicatif as indicatifA,
            B.indicatif as indicatifB,
            A.application,
            A.plateforme,
            A.appareil,
            A.os,
            A.version,
            B.idEcholink AS idEcholink
        FROM id B
        LEFT JOIN connexions A ON B.indicatif = A.indicatif AND B.id = A.fk_idID

        ORDER BY date_connexion DESC
        LIMIT 100
    ");
    $connections = $stmt->fetchAll();
} catch (PDOException $e) {
    //die("Erreur connexion BDD : " . $e->getMessage());
    $connections = null;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des connexions EchoLink</title>
    <link rel="stylesheet" href="index.css">
    <link rel="shortcut icon" href="Images/LogoEcholink.png" type="image/png">
</head>
<body>

    <div class="header">
        <h1>Liste des connexions [<?php echo count($connections); ?>] EchoLink</h1>
        <img src="./Images/LogoEcholink.png" alt="Logo EchoLink">
    </div>

    <table>
        <thead>
            <tr>
                <th>Date connexion</th>
                <th>ID</th>
                <th>Indicatif</th>
                <th>Application</th>
                <th>Plateforme</th>
                <th>Appareil</th>
                <th>OS</th>
                <th>Version</th>
            </tr>
        </thead>
        <tbody>
        <?php 
            if ($connections != null) {
                foreach ($connections as $c):
                    echo "<tr>";
                    echo "<td>".htmlspecialchars($c['date_connexion'])."</td>";
                    echo "<td>".htmlspecialchars($c['idEcholink'])."</td>";
                    echo "<td>".htmlspecialchars($c['indicatifA'] ?? $c['indicatifB'])."</td>";
                    echo "<td>".htmlspecialchars($c['application'])."</td>";
                    echo "<td>".htmlspecialchars($c['plateforme'])."</td>";
                    echo "<td>".htmlspecialchars($c['appareil'])."</td>";
                    echo "<td>".htmlspecialchars($c['os'])."</td>";
                    echo "<td>".htmlspecialchars($c['version'])."</td>";
                    echo "</tr>";
                endforeach;
            }
        ?>
        </tbody>
    </table>
</body>
</html>
