<?php
session_start();

require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'refresh') {
    try {
        $user_id = $_POST['user_id'];

        $stmt = $pdo->prepare("SELECT * FROM players WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
        $player_info = $stmt->fetch(PDO::FETCH_ASSOC);

        if($player_info['queue'] == "find"){
            // Préparez la requête pour trouver les autres joueurs dans la même file d'attente
            $stmt = $pdo->prepare("SELECT * FROM games WHERE (`player_1_id` = :player_1_id OR `player_2_id` = :player_2_id) AND `status` = :status_game");
            $stmt->execute(['player_1_id' => $user_id, 'player_2_id' => $user_id, 'status_game' => "in_progress"]);
            $game = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if($game) {
                $gameId = $game['id'];
                echo json_encode(['success' => true, 'player_info' => $player_info, 'game' => $game]);
            } else {
                echo json_encode(['success' => true, 'player_info' => $player_info]);
            }
        } else {
            echo json_encode(['success' => true, 'player_info' => $player_info]);
        }


    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la récupération des joueurs : " . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Action non valide"]);
}
?>