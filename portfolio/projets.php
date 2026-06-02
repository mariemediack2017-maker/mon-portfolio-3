
<?php
require_once 'config/connexion.php';
require_once 'outils/fonctions.php';
enregistrerVisite($pdo);

$mot_cle = isset($_GET['recherche']) ? securiser($_GET['recherche']) : '';
$resultats = [];

if ($mot_cle !== '') {
    $stmt = $pdo->prepare("SELECT * FROM projets WHERE titre LIKE :mot_cle OR description LIKE :mot_cle");
    $stmt->execute([':mot_cle' => '%' . $mot_cle . '%']);
    $resultats = $stmt->fetchAll();
} else {
    $stmt = $pdo->query("SELECT * FROM projets ORDER BY date_creation DESC");
    $resultats = $stmt->fetchAll();
}
$titre_page = mettreEnMajuscule("Mes Projets");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titre_page; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php require 'composants/navigation.php'; ?>
    <main>
        <section style="text-align: center; padding: 20px;">
            <h2>Rechercher un projet</h2>
            <form method="get" action="projets.php">
                <input type="text" name="recherche" placeholder="Rechercher..." value="<?php echo htmlspecialchars($mot_cle); ?>">
                <button type="submit">Rechercher</button>
            </form>
        </section>
        <section class="projets">
            <h2>Mes réalisations</h2>
            <?php if (empty($resultats)) : ?>
                <p>Aucun projet trouvé pour "<?php echo htmlspecialchars($mot_cle); ?>"</p>
            <?php else : ?>
                <?php foreach ($resultats as $projet) : ?>
                    <div>
                        <h2><?php echo htmlspecialchars($projet['titre']); ?></h2>
                        <p><?php echo nl2br(htmlspecialchars($projet['description'])); ?></p>
                        <div class="technologie">
                            <?php 
                            if (!empty($projet['technologies'])) {
                                $techs = explode(',', $projet['technologies']);
                                foreach ($techs as $tech) {
                                    if (trim($tech) !== '') {
                                        echo '<span class="badge">'.htmlspecialchars(trim($tech)).'</span>';
                                    }
                                }
                            }
                            ?>
                        </div>
                        <?php 
                        if (!empty($projet['image'])) :
                            $imgs = explode(',', $projet['image']);
                            foreach ($imgs as $img) : 
                                if (trim($img) !== '') :
                        ?>
                            <img src="images/<?php echo htmlspecialchars(trim($img)); ?>" class="img-projet" alt="Aperçu">
                        <?php 
                                endif;
                            endforeach; 
                        endif;
                        ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
        <section class="experience">
            <h2>Expériences</h2>
            <div class="experience-box">
                Je n’ai pas encore eu d’expérience professionnelle ou associative,
                mais j’aimerais m’impliquer dans le secteur du developpement web
                pour développer des compétences en organisation, travail d’équipe,
                communication, informatique… Je suis vraiment motivée pour apprendre et contribuer.
            </div>
        </section>
    </main>
    <?php require 'composants/pied-de-page.php'; ?>
</body>
</html>