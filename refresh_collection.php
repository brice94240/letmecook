<?php
session_start();

require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'refresh') {
    try {
        $user_id = $_POST['user_id'];
        $search = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : '';
        $type = isset($_POST['selectedType']) ? $_POST['selectedType'] : '';

        // Construire la requête
        $sql = "
            SELECT cards.*
            FROM cards_players
            INNER JOIN cards ON cards_players.card_id = cards.id
            WHERE cards_players.player_id = :user_id
        ";

        if ($search) {
            $sql .= " AND cards.name LIKE :search";
        }

        if ($type) {
            $sql .= " AND cards.type = :type";
        }

        $stmt_collection = $pdo->prepare($sql);
        $stmt_collection->bindValue(':user_id', $user_id);

        if ($search) {
            $stmt_collection->bindValue(':search', '%' . $search . '%');
        }

        if ($type) {
            $stmt_collection->bindValue(':type', $type);
        }
        
        // Exécuter la requête avec tous les paramètres
        $stmt_collection->execute(['user_id' => $user_id] + ($search ? ['search' => '%' . $search . '%'] : []) + ($type ? ['type' => $type] : []));
        
        $collection_player = $stmt_collection->fetchAll(PDO::FETCH_ASSOC); // Récupérer toutes les cartes du joueur

        echo json_encode(['success' => true, 'collection_player' => $collection_player, 'type' => $type, 'search' => $search]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la récupération des joueurs : " . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Action non valide"]);
}
?>
