<?php
session_start();

require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'refresh') {
    try {
        $user_id = $_POST['user_id'];
        // Préparer la requête pour sélectionner les decks et joindre les cartes
        $sql = "
            SELECT 
                d.deck_id AS deck_id, d.name, d.player_id,
                c1.name AS card_1_name, c1.avatar AS card_1_avatar, c1.type AS card_1_type,
                c2.name AS card_2_name, c2.avatar AS card_2_avatar, c2.type AS card_2_type,
                c3.name AS card_3_name, c3.avatar AS card_3_avatar, c3.type AS card_3_type,
                c4.name AS card_4_name, c4.avatar AS card_4_avatar, c4.type AS card_4_type
            FROM decks d
            LEFT JOIN cards c1 ON d.card_1_id = c1.id
            LEFT JOIN cards c2 ON d.card_2_id = c2.id
            LEFT JOIN cards c3 ON d.card_3_id = c3.id
            LEFT JOIN cards c4 ON d.card_4_id = c4.id
            WHERE d.player_id = :user_id
        ";

        // Préparer et exécuter la requête
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Récupérer les résultats
        $decks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Préparer la requête pour sélectionner les decks et joindre les cartes
        $sql = "
            SELECT 
                d.deck_id AS deck_id, d.name, d.player_id,
                c1.name AS card_1_name, c1.avatar AS card_1_avatar, c1.type AS card_1_type,
                c2.name AS card_2_name, c2.avatar AS card_2_avatar, c2.type AS card_2_type,
                c3.name AS card_3_name, c3.avatar AS card_3_avatar, c3.type AS card_3_type,
                c4.name AS card_4_name, c4.avatar AS card_4_avatar, c4.type AS card_4_type,
                p.deck AS player_deck
            FROM players p
            LEFT JOIN decks d ON p.deck = d.deck_id
            LEFT JOIN cards c1 ON d.card_1_id = c1.id
            LEFT JOIN cards c2 ON d.card_2_id = c2.id
            LEFT JOIN cards c3 ON d.card_3_id = c3.id
            LEFT JOIN cards c4 ON d.card_4_id = c4.id
            WHERE p.id = :user_id
            AND d.player_id = p.id
        ";

        // Préparer et exécuter la requête
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Récupérer les résultats
        $deck = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'decks' => $decks, 'deck' => $deck]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la récupération des joueurs : " . $e->getMessage()]);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    try {
        // Récupérer les données envoyées via POST
        $user_id = $_POST['user_id'];
        $data_ids = $_POST['dataIds'];
        $title_deck = $_POST['TitleDeck'];
    
        // Vérifier que title_deck n'est pas vide et que data_ids est un tableau
        if (!empty($title_deck) && is_array($data_ids) && count($data_ids) === 4) {
            // Vérifier l'unicité des data_ids
            if (count($data_ids) === count(array_unique($data_ids))) {
                // Sélectionner le deck_id maximum pour le joueur
                $stmt = $pdo->prepare("SELECT MAX(deck_id) AS max_deck_id FROM decks WHERE player_id = :player_id");
                $stmt->bindParam(':player_id', $user_id);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
                // Calculer le nouveau deck_id
                $new_deck_id = $result['max_deck_id'] ? $result['max_deck_id'] + 1 : 1;
    
                // Préparer la requête d'insertion
                $stmt = $pdo->prepare("
                    INSERT INTO decks (deck_id, name, player_id, card_1_id, card_2_id, card_3_id, card_4_id) 
                    VALUES (:deck_id, :name, :player_id, :card_1_id, :card_2_id, :card_3_id, :card_4_id)
                ");
    
                // Lier les paramètres
                $stmt->bindParam(':deck_id', $new_deck_id);
                $stmt->bindParam(':name', $title_deck);
                $stmt->bindParam(':player_id', $user_id);
                $stmt->bindParam(':card_1_id', $data_ids[0]);
                $stmt->bindParam(':card_2_id', $data_ids[1]);
                $stmt->bindParam(':card_3_id', $data_ids[2]);
                $stmt->bindParam(':card_4_id', $data_ids[3]);
    
                // Exécuter la requête
                $stmt->execute();
    
                //Update le deck du joueur avec le nouveau deck
                $stmt_update_new_deck = $pdo->prepare("UPDATE `players` SET `deck` = :new_deck WHERE `id` = :id");
                $stmt_update_new_deck->execute(['new_deck' => $new_deck_id, 'id' => $user_id]);

                // Retourner une réponse JSON indiquant le succès
                echo json_encode(['success' => true, 'deck_id' => $new_deck_id]);
            } else {
                // Si les data_ids ne sont pas uniques
                echo json_encode(['success' => false, 'message' => 'Les data_ids doivent être uniques.']);
            }
        } else {
            // Si title_deck est vide ou si data_ids n'a pas 4 éléments
            echo json_encode(['success' => false, 'message' => 'title_deck doit être non vide et data_ids doit contenir exactement 4 éléments.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la création du deck : " . $e->getMessage()]);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        // Récupérer les données envoyées via POST
        $user_id = $_POST['user_id'];
        $data_id = $_POST['dataId'];

        // Vérifier si le deck à supprimer n'est pas celui utilisé par le joueur
        // Ici, récupérez l'ID du deck utilisé par le joueur
        $stmt = $pdo->prepare("SELECT deck FROM players WHERE id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $usedDeckId = $stmt->fetchColumn();

        if ($usedDeckId != $data_id) {
    
            if(isset($user_id) && isset($data_id)){
                //Verifie si ce n'est pas le deck utilisé par le joueur (joueurs.deck)

                // Préparer la requête DELETE
                $stmt = $pdo->prepare("DELETE FROM decks WHERE deck_id = :data_id AND player_id = :user_id");

                // Lier les paramètres
                $stmt->bindParam(':data_id', $data_id, PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

                // Exécuter la requête
                $stmt->execute();

                // Vérifier si une ligne a été supprimée
                if ($stmt->rowCount() > 0) {
                    // Retourner une réponse JSON indiquant le succès
                    echo json_encode(['success' => true, 'message' => 'Deck supprimé avec succès']);
                } else {
                    // Aucun deck trouvé avec ces critères
                    echo json_encode(['success' => false, 'message' => 'Aucun deck trouvé pour cet utilisateur']);
                }
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Vous ne pouvez pas supprimer le deck utilisé']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la création du deck : " . $e->getMessage()]);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'select') {
    try {
        // Récupérer les données envoyées via POST
        $user_id = $_POST['user_id'];
        $selectedDeckId = $_POST['selectedDeckId'];

        if (isset($user_id) && isset($selectedDeckId)) {
            // Préparer la requête UPDATE pour mettre à jour le deck du joueur
            $stmt = $pdo->prepare("
                UPDATE players 
                SET deck = :selectedDeckId 
                WHERE id = :user_id
            ");
    
            // Exécuter la requête avec les paramètres
            $stmt->execute(['selectedDeckId' => $selectedDeckId, 'user_id' => $user_id]);

            echo json_encode(['success' => true, 'message' => "Deck mis à jour avec succès."]);
        } else {
            echo json_encode(['success' => false, 'message' => "Identifiant utilisateur ou deck sélectionné manquant."]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la séléction du deck : " . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Action non valide"]);
}
?>
