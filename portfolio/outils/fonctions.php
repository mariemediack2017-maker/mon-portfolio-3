<?php
function securiser($donnee) {
    return htmlspecialchars(trim($donnee), ENT_QUOTES, 'UTF-8');
}

function mettreEnMajuscule($texte) {
    return mb_strtoupper($texte, 'UTF-8');
}

function enregistrerVisite($pdo) {
    $ip = $_SERVER['REMOTE_ADDR'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    
    $page = $_SERVER['SCRIPT_NAME'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO visites (adresse_ip, page) VALUES (:adresse_ip, :page)");
        $stmt->execute([
            ':adresse_ip' => $ip,
            ':page' => $page
        ]);
    } catch (\PDOException $e) {
        error_log("Erreur journalisation visites : " . $e->getMessage());
    }
}

function genererTokenCSRF() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifierTokenCSRF($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}
?>