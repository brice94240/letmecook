<?php
session_start();

require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'buy_pack') {
    try {
        $user_id = $_POST['user_id'];
        $pack_id = $_POST['packId'];

        // Préparez la requête pour trouver les autres joueurs dans la même file d'attente
        $stmt_pack_info = $pdo->prepare("SELECT * FROM packs_shop WHERE `id` = :id");
        $stmt_pack_info->execute(['id' => $pack_id]);
        $pack_info = $stmt_pack_info->fetch(PDO::FETCH_ASSOC);
        if($pack_info){
            $nb_cards_buy = $pack_info['nb_card'];
            $prix_pack = $pack_info['price'];
            // Préparez la requête pour trouver les autres joueurs dans la même file d'attente
            $stmt_player_info = $pdo->prepare("SELECT * FROM players WHERE `id` = :id");
            $stmt_player_info->execute(['id' => $user_id]);
            $player_info = $stmt_player_info->fetch(PDO::FETCH_ASSOC);
    
            if($player_info){
                if(intval($player_info['gold']) >= intval($prix_pack)){
                    //Select toutes les cartes que les joueurs n'a pas
                    $stmt_cards_non_possedes = $pdo->prepare("SELECT c.* FROM cards c LEFT JOIN cards_players cp ON c.id = cp.card_id AND cp.player_id = :user_id WHERE cp.card_id IS NULL");
                    $stmt_cards_non_possedes->execute(['user_id' => $user_id]);
                    $cards_non_possedes = $stmt_cards_non_possedes->fetchAll(PDO::FETCH_ASSOC);
                    if ($cards_non_possedes) {
                        if ($nb_cards_buy <= count($cards_non_possedes)) {
                            // Mettre à jour l'or
                            $new_gold = intval($player_info['gold']) - intval($prix_pack);
                            $stmt_update_gold_player = $pdo->prepare("UPDATE `players` SET `gold` = :gold WHERE `id` = :id");
                            $stmt_update_gold_player->execute(['gold' => $new_gold, 'id' => $user_id]);
                        
                            // Prendre x nombre de cartes aléatoires, x étant le nb_cards_buy
                            $cards_to_add = [];
                        
                            for ($i = 0; $i < $nb_cards_buy; $i++) {
                                // Prendre un index aléatoire
                                $random_index = array_rand($cards_non_possedes);
                                $random_card = $cards_non_possedes[$random_index];
                        
                                // Ajouter la carte sélectionnée à la liste
                                $cards_to_add[] = $random_card['id'];
                                
                                // Éviter de sélectionner la même carte plusieurs fois
                                unset($cards_non_possedes[$random_index]);
                            }
                        
                            // Récupérer toutes les nouvelles cartes
                            $placeholders = rtrim(str_repeat('?,', count($cards_to_add)), ',');
                            $stmt_new_cards = $pdo->prepare("SELECT * FROM cards WHERE id IN ($placeholders)");
                            $stmt_new_cards->execute($cards_to_add);
                            $new_cards = $stmt_new_cards->fetchAll(PDO::FETCH_ASSOC); // Utiliser fetchAll pour obtenir toutes les nouvelles cartes
                        
                            // Insérer ces cartes dans cards_players
                            $stmt_insert_card = $pdo->prepare("INSERT INTO cards_players (card_id, player_id) VALUES (:card_id, :player_id)");
                        
                            foreach ($new_cards as $card) {
                                $stmt_insert_card->execute(['card_id' => $card['id'], 'player_id' => $user_id]);
                            } 

                            // Répondre avec succès
                            echo json_encode(['success' => true, 'new_cards' => $new_cards, 'cards_to_add' => $cards_to_add, 'message' => "Pack acheté avec succèes."]);
                        } else {
                            echo json_encode(['success' => true, 'message' => "Pas assez de carte non possédée."]);
                        }
                    } else {
                        echo json_encode(['success' => true, 'message' => "Aucune carte non possédée."]);
                    }                    
                } else {
                    echo json_encode(['success' => false, 'message' => "Pas assez de gold."]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => "Joueur non trouvé."]);
            }
        }  else {
            echo json_encode(['success' => false, 'message' => "Pack non trouvé."]);
        }



    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la récupération des joueurs : " . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Action non valide"]);
}
?>
