<?php
require_once 'config/connexion.php';
require_once 'outils/fonctions.php';
enregistrerVisite($pdo);

$erreurs = [];
$succes_contact = false;
$nom = '';
$prenom= '';
$email = '';
$message = '';

$erreurs_projet = [];
$demande_valide = false;
$recap = ['type' => '', 'description' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['envoyer_contact'])) {
    $nom = securiser($_POST['nom']??'');
    $prenom = securiser($_POST['prenom']??'');
    $email = securiser($_POST['email']??'');
    $message = securiser($_POST['message']??'');
    
    if (empty($nom)) { $erreurs[] = "Le nom est requis."; }
    if (empty($prenom)) { $erreurs[] = "Le prénom est requis."; }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $erreurs[] = "Un email valide est requis."; }
    if (empty($message)) { $erreurs[] = "Le message ne peut pas être vide."; }
    
    if (empty($erreurs)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages_contact (nom, prenom, email, message) VALUES (:nom, :prenom, :email, :message)");
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email,
                ':message' => $message
            ]);
            $succes_contact = true;
        } catch (\PDOException $e) {
            $erreurs[] = "Erreur lors de l'enregistrement du message.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['envoyer_demande'])) {
    $recap=[
        'type' => securiser($_POST['type_projet']??''),
        'description' => securiser($_POST['description_projet']??'')
    ];
    
    if(empty($recap['type'])){ $erreurs_projet['type'] = "Veuillez préciser le type de projet."; }
    if(empty($recap['description'])){ $erreurs_projet['description'] = " La description est obligatoire."; }
    
    if (empty($erreurs_projet)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO demandes_projet (nom, email, type_projet, description) VALUES (:nom, :email, :type_projet, :description)");
            $stmt->execute([
                ':nom' => 'Anonyme ou Visiteur',
                ':email' => 'non_renseigne@exemple.com',
                ':type_projet' => $recap['type'],
                ':description' => $recap['description']
            ]);
            $demande_valide = true;
        } catch (\PDOException $e) {
            $erreurs_projet['description'] = "Erreur lors de l'enregistrement de votre demande.";
        }
    }
}

$titre_page = mettreEnMajuscule("Contact");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php require 'composants/navigation.php'; ?>
    <main>
        <section>
            <h2>Contactez-moi</h2>
            <?php if (!empty($erreurs)): ?>
                <ul>
                    <?php foreach ($erreurs as $erreur): ?>
                        <li><?php echo $erreur; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if ($succes_contact): ?>
                <p>Merci <?php echo $prenom . " " . $nom; ?>, votre message a bien été reçu !</p>
            <?php endif; ?>
            <form method="post" action="contact.php">
                <input type="text" name="nom" placeholder="Nom" value="<?php echo $nom; ?>">
                <input type="text" name="prenom" placeholder="Prénom" value="<?php echo $prenom; ?>">
                <input type="email" name="email" placeholder="Email" value="<?php echo $email; ?>">
                <textarea name="message" rows="4" placeholder="Votre message"><?php echo $message; ?></textarea>
                <button type="submit" name="envoyer_contact">Envoyer</button>
            </form>
        </section>
        <section>
            <h2>Demande de projet</h2>
            <form method="post" action="contact.php">
                <input type="text" name="type_projet" placeholder="Type de projet" value="<?php echo $recap['type']; ?>">
                <?php if (isset($erreurs_projet['type'])) echo "<p>".$erreurs_projet['type']."</p>";?>
                <textarea name="description_projet" rows="4" placeholder="Description du projet"><?php echo $recap['description']; ?></textarea>
                <?php if (isset($erreurs_projet['description'])) echo "<p>".$erreurs_projet['description']."</p>";?>
                <button type="submit" name="envoyer_demande">Envoyer la demande</button>
            </form>
            <?php if ($demande_valide): ?>
                <div>
                    <h3>Récapitulatif de votre demande :</h3>
                    <p><strong>Type :</strong> <?php echo $recap['type']; ?></p>
                    <p><strong>Description :</strong> <?php echo $recap['description']; ?></p>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <?php require 'composants/pied-de-page.php'; ?>
</body>
</html>