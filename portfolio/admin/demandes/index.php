<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$dbname = 'portfolio';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PHP_SESSION_NONE
    ]);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if (isset($_GET['lire'])) {
    $id_demande = intval($_GET['lire']);
    if ($id_demande > 0) {
        try {
            $stmt_update = $pdo->prepare("UPDATE demandes_projet SET lu = 1 WHERE id = :id");
            $stmt_update->execute([':id' => $id_demande]);
        } catch (PDOException $e) {
            die("Erreur de mise à jour : " . $e->getMessage());
        }
    }
}

try {
    $stmt = $pdo->query("SELECT id, nom, email, type_projet, description, budget, lu, date_demande FROM demandes_projet ORDER BY date_demande DESC");
    $demandes = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demandes de Projet - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        main { max-width: 1000px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #17a2b8; padding-bottom: 10px; }
        .card { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 4px; background-color: #f8f9fa; }
        .non-lu { border-left: 5px solid #17a2b8; background-color: #fffdfd; font-weight: bold; }
        .badge { padding: 4px 8px; font-size: 12px; color: white; border-radius: 4px; display: inline-block; margin-bottom: 10px; }
        .badge-new { background-color: #17a2b8; }
        .badge-read { background-color: #6c757d; }
        .btn-action { padding: 6px 12px; background-color: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 13px; display: inline-block; margin-top: 10px; }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <main>
        <h2>DEMANDES DE PROJET</h2>
        <p><a href="../dashboard.php">← Retour au Tableau de bord</a></p>

        <?php if (empty($demandes)): ?>
            <p style="text-align: center; color: #666;">Aucune demande de projet reçue.</p>
        <?php else: ?>
            <?php foreach ($demandes as $dem): ?>
                <div class="card <?php echo $dem['lu'] == 0 ? 'non-lu' : ''; ?>">
                    <div>
                        <?php if ($dem['lu'] == 0): ?>
                            <span class="badge badge-new">Nouvelle demande</span>
                        <?php else: ?>
                            <span class="badge badge-read">Consultée</span>
                        <?php endif; ?>
                        <small style="float: right; color: #666;"><?php echo htmlspecialchars($dem['date_demande']); ?></small>
                    </div>
                    <strong>Client :</strong> <?php echo htmlspecialchars($dem['nom']); ?> (<?php echo htmlspecialchars($dem['email']); ?>)<br>
                    <strong>Type de projet demandé :</strong> <?php echo htmlspecialchars($dem['type_projet']); ?><br>
                    <strong>Budget estimé :</strong> <?php echo htmlspecialchars($dem['budget'] ?? 'Non spécifié'); ?><br><br>
                    <strong>Cahier des charges / Description :</strong><br>
                    <p style="background: #fff; padding: 10px; border: 1px solid #eee; border-radius: 4px; font-weight: normal;">
                        <?php echo nl2br(htmlspecialchars($dem['description'])); ?>
                    </p>
                    <?php if ($dem['lu'] == 0): ?>
                        <a href="index.php?lire=<?php echo $dem['id']; ?>" class="btn-action">Marquer comme consultée</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</body>
</html>