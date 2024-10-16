<?php
session_start();

require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'find_ennemies') {
    try {
        $user_id = $_POST['user_id'];

        // Préparez la requête pour trouver les autres joueurs dans la même file d'attente
        $stmt = $pdo->prepare("SELECT * FROM players WHERE `queue` = :status_queue AND `id` != :user_id");
        $stmt->execute(['status_queue' => "search", 'user_id' => $user_id]);
        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Compter le nombre de joueurs trouvés et en prendre un au hasard
        if (!empty($players)) {
            $player_find = $players[array_rand($players)];
            $status_queue = "find";
            // Update find game
            $stmt_update_searching_game = $pdo->prepare("UPDATE `players` SET `queue` = :status_queue WHERE `id` = :id");
            $stmt_update_searching_game->execute(['status_queue' => $status_queue, 'id' => $user_id]);

            // Update find game
            $stmt_update_searching_game = $pdo->prepare("UPDATE `players` SET `queue` = :status_queue WHERE `id` = :id");
            $stmt_update_searching_game->execute(['status_queue' => $status_queue, 'id' => $player_find['id']]);

            $stmt_create_game = $pdo->prepare("INSERT INTO games (player_1_id, player_2_id) VALUES (:player_1_id, :player_2_id)");
            $stmt_create_game->execute([
                'player_1_id' => $user_id,
                'player_2_id' => $player_find['id']
            ]);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => "Aucun joueur trouvé"]);
        }

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la récupération des joueurs : " . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Action non valide"]);
}
?>
