<?php
require_once '../config/connexion.php';
require_once '../outils/fonctions.php';

session_start();

if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: connexion.php');
    exit();
}

if (isset($_GET['lire'])) {
    $id = (int)$_GET['lire'];
    $stmt = $pdo->prepare("UPDATE messages_contact SET lu = 1 WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: messages.php');
    exit();
}

try {
    $stmt = $pdo->query("SELECT * FROM messages_contact ORDER BY id DESC");
    $messages = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Erreur lors de la récupération des messages : " . $e->getMessage());
}

$titre_page = mettreEnMajuscule("Messages Reçus");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Messages - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-family: Arial, sans-serif; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .btn-retour { display: inline-block; margin-bottom: 20px; color: #007bff; text-decoration: none; }
        .btn-lu { color: #007bff; font-weight: bold; }
    </style>
</head>
<body>
    <main style="padding: 20px;">
        <h2><?php echo $titre_page; ?></h2>
        <a href="dashboard.php" class="btn-retour">← Retour au Tableau de bord</a>

        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($messages)): ?>
                    <tr><td colspan="5" style="text-align: center;">Aucun message reçu pour le moment.</td></tr>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($msg['nom'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($msg['prenom'] ?? ''); ?></td>
                            <td><a href="mailto:<?php echo htmlspecialchars($msg['email'] ?? ''); ?>"><?php echo htmlspecialchars($msg['email'] ?? ''); ?></a></td>
                            <td><?php echo nl2br(htmlspecialchars($msg['message'] ?? '')); ?></td>
                            <td>
                                <?php if ($msg['lu'] == 0): ?>
                                    <a href="?lire=<?php echo $msg['id']; ?>" class="btn-lu">Marquer comme lu</a>
                                <?php else: ?>
                                    ✅ Lu
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>