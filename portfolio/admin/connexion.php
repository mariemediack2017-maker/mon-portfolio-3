<?php
require_once '../config/connexion.php';
require_once '../outils/fonctions.php';

session_start();

if (isset($_SESSION['admin_connecte']) && $_SESSION['admin_connecte'] === true) {
    header('Location: dashboard.php');
    exit();
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = securiser($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (empty($email) || empty($mot_de_passe)) {
        $erreur = "Identifiants incorrects.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $admin = $stmt->fetch();
if ($admin && ($mot_de_passe === $admin['mot_de_passe'] || password_verify($mot_de_passe, $admin['mot_de_passe']))) {
                session_regenerate_id(true);
                $_SESSION['admin_connecte'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_prenom'] = $admin['prenom'];
                header('Location: dashboard.php');
                exit();
            } else {
                $erreur = "Identifiants incorrects.";
            }
        } catch (\PDOException $e) {
            $erreur = "Une erreur est survenue lors de la connexion.";
        }
    }
}

$titre_page = mettreEnMajuscule("Connexion");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Administration</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <main style="max-width: 400px; margin: 40px auto; padding: 20px;">
        <h2>Connexion Admin</h2>
        
        <?php if (!empty($erreur)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($erreur); ?></p>
        <?php endif; ?>

        <form method="post" action="connexion.php">
            <input type="email" name="email" placeholder="Email" required style="width: 100%; margin-bottom: 15px; padding: 8px;">
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required style="width: 100%; margin-bottom: 15px; padding: 8px;">
            <button type="submit" style="width: 100%; padding: 10px; background-color: #333; color: #fff; border: none; cursor: pointer;">Se connecter</button>
        </form>
    </main>
</body>
</html>