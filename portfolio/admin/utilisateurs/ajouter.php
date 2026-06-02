<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: ../connexion.php');
    exit();
}

require_once '../../config/connexion.php';
require_once '../../outils/fonctions.php';


$token = genererTokenCSRF();

$erreurs = [];
$succes = false;
$nom = '';
$prenom = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['csrf_token']) || !verifierTokenCSRF($_POST['csrf_token'])) {
        die("Erreur de sécurité : Jeton CSRF invalide.");
    }


    $nom = securiser($_POST['nom'] ?? '');
    $prenom = securiser($_POST['prenom'] ?? '');
    $email = securiser($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    
    if (empty($nom)) { $erreurs[] = "Le nom est obligatoire."; }
    if (empty($prenom)) { $erreurs[] = "Le prénom est obligatoire."; }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $erreurs[] = "Un email valide est obligatoire."; }
    if (strlen($mot_de_passe) < 6) { $erreurs[] = "Le mot de passe doit contenir au moins 6 caractères."; }

    
    if (empty($erreurs)) {
        try {
            $stmt_verif = $pdo->prepare("SELECT COUNT(*) FROM administrateurs WHERE email = :email");
            $stmt_verif->execute([':email' => $email]);
            if ($stmt_verif->fetchColumn() > 0) {
                $erreurs[] = "Cet email est déjà utilisé par un autre administrateur.";
            }
        } catch (PDOException $e) {
            $erreurs[] = "Erreur de vérification : " . $e->getMessage();
        }
    }

    
    if (empty($erreurs)) {
        try {
            $mdp_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO administrateurs (nom, prenom, email, mot_de_passe) VALUES (:nom, :prenom, :email, :mot_de_passe)");
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email,
                ':mot_de_passe' => $mdp_hache
            ]);

            $succes = true;
            
            $nom = ''; $prenom = ''; $email = '';
        } catch (PDOException $e) {
            $erreurs[] = "Erreur lors de l'ajout : " . $e->getMessage();
        }
    }
}

$titre_page = mettreEnMajuscule("Ajouter un Administrateur");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Administrateur - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px; }
        main { max-width: 600px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #28a745; padding-bottom: 10px; }
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 4px; }
        .err { background: #f8d7da; color: #721c24; }
        .suc { background: #d4edda; color: #155724; }
    </style>
</head>
<body>
    <main>
        <h2><?php echo $titre_page; ?></h2>
        <p><a href="index.php">← Retour à la liste</a></p>

        <?php if (!empty($erreurs)): ?>
            <div class="alert err"><ul><?php foreach ($erreurs as $erreur): ?><li><?php echo $erreur; ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <?php if ($succes): ?>
            <div class="alert suc">Le nouvel administrateur a été créé avec succès !</div>
        <?php endif; ?>

        <form method="post" action="ajouter.php">
            <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">

            <div style="margin-bottom: 15px;">
                <label>Prénom :</label>
                <input type="text" name="prenom" value="<?php echo htmlspecialchars($prenom); ?>" style="width: 100%; padding: 8px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Nom :</label>
                <input type="text" name="nom" value="<?php echo htmlspecialchars($nom); ?>" style="width: 100%; padding: 8px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Email :</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" style="width: 100%; padding: 8px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label>Mot de passe :</label>
                <input type="password" name="mot_de_passe" style="width: 100%; padding: 8px;">
            </div>
            <button type="submit" style="padding: 10px 20px; background-color: #28a745; color: white; border: none; cursor: pointer;">Créer le compte</button>
        </form>
    </main>
</body>
</html>