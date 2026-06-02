<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$chemin_config = '../../config/connexion.php';
$chemin_fonctions = '../../outils/fonctions.php';

if (file_exists($chemin_config)) {
    require_once $chemin_config;
} else {
    die("Fichier connexion.php introuvable à l'emplacement : " . realpath('../../config/connexion.php'));
}

if (file_exists($chemin_fonctions)) {
    require_once $chemin_fonctions;
}

$token = function_exists('genererTokenCSRF') ? genererTokenCSRF() : '';
$message_action = '';
$erreur_action = '';
$id_admin_actuel = $_SESSION['admin_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_supprimer'])) {
    $id_a_supprimer = intval($_POST['id_admin'] ?? 0);
    if ($id_a_supprimer === $id_admin_actuel) {
        $erreur_action = "Erreur : Vous ne pouvez pas supprimer votre propre compte connecté.";
    } elseif ($id_a_supprimer > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM administrateurs WHERE id = :id");
            $stmt->execute([':id' => $id_a_supprimer]);
            $message_action = "L'administrateur a été supprimé avec succès.";
        } catch (\PDOException $e) {
            $erreur_action = "Erreur lors de la suppression : " . $e->getMessage();
        }
    }
}

try {
    $stmt = $pdo->query("SELECT id, prenom, nom, email, date_creation FROM administrateurs ORDER BY id ASC");
    $liste_admins = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Erreur SQL lors de la récupération des administrateurs : " . $e->getMessage());
}

$titre_page = function_exists('mettreEnMajuscule') ? mettreEnMajuscule("Gestion des Administrateurs") : "GESTION DES ADMINISTRATEURS";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Administrateurs - Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-family: Arial, sans-serif; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f4f4f4; }
        .btn { padding: 6px 12px; text-decoration: none; border-radius: 4px; color: white; display: inline-block; cursor: pointer; border: none; font-family: Arial, sans-serif; font-size: 14px; }
        .btn-add { background-color: #28a745; margin-bottom: 20px; }
        .btn-delete { background-color: #dc3545; }
        .btn-disabled { background-color: #6c757d; cursor: not-allowed; opacity: 0.6; }
    </style>
</head>
<body>
    <main style="max-width: 1000px; margin: 40px auto; padding: 20px; font-family: Arial, sans-serif;">
        <h2><?php echo $titre_page; ?></h2>
        <p><a href="../dashboard.php">← Retour au Tableau de bord</a></p>

        <?php if (!empty($message_action)): ?>
            <div style="padding: 10px; background: #d4edda; color: #155724; margin-bottom: 20px; border-radius: 4px;">
                <?php echo htmlspecialchars($message_action); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($erreur_action)): ?>
            <div style="padding: 10px; background: #f8d7da; color: #721c24; margin-bottom: 20px; border-radius: 4px;">
                <?php echo htmlspecialchars($erreur_action); ?>
            </div>
        <?php endif; ?>

        <a href="ajouter.php" class="btn btn-add">+ Ajouter un administrateur</a>

        <table>
            <thead>
                <tr>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Date de création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($liste_admins)): ?>
                    <tr><td colspan="5" style="text-align: center;">Aucun administrateur trouvé.</td></tr>
                <?php else: ?>
                    <?php foreach ($liste_admins as $admin): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($admin['prenom'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($admin['nom'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($admin['email'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($admin['date_creation'] ?? ''); ?></td>
                            <td>
                                <?php if ($admin['id'] === $id_admin_actuel): ?>
                                    <button class="btn btn-disabled" title="Vous ne pouvez pas vous supprimer vous-même">Impossible</button>
                                <?php else: ?>
                                    <form method="post" action="index.php" onsubmit="return confirm('Voulez-vous vraiment supprimer cet administrateur ?');" style="margin: 0;">
                                        <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                                        <input type="hidden" name="id_admin" value="<?php echo $admin['id']; ?>">
                                        <button type="submit" name="action_supprimer" class="btn btn-delete">Supprimer</button>
                                    </form>
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