<?php
require_once 'config/connexion.php';
require_once 'outils/fonctions.php';

enregistrerVisite($pdo); 

$titre_page = mettreEnMajuscule("Mariama Diack"); 
$sous_titre = "Etudiante en 2eme année de licence <br> en ingénierie logiciel et administration réseau";
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Portfolio | Accueil</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php require 'composants/navigation.php'; ?>
    
    <main>
        <section class="presentation">
            <h2>A propos de moi</h2>
            <img src="images/moi.jpeg" alt="Ma photo">
            <div>
                Je suis étudiante en 2eme année de licence engénie logiciel et administration reseau.Passionnée par la
                developpement Web et la cybersécurité,
                Je cherche a créer des solution modernes, utiles et sécurisées. J'aime relever des défis techniques, et
                travailler en
                équipe pour résoudre des problémes.
                Mon objectif est d'aquérire de solide connaisance en informatique afin de me spécialiser en
                develoloppement web voici un
                aperçu de l de mes expériences et de mes compétences.N'hésitez pas à me contacter
            </div>
        </section>

        <section class="competences" style="display: flex; gap: 20px; flex-wrap: wrap;">
            <h2>Mes competences</h2>
            <div class="card" style="flex: 1;">
                <h3>Langages apprises :</h3>
                <p>HTML, CSS, Java, Linux, PHP (en cours)</p>
            </div>
            <div class="card" style="flex: 1;">
                <h3>Outils utilisés</h3>
                <p>VS Code, GitHub, XAMPP, Linux</p>
            </div>
            <div class="card" style="flex: 1;">
                <h3>Autres</h3>
                <p>Algorithmique, bases de données</p>
            </div>
        </section>
    </main>

    <?php require 'composants/pied-de-page.php'; ?>
</body>
</html>