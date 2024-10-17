<?php
session_start();

require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'refresh') {
    try {
        $user_id = $_SESSION['user_id'];

        $stmt_packs_shop = $pdo->prepare("SELECT * FROM packs_shop");
        $stmt_packs_shop->execute();
        $packs_shop = $stmt_packs_shop->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'packs_shop' => $packs_shop]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la récupération des joueurs : " . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Action non valide"]);
}
?>