<?php
session_start();

require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'search') {
    try {
        $user_id = $_SESSION['user_id'];

        $stmt = $pdo->prepare("SELECT * FROM players WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row){
            if($row['queue'] == null){
                $status_queue = "search";
            } else if($row['queue'] == "search"){
                $status_queue = null;
            }
            // Update searching game
            $stmt_update_searching_game = $pdo->prepare("UPDATE `players` SET `queue` = :status_queue WHERE `id` = :id");
            $stmt_update_searching_game->execute(['status_queue' => $status_queue, 'id' => $user_id]);
        }
        echo json_encode(['success' => true, 'status_queue' => $status_queue]);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la récupération des joueurs : " . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Action non valide"]);
}
?>