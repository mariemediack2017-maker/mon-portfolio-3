<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/connexion.php';
require_once '../outils/fonctions.php';

// 1. Sécurité : Vérifier si l'admin est connecté
if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: connexion.php');
    exit();
}

try {
    // 2. Récupération des données pour le Dashboard
    $stmt_projets = $pdo->query("SELECT COUNT(*) AS total FROM projets");
    $total_projets = $stmt_projets->fetch()['total'];

    // Compteur des messages non lus
    $stmt_messages = $pdo->query("SELECT COUNT(*) AS total FROM messages_contact WHERE lu = 0");
    $messages_non_lus = $stmt_messages->fetch()['total'];

    // Compteur des demandes non lues
    $stmt_demandes_count = $pdo->query("SELECT COUNT(*) AS total FROM demandes_projet WHERE lu = 0");
    $demandes_non_lues = $stmt_demandes_count->fetch()['total'];

    // Dernières visites (5 dernières)
    $stmt_visites = $pdo->query("SELECT * FROM visites ORDER BY id DESC LIMIT 5");
    $dernieres_visites = $stmt_visites->fetchAll();

    // Dernières demandes de projet (5 dernières)
    $stmt_demandes = $pdo->query("SELECT * FROM demandes_projet ORDER BY id DESC LIMIT 5");
    $dernieres_demandes = $stmt_demandes->fetchAll();

} catch (\PDOException $e) {
    die("Une erreur est survenue lors du chargement des données du dashboard.");
}

$titre_page = function_exists('mettreEnMajuscule') ? mettreEnMajuscule("Tableau de bord") : "TABLEAU DE BORD";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .menu-admin a { font-weight: bold; text-decoration: none; margin-right: 15px; padding: 8px 12px; border-radius: 4px; color: white; display: inline-block; }
        .btn-projets { background-color: #28a745; }
        .btn-messages { background-color: #007bff; }
        .btn-demandes { background-color: #17a2b8; }
        .btn-admins { background-color: #6f42c1; }
        .btn-deco { background-color: #dc3545; }
        .stats-container { display: flex; gap: 20px; margin-bottom: 30px; }
        .card { border: 1px solid #ccc; padding: 20px; flex: 1; background: #f9f9f9; border-radius: 5px; }
    </style>
</head>
<body>
    <main style="padding: 20px; font-family: Arial, sans-serif;">
        <h2><?php echo htmlspecialchars($titre_page); ?></h2>
        
        <p>Bienvenue, <?php echo htmlspecialchars($_SESSION['admin_prenom'] ?? 'Administrateur'); ?> !</p>
        
        <div class="menu-admin" style="margin-bottom: 30px;">
            <a href="projets/index.php" class="btn-projets">📁 Gérer les projets</a>
            <a href="messages.php" class="btn-messages">✉️ Voir les messages</a>
            <a href="demandes.php" class="btn-demandes">💼 Voir les demandes</a>
            <a href="utilisateurs/index.php" class="btn-admins">👥 Gérer les admins</a>
            <a href="deconnexion.php" class="btn-deco">🚪 Déconnexion</a>
        </div>

        <div class="stats-container">
            <div class="card">
                <h3>Projets</h3>
                <p style="font-size: 24px; font-weight: bold;"><?php echo $total_projets; ?></p>
            </div>
            <div class="card">
                <h3>Nouveaux Messages</h3>
                <p style="font-size: 24px; font-weight: bold; color: #dc3545;"><?php echo $messages_non_lus; ?></p>
            </div>
            <div class="card">
                <h3>Nouvelles Demandes</h3>
                <p style="font-size: 24px; font-weight: bold; color: #17a2b8;"><?php echo $demandes_non_lues; ?></p>
            </div>
        </div>

        <h3>📊 5 Dernières Visites</h3>
        <table>
            <thead>
                <tr><th>Adresse IP</th><th>Page visitée</th></tr>
            </thead>
            <tbody>
                <?php if (empty($dernieres_visites)): ?>
                    <tr><td colspan="2">Aucune visite enregistrée.</td></tr>
                <?php else: ?>
                    <?php foreach ($dernieres_visites as $v): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($v['adresse_ip']); ?></td>
                            <td><?php echo htmlspecialchars($v['page']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <h3>💼 5 Dernières Demandes de Projet</h3>
        <table>
            <thead>
                <tr><th>Type de projet</th><th>Description</th></tr>
            </thead>
            <tbody>
                <?php if (empty($dernieres_demandes)): ?>
                    <tr><td colspan="2">Aucune demande reçue.</td></tr>
                <?php else: ?>
                    <?php foreach ($dernieres_demandes as $d): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($d['type_projet']); ?></td>
                            <td><?php echo htmlspecialchars($d['description']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>