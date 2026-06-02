<?php
require_once '../config/connexion.php';
require_once '../outils/fonctions.php';

session_start();

if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: connexion.php');
    exit();
}

// Logique pour marquer une demande comme lue
if (isset($_GET['lire'])) {
    $id = (int)$_GET['lire'];
    $stmt = $pdo->prepare("UPDATE demandes_projet SET lu = 1 WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: demandes.php');
    exit();
}

try {
    $stmt = $pdo->query("SELECT * FROM demandes_projet ORDER BY id DESC");
    $demandes = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Erreur lors de la récupération des demandes : " . $e->getMessage());
}

$titre_page = mettreEnMajuscule("Demandes de Projet");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Demandes - Admin</title>
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
                    <th>Email</th>
                    <th>Type de projet</th>
                    <th>Description</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($demandes)): ?>
                    <tr><td colspan="5" style="text-align: center;">Aucune demande de projet enregistrée.</td></tr>
                <?php else: ?>
                    <?php foreach ($demandes as $dem): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($dem['nom'] ?? 'Anonyme'); ?></td>
                            <td><a href="mailto:<?php echo htmlspecialchars($dem['email'] ?? ''); ?>"><?php echo htmlspecialchars($dem['email'] ?? 'Non renseigné'); ?></a></td>
                            <td><?php echo htmlspecialchars($dem['type_projet'] ?? 'Non spécifié'); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($dem['description'] ?? '')); ?></td>
                            <td>
                                <?php if ($dem['lu'] == 0): ?>
                                    <a href="?lire=<?php echo $dem['id']; ?>" class="btn-lu">Marquer comme lu</a>
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