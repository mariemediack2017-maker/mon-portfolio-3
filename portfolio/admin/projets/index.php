<?php
require_once '../../config/connexion.php';
require_once '../../outils/fonctions.php';

session_start();

if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: ../connexion.php');
    exit();
}

$token = genererTokenCSRF();
$message_action = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_supprimer'])) {
    if (!verifierTokenCSRF($_POST['csrf_token'] ?? '')) {
        die("Attaque CSRF bloquée.");
    }
    
    $id_projet = intval($_POST['id_projet'] ?? 0);
    if ($id_projet > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM projets WHERE id = :id");
            $stmt->execute([':id' => $id_projet]);
            $message_action = "Le projet a été supprimé avec succès.";
        } catch (\PDOException $e) {
            $message_action = "Erreur lors de la suppression : " . $e->getMessage();
        }
    }
}

try {
    $stmt = $pdo->query("SELECT * FROM projets ORDER BY date_creation DESC");
    $liste_projets = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Erreur lors de la récupération des projets : " . $e->getMessage());
}

$titre_page = mettreEnMajuscule("Gestion des Projets");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des projets - Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-family: Arial, sans-serif; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f4f4f4; }
        .btn { padding: 6px 12px; text-decoration: none; border-radius: 4px; color: white; display: inline-block; cursor: pointer; border: none; font-family: Arial, sans-serif; font-size: 14px; }
        .btn-add { background-color: #28a745; margin-bottom: 20px; }
        .btn-edit { background-color: #ffc107; color: black; margin-right: 10px; font-weight: bold; }
        .btn-delete { background-color: #dc3545; }
    </style>
</head>
<body>
    <main style="padding: 20px;">
        <h2><?php echo $titre_page; ?></h2>
        <p><a href="../dashboard.php">← Retour au Tableau de bord</a></p>

        <?php if (!empty($message_action)): ?>
            <div style="padding: 10px; background: #e2e3e5; margin-bottom: 20px; border-radius: 4px;">
                <?php echo htmlspecialchars($message_action); ?>
            </div>
        <?php endif; ?>

        <a href="../ajouter-projet.php" class="btn btn-add">+ Ajouter un nouveau projet</a>

        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Technologies</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($liste_projets)): ?>
                    <tr><td colspan="3" style="text-align: center;">Aucun projet en base de données.</td></tr>
                <?php else: ?>
                    <?php foreach ($liste_projets as $proj): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($proj['titre'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($proj['technologies'] ?? ''); ?></td>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <a href="modifier.php?id=<?php echo $proj['id']; ?>" class="btn btn-edit">Modifier</a>
                                    
                                    <form method="post" action="index.php" onsubmit="return confirm('Voulez-vous vraiment supprimer ce projet ?');" style="margin: 0;">
                                        <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                                        <input type="hidden" name="id_projet" value="<?php echo $proj['id']; ?>">
                                        <button type="submit" name="action_supprimer" class="btn btn-delete">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>