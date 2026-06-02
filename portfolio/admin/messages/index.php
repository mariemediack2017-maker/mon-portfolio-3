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
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if (isset($_GET['lire'])) {
    $id_msg = intval($_GET['lire']);
    if ($id_msg > 0) {
        try {
            $stmt_update = $pdo->prepare("UPDATE messages_contact SET lu = 1 WHERE id = :id");
            $stmt_update->execute([':id' => $id_msg]);
        } catch (PDOException $e) {
            die("Erreur de mise à jour : " . $e->getMessage());
        }
    }
}

try {
    $stmt = $pdo->query("SELECT id, nom, email, message, lu, date_envoi FROM messages_contact ORDER BY date_envoi DESC");
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Messages de Contact - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        main { max-width: 1000px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .message-card { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 4px; background-color: #f8f9fa; }
        .non-lu { border-left: 5px solid #dc3545; background-color: #fffdfd; font-weight: bold; }
        .badge { padding: 4px 8px; font-size: 12px; color: white; border-radius: 4px; display: inline-block; margin-bottom: 10px; }
        .badge-new { background-color: #dc3545; }
        .badge-read { background-color: #6c757d; }
        .btn-action { padding: 6px 12px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; font-size: 13px; display: inline-block; margin-top: 10px; }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <main>
        <h2>MESSAGES DE CONTACT</h2>
        <p><a href="../dashboard.php">← Retour au Tableau de bord</a></p>

        <?php if (empty($messages)): ?>
            <p style="text-align: center; color: #666;">Aucun message reçu pour le moment.</p>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <div class="message-card <?php echo $msg['lu'] == 0 ? 'non-lu' : ''; ?>">
                    <div>
                        <?php if ($msg['lu'] == 0): ?>
                            <span class="badge badge-new">Nouveau</span>
                        <?php else: ?>
                            <span class="badge badge-read">Lu</span>
                        <?php endif; ?>
                        <small style="float: right; color: #666;"><?php echo htmlspecialchars($msg['date_envoi']); ?></small>
                    </div>
                    <strong>De :</strong> <?php echo htmlspecialchars($msg['nom']); ?> (<?php echo htmlspecialchars($msg['email']); ?>)<br><br>
                    <strong>Message :</strong><br>
                    <p style="background: #fff; padding: 10px; border: 1px solid #eee; border-radius: 4px; font-weight: normal;">
                        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                    </p>
                    <?php if ($msg['lu'] == 0): ?>
                        <a href="index.php?lire=<?php echo $msg['id']; ?>" class="btn-action">Marquer comme lu</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</body>
</html>