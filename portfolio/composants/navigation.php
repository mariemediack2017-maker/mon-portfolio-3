<?php
$page_courante = basename($_SERVER['PHP_SELF']);
?>
<header>
    <h1><?php echo $titre_page; ?></h1>
    <?php if (!empty($sous_titre)) : ?>
        <p><?php echo $sous_titre; ?></p>
    <?php endif; ?>

    <nav>
        <a href="index.php" class="<?php echo ($page_courante == 'index.php' ? 'actif' : ''); ?>">Accueil</a>
        <a href="projets.php" class="<?php echo ($page_courante == 'projets.php' ? 'actif' : ''); ?>">Projets</a>
        <a href="contact.php" class="<?php echo ($page_courante == 'contact.php' ? 'actif' : ''); ?>">Contact</a>
    </nav>
</header>