<?php
session_start();
require_once '../../config/connexion.php';


if (!isset($_SESSION['admin_connecte'])) {
    header('Location: ../connexion.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Erreur CSRF : Tentative non autorisée.");
    }

    if (isset($_POST['id'])) {
        $stmt = $pdo->prepare("DELETE FROM projets WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        header('Location: index.php?msg=supprime');
        exit();
    }
}
?>