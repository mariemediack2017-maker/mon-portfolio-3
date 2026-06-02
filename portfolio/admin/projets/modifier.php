<?php
require_once '../../config/connexion.php';
require_once '../../outils/fonctions.php';

session_start();

if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: ../connexion.php');
    exit();
}

$erreurs = [];
$succes = false;
$id_projet = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_projet <= 0) {
    header('Location: index.php');
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM projets WHERE id = :id");
    $stmt->execute([':id' => $id_projet]);
    $projet = $stmt->fetch();
    
    if (!$projet) {
        header('Location: index.php');
        exit();
    }
} catch (\PDOException $e) {
    die("Erreur lors de la récupération du projet : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = securiser($_POST['titre'] ?? '');
    $description = securiser($_POST['description'] ?? '');
    $technologies = securiser($_POST['technologies'] ?? '');

    if (empty($titre)) { $erreurs[] = "Le titre du projet est obligatoire."; }
    if (empty($description)) { $erreurs[] = "La description est obligatoire."; }

    if (empty($erreurs)) {
        try {
            $stmt = $pdo->prepare("UPDATE projets SET titre = :titre, description = :description, technologies = :technologies WHERE id = :id");
            $stmt->execute([
                ':titre' => $titre,
                ':description' => $description,
                ':technologies' => $technologies,
                ':id' => $id_projet
            ]);
            $succes = true;
            
            $stmt = $pdo->prepare("SELECT * FROM projets WHERE id = :id");
            $stmt->execute([':id' => $id_projet]);
            $projet = $stmt->fetch();
        } catch (\PDOException $e) {
            $erreurs[] = "Erreur lors de la modification : " . $e->getMessage();
        }
    }
}

$titre_page = mettreEnMajuscule("Modifier le Projet");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un projet - Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <main style="max-width: 600px; margin: 40px auto; padding: 20px; font-family: Arial, sans-serif;">
        <h2><?php echo $titre_page; ?></h2>
        <p><a href="index.php">← Retour à la liste des projets</a></p>

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
                Le projet a été modifié avec succès !
            </div>
        <?php endif; ?>

        <form method="post" action="modifier.php?id=<?php echo $id_projet; ?>">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Titre du projet :</label>
                <input type="text" name="titre" value="<?php echo htmlspecialchars($projet['titre'] ?? ''); ?>" style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Description :</label>
                <textarea name="description" rows="6" style="width: 100%; padding: 8px; box-sizing: border-box;"><?php echo htmlspecialchars($projet['description'] ?? ''); ?></textarea>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Technologies :</label>
                <input type="text" name="technologies" value="<?php echo htmlspecialchars($projet['technologies'] ?? ''); ?>" style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>

            <button type="submit" style="padding: 10px 20px; background-color: #ffc107; color: black; border: none; cursor: pointer; border-radius: 4px; font-weight: bold;">Enregistrer les modifications</button>
        </form>
    </main>
</body>
</html>