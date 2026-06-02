<?php
require_once '../config/connexion.php';
require_once '../outils/fonctions.php';

session_start();

if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: connexion.php');
    exit();
}

$erreurs = [];
$succes = false;
$titre = '';
$description = '';
$technologies = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = securiser($_POST['titre'] ?? '');
    $description = securiser($_POST['description'] ?? '');
    $technologies = securiser($_POST['technologies'] ?? '');

    if (empty($titre)) { $erreurs[] = "Le titre du projet est obligatoire."; }
    if (empty($description)) { $erreurs[] = "La description est obligatoire."; }

    if (empty($erreurs)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO projets (titre, description, technologies) VALUES (:titre, :description, :technologies)");
            $stmt->execute([
                ':titre' => $titre,
                ':description' => $description,
                ':technologies' => $technologies
            ]);
            $succes = true;
            $titre = '';
            $description = '';
            $technologies = '';
        } catch (\PDOException $e) {
            $erreurs[] = "Erreur lors de l'ajout du projet : " . $e->getMessage();
        }
    }
}

$titre_page = mettreEnMajuscule("Ajouter un Projet");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un projet - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <main style="max-width: 600px; margin: 40px auto; padding: 20px; font-family: Arial, sans-serif;">
        <h2><?php echo $titre_page; ?></h2>
        <p><a href="dashboard.php">← Retour au Tableau de bord</a></p>

        <?php if (!empty($erreurs)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
                <ul>
                    <?php foreach ($erreurs as $erreur): ?>
                        <li><?php echo $erreur; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($succes): ?>
            <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
                Le projet a été ajouté avec succès dans la base de données !
            </div>
        <?php endif; ?>

        <form method="post" action="ajouter-projet.php">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Titre du projet :</label>
                <input type="text" name="titre" value="<?php echo htmlspecialchars($titre); ?>" style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Description :</label>
                <textarea name="description" rows="6" style="width: 100%; padding: 8px; box-sizing: border-box;"><?php echo htmlspecialchars($description); ?></textarea>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Technologies (séparées par des virgules) :</label>
                <input type="text" name="technologies" placeholder="Ex: HTML, CSS, PHP, MySQL" value="<?php echo htmlspecialchars($technologies); ?>" style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>

            <button type="submit" style="padding: 10px 20px; background-color: #28a745; color: white; border: none; cursor: pointer; border-radius: 4px;">Enregistrer le projet</button>
        </form>
    </main>
</body>
</html>