<?php
session_start();

require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'refresh') {
    try {
        $user_id = $_SESSION['user_id'];
        $game_id = $_POST['game_id'];
        $game = false;

        // Récupérer les informations du jeu
        $stmt_game = $pdo->prepare("SELECT * FROM games WHERE id = :game_id AND (player_1_id = :user_id OR player_2_id = :user_id) AND status = :status_game");
        $stmt_game->execute(['game_id' => $game_id, 'user_id' => $user_id, 'status_game' => 'in_progress']);
        $game_info = $stmt_game->fetch(PDO::FETCH_ASSOC);

        if ($game_info) {
            $player_1 = $game_info['player_1_id'];
            $player_2 = $game_info['player_2_id'];
            $game = true;

            // Récupérer les informations des joueurs
            $stmt_players = $pdo->prepare("SELECT * FROM players WHERE id IN (:player_1, :player_2)");
            $stmt_players->execute(['player_1' => $player_1, 'player_2' => $player_2]);
            $players_info = $stmt_players->fetchAll(PDO::FETCH_ASSOC);

            // Initialiser les tableaux pour les decks et les cartes
            $deck_info_player = [];
            $deck_info_ennemy = [];
            $cards_info_player = [];
            $cards_info_ennemy = [];
            if($game_info['turn'] == 0){
                if ($players_info) {
                    foreach ($players_info as $player_info) {
                        $stmt_count_cards_game = $pdo->prepare("SELECT * FROM cards_game WHERE game_id = :game_id");
                        $stmt_count_cards_game->execute(['game_id' => $game_id]);
                        $cards = $stmt_count_cards_game->fetchAll(PDO::FETCH_ASSOC);
                        if (count($cards) < 8) {
                            if ($player_info['id'] == $user_id) {
                                $deck_player = $player_info['deck'];
                                $player_id = $player_info['id'];

                                // Récupérer les informations du deck du joueur
                                $stmt_deck_player = $pdo->prepare("SELECT id, player_id, card_1_id, card_2_id, card_3_id, card_4_id FROM decks WHERE deck_id = :deck_id AND player_id = :player_id");
                                $stmt_deck_player->execute(['deck_id' => $deck_player, 'player_id' => $player_id]);
                                $deck_info_player = $stmt_deck_player->fetch(PDO::FETCH_ASSOC);

                                // Récupérer les cartes du joueur si le deck a été trouvé
                                if ($deck_info_player) {
                                    $unique_id_deck_player = $deck_info_player['id'];
                                    $card_ids = [
                                        $deck_info_player['card_1_id'],
                                        $deck_info_player['card_2_id'],
                                        $deck_info_player['card_3_id'],
                                        $deck_info_player['card_4_id']
                                    ];

                                    // Construire dynamiquement la requête avec FIELD pour préserver l'ordre
                                    $placeholders = rtrim(str_repeat('?,', count($card_ids)), ',');
                                    $sql = "SELECT * FROM cards WHERE id IN ($placeholders) ORDER BY FIELD(id, " . implode(',', $card_ids) . ")";
                                    $stmt_cards = $pdo->prepare($sql);

                                    // Exécuter la requête avec les bons paramètres
                                    $stmt_cards->execute($card_ids);

                                    // Récupérer les informations des cartes
                                    $cards_info_player = $stmt_cards->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($cards_info_player as &$card_info_player) {
                                        $card_info_player['player_id'] = $deck_info_player['player_id']; // Ajoute l'ID du joueur
                                        $card_info_player['deck_id'] = $unique_id_deck_player; // Ajoute l'ID du deck
                                    }
                                }
                            } else {
                                // C'est l'ennemi
                                $deck_ennemy = $player_info['deck'];
                                $ennemy_player_id = $player_info['id'];

                                // Récupérer les informations du deck de l'ennemi
                                $stmt_deck_ennemy = $pdo->prepare("SELECT id, player_id, card_1_id, card_2_id, card_3_id, card_4_id FROM decks WHERE deck_id = :deck_id AND player_id = :player_id");
                                $stmt_deck_ennemy->execute(['deck_id' => $deck_ennemy, 'player_id' => $ennemy_player_id]);
                                $deck_info_ennemy = $stmt_deck_ennemy->fetch(PDO::FETCH_ASSOC);

                                // Récupérer les cartes de l'ennemi si le deck a été trouvé
                                if ($deck_info_ennemy) {
                                    $unique_id_deck_ennemy = $deck_info_ennemy['id'];
                                    $card_ids_ennemy = [
                                        $deck_info_ennemy['card_1_id'],
                                        $deck_info_ennemy['card_2_id'],
                                        $deck_info_ennemy['card_3_id'],
                                        $deck_info_ennemy['card_4_id']
                                    ];
                                    $placeholders = rtrim(str_repeat('?,', count($card_ids_ennemy)), ',');
                                    $sql = "SELECT * FROM cards WHERE id IN ($placeholders) ORDER BY FIELD(id, " . implode(',', $card_ids_ennemy) . ")";
                                    $stmt_cards = $pdo->prepare($sql);
                                    $stmt_cards->execute($card_ids_ennemy);
                                    // Récupérer les informations des cartes
                                    $cards_info_ennemy = $stmt_cards->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($cards_info_ennemy as &$card_info_ennemy) {
                                        $card_info_ennemy['player_id'] = $deck_info_ennemy['player_id']; // Ajoute l'ID du joueur
                                        $card_info_ennemy['deck_id'] = $unique_id_deck_ennemy; // Ajoute l'ID du deck
                                    }
                                }
                            }
                        }
                    }
                    // Insertion des cartes du joueur
                    if ($cards_info_player && count($cards_info_player) == 4) {
                        //Compter le nombre de carte de la game dans cards_game
                        //Si le nombre de carte est plus petit que 8
                        foreach ($cards_info_player as $card) {
                            $stmt_insert = $pdo->prepare("INSERT INTO cards_game (game_id, player_id, card_id, deck_id, skill_1_cooldown, skill_2_cooldown, skill_1_crit, skill_2_crit, skill_1_crit_dmg, skill_2_crit_dmg, skill_1_precision, skill_2_precision, hp_left, hp_max, avatar, name, type, skill_1_name, skill_2_name, skill_1_desc, skill_2_desc, skill_1_type, skill_2_type, skill_1_effect, skill_2_effect, skill_1_base_value, skill_2_base_value, skill_1_targets, skill_2_targets, skill_1_target_type, skill_2_target_type, price, rarity) VALUES (:game_id, :player_id, :card_id, :deck_id, :skill_1_cooldown, :skill_2_cooldown, :skill_1_crit, :skill_2_crit, :skill_1_crit_dmg, :skill_2_crit_dmg, :skill_1_precision, :skill_2_precision, :hp_left, :hp_max, :avatar, :name, :type, :skill_1_name, :skill_2_name, :skill_1_desc, :skill_2_desc, :skill_1_type, :skill_2_type, :skill_1_effect, :skill_2_effect, :skill_1_base_value, :skill_2_base_value, :skill_1_targets, :skill_2_targets, :skill_1_target_type, :skill_2_target_type, :price, :rarity)");
                            $stmt_insert->execute([
                                'game_id' => $game_id,
                                'player_id' => $card['player_id'],
                                'card_id' => $card['id'],
                                'deck_id' => $card['deck_id'], // Ajout du deck_id
                                'skill_1_cooldown' => $card['skill_1_cooldown'],
                                'skill_2_cooldown' => $card['skill_2_cooldown'],
                                'skill_1_crit' => $card['skill_1_crit'],
                                'skill_2_crit' => $card['skill_2_crit'],
                                'skill_1_crit_dmg' => $card['skill_1_crit_dmg'],
                                'skill_2_crit_dmg' => $card['skill_2_crit_dmg'],
                                'skill_1_precision' => $card['skill_1_precision'],
                                'skill_2_precision' => $card['skill_2_precision'],
                                'hp_left' => $card['hp'],
                                'hp_max' => $card['hp'],
                                'avatar' => $card['avatar'],
                                'name' => $card['name'],
                                'type' => $card['type'],
                                'skill_1_name' => $card['skill_1_name'],
                                'skill_2_name' => $card['skill_2_name'],
                                'skill_1_desc' => $card['skill_1_desc'],
                                'skill_2_desc' => $card['skill_2_desc'],
                                'skill_1_type' => $card['skill_1_type'],
                                'skill_2_type' => $card['skill_2_type'],
                                'skill_1_effect' => $card['skill_1_effect'],
                                'skill_2_effect' => $card['skill_2_effect'],
                                'skill_1_base_value' => $card['skill_1_base_value'],
                                'skill_2_base_value' => $card['skill_2_base_value'],
                                'skill_1_targets' => $card['skill_1_targets'],
                                'skill_2_targets' => $card['skill_2_targets'],
                                'skill_1_target_type' => $card['skill_1_target_type'],
                                'skill_2_target_type' => $card['skill_2_target_type'],
                                'price' => $card['price'],
                                'rarity' => $card['rarity']
                            ]);
                        }
                        foreach ($players_info as $player) {
                            $stmt_insert = $pdo->prepare("INSERT INTO players_game (game_id, player_id) VALUES (:game_id, :player_id)");
                            $stmt_insert->execute([
                                'game_id' => $game_id,
                                'player_id' => $player['id'],
                            ]);
                        }
                    }

                    // Insertion des cartes de l'ennemi
                    if ($cards_info_ennemy && count($cards_info_ennemy) == 4) {
                        foreach ($cards_info_ennemy as $card_ennemy) {
                            $stmt_insert = $pdo->prepare("INSERT INTO cards_game (game_id, player_id, card_id, deck_id, skill_1_cooldown, skill_2_cooldown, skill_1_crit, skill_2_crit, skill_1_crit_dmg, skill_2_crit_dmg, skill_1_precision, skill_2_precision, hp_left, hp_max, avatar, name, type, skill_1_name, skill_2_name, skill_1_desc, skill_2_desc, skill_1_type, skill_2_type, skill_1_effect, skill_2_effect, skill_1_base_value, skill_2_base_value, skill_1_targets, skill_2_targets, skill_1_target_type, skill_2_target_type, price, rarity) VALUES (:game_id, :player_id, :card_id, :deck_id, :skill_1_cooldown, :skill_2_cooldown, :skill_1_crit, :skill_2_crit, :skill_1_crit_dmg, :skill_2_crit_dmg, :skill_1_precision, :skill_2_precision, :hp_left, :hp_max, :avatar, :name, :type, :skill_1_name, :skill_2_name, :skill_1_desc, :skill_2_desc, :skill_1_type, :skill_2_type, :skill_1_effect, :skill_2_effect, :skill_1_base_value, :skill_2_base_value, :skill_1_targets, :skill_2_targets, :skill_1_target_type, :skill_2_target_type, :price, :rarity)");
                            $stmt_insert->execute([
                                'game_id' => $game_id,
                                'player_id' => $card_ennemy['player_id'],
                                'card_id' => $card_ennemy['id'],
                                'deck_id' => $card_ennemy['deck_id'], // Ajout du deck_id
                                'skill_1_cooldown' => $card_ennemy['skill_1_cooldown'],
                                'skill_2_cooldown' => $card_ennemy['skill_2_cooldown'],
                                'skill_1_crit' => $card_ennemy['skill_1_crit'],
                                'skill_2_crit' => $card_ennemy['skill_2_crit'],
                                'skill_1_crit_dmg' => $card_ennemy['skill_1_crit_dmg'],
                                'skill_2_crit_dmg' => $card_ennemy['skill_2_crit_dmg'],
                                'skill_1_precision' => $card_ennemy['skill_1_precision'],
                                'skill_2_precision' => $card_ennemy['skill_2_precision'],
                                'hp_left' => $card_ennemy['hp'],
                                'hp_max' => $card_ennemy['hp'],
                                'avatar' => $card_ennemy['avatar'],
                                'name' => $card_ennemy['name'],
                                'type' => $card_ennemy['type'],
                                'skill_1_name' => $card_ennemy['skill_1_name'],
                                'skill_2_name' => $card_ennemy['skill_2_name'],
                                'skill_1_desc' => $card_ennemy['skill_1_desc'],
                                'skill_2_desc' => $card_ennemy['skill_2_desc'],
                                'skill_1_type' => $card_ennemy['skill_1_type'],
                                'skill_2_type' => $card_ennemy['skill_2_type'],
                                'skill_1_effect' => $card_ennemy['skill_1_effect'],
                                'skill_2_effect' => $card_ennemy['skill_2_effect'],
                                'skill_1_base_value' => $card_ennemy['skill_1_base_value'],
                                'skill_2_base_value' => $card_ennemy['skill_2_base_value'],
                                'skill_1_targets' => $card_ennemy['skill_1_targets'],
                                'skill_2_targets' => $card_ennemy['skill_2_targets'],
                                'skill_1_target_type' => $card_ennemy['skill_1_target_type'],
                                'skill_2_target_type' => $card_ennemy['skill_2_target_type'],
                                'price' => $card_ennemy['price'],
                                'rarity' => $card_ennemy['rarity']
                            ]);
                        }
                    }

                    $rand = rand(1, 2);
                    $turn_data = ($rand == 1) ? [$player_1, $player_2] : [$player_2, $player_1];
                    
                    // Convertir le tableau en JSON pour stocker dans la base de données
                    $turn_data_json = json_encode($turn_data);
                    
                    // Préparer la requête pour mettre à jour la colonne 'turn' et 'turn_data'
                    $stmt_update_turn = $pdo->prepare("UPDATE games SET turn = 1, turn_data = :turn_data WHERE id = :game_id");
                    
                    // Exécuter la requête avec les données appropriées
                    $stmt_update_turn->execute([
                        'turn_data' => $turn_data_json,
                        'game_id' => $game_id // Assurez-vous que $game_id contient la valeur appropriée
                    ]);

                    // Renvoyer les résultats en JSON
                    echo json_encode([
                        'success' => true,
                        'players_info' => $players_info,
                        'deck_info_player' => $deck_info_player,
                        'deck_info_ennemy' => $deck_info_ennemy,
                        'cards_info_player' => $cards_info_player,
                        'cards_info_ennemy' => $cards_info_ennemy,
                        'game' => $game,
                        'game_info' => $game_info,
                    ]);
                } else {
                    echo json_encode([
                        'success' => true,
                        'players_info' => $players_info,
                        'decks_player' => $deck_info_player,
                        'decks_ennemy' => $deck_info_ennemy,
                        'cards_info_ennemy' => $cards_info_ennemy,
                        'game_info' => $game_info
                    ]);
                }
            } else {
                // Récupérer les informations des joueurs
                $stmt_cards_game = $pdo->prepare("SELECT * FROM cards_game WHERE game_id = :game_id");
                $stmt_cards_game->execute(['game_id' => $game_id]);
                $cards_game = $stmt_cards_game->fetchAll(PDO::FETCH_ASSOC);

                // Récupérer les informations des joueurs
                $stmt_players_game = $pdo->prepare("SELECT * FROM players_game WHERE game_id = :game_id");
                $stmt_players_game->execute(['game_id' => $game_id]);
                $players_game = $stmt_players_game->fetchAll(PDO::FETCH_ASSOC);


                $id_player_1 = $players_game[0]['player_id'];
                $id_player_2 = $players_game[1]['player_id'];
                
                $stmt_player_1_cards = $pdo->prepare("SELECT COUNT(*) FROM cards_game WHERE player_id = :id_player_1 AND hp_left <= 0 AND game_id = :game_id");
                $stmt_player_2_cards = $pdo->prepare("SELECT COUNT(*) FROM cards_game WHERE player_id = :id_player_2 AND hp_left <= 0 AND game_id = :game_id");

                $stmt_player_1_cards->execute(['id_player_1' => $id_player_1, 'game_id' => $game_id]);
                $stmt_player_2_cards->execute(['id_player_2' => $id_player_2, 'game_id' => $game_id]);

                $player_1_dead_cards = $stmt_player_1_cards->fetchColumn();
                $player_2_dead_cards = $stmt_player_2_cards->fetchColumn();

                if ($player_1_dead_cards == 4 || $player_2_dead_cards == 4) {
                    $looser_id = ($player_1_dead_cards == 4) ? $id_player_1 : $id_player_2;
                    $winner_id = ($looser_id == $id_player_1) ? $id_player_2 : $id_player_1;

                    if($looser_id == $_SESSION['user_id']) {
                        
                        // Mises à jour des points de rang
                        $stmt_looser_rank = $pdo->prepare("UPDATE players SET rank_point = GREATEST(rank_point - 10, 1000) WHERE id = :looser_id");
                        $stmt_winner_rank = $pdo->prepare("UPDATE players SET rank_point = rank_point + 10 WHERE id = :winner_id");

                        $stmt_looser_rank->bindParam(':looser_id', $looser_id, PDO::PARAM_INT);
                        $stmt_winner_rank->bindParam(':winner_id', $winner_id, PDO::PARAM_INT);

                        $stmt_looser_rank->execute();
                        $stmt_winner_rank->execute();

                        // Mises à jour de l'or
                        $stmt_winner_gold = $pdo->prepare("UPDATE players SET gold = gold + 200 WHERE id = :winner_id");
                        $stmt_looser_gold = $pdo->prepare("UPDATE players SET gold = gold + :gold WHERE id = :looser_id");

                        $dead_cards_count = $player_1_dead_cards == 4 ? $player_2_dead_cards : $player_1_dead_cards;
                        // Calculer l'or à ajouter pour le perdant
                        $looser_gold_increment = 50 * $dead_cards_count;

                        $stmt_winner_gold->bindParam(':winner_id', $winner_id, PDO::PARAM_INT);
                        $stmt_looser_gold->bindParam(':gold', $looser_gold_increment, PDO::PARAM_INT);
                        $stmt_looser_gold->bindParam(':looser_id', $looser_id, PDO::PARAM_INT);

                        $stmt_winner_gold->execute();
                        $stmt_looser_gold->execute();

                        // Mise à jour des victoires/défaites
                        $stmt_winner_victory = $pdo->prepare("UPDATE players SET victory = victory + 1 WHERE id = :winner_id");
                        $stmt_looser_defeat = $pdo->prepare("UPDATE players SET defeat = defeat + 1 WHERE id = :looser_id");

                        $stmt_winner_victory->bindParam(':winner_id', $winner_id, PDO::PARAM_INT);
                        $stmt_looser_defeat->bindParam(':looser_id', $looser_id, PDO::PARAM_INT);

                        $stmt_winner_victory->execute();
                        $stmt_looser_defeat->execute();

                        // Mise à jour des victoires/défaites des decks
                        $stmt_deck_winner = $pdo->prepare("UPDATE decks SET victory = victory + 1 WHERE deck_id = (SELECT deck FROM players WHERE id = :winner_id)");
                        $stmt_deck_looser = $pdo->prepare("UPDATE decks SET defeat = defeat + 1 WHERE deck_id = (SELECT deck FROM players WHERE id = :looser_id)");

                        $stmt_deck_winner->bindParam(':winner_id', $winner_id, PDO::PARAM_INT);
                        $stmt_deck_looser->bindParam(':looser_id', $looser_id, PDO::PARAM_INT);

                        $stmt_deck_winner->execute();
                        $stmt_deck_looser->execute();

                        // Remettre la colonne queue à null
                        $stmt_reset_queue = $pdo->prepare("UPDATE players SET queue = NULL WHERE id IN (:id_player_1, :id_player_2)");
                        $stmt_reset_queue->bindParam(':id_player_1', $id_player_1, PDO::PARAM_INT);
                        $stmt_reset_queue->bindParam(':id_player_2', $id_player_2, PDO::PARAM_INT);
                        $stmt_reset_queue->execute();

                        // Mise à jour du statut de la game
                        $stmt_game_update = $pdo->prepare("UPDATE games SET status = 'finished', winner = :winner_id, looser = :looser_id WHERE id = :game_id");
                        $stmt_game_update->bindParam(':winner_id', $winner_id, PDO::PARAM_INT);
                        $stmt_game_update->bindParam(':looser_id', $looser_id, PDO::PARAM_INT);
                        $stmt_game_update->bindParam(':game_id', $game_id, PDO::PARAM_INT);
                        $stmt_game_update->execute();
                    }

                    echo json_encode([
                        'success' => true,
                        'players_info' => $players_info,
                        'cards_game' => $cards_game,
                        'players_game' => $players_game,
                        'decks_player' => $deck_info_player,
                        'decks_ennemy' => $deck_info_ennemy,
                        'game' => $game,
                        'game_info' => $game_info,
                        'player_1_dead_cards' => $player_1_dead_cards,
                        'player_2_dead_cards' => $player_2_dead_cards,
                        'winner' => $winner_id,
                        'looser' => $looser_id,
                        'game' => false
                    ]);
                } else {
                    echo json_encode([
                        'success' => true,
                        'players_info' => $players_info,
                        'cards_game' => $cards_game,
                        'players_game' => $players_game,
                        'decks_player' => $deck_info_player,
                        'decks_ennemy' => $deck_info_ennemy,
                        'game' => $game,
                        'game_info' => $game_info
                    ]);
                }
            }
        } else {
            echo json_encode(['success' => false, 'game' => false,'message' => "Game not found"]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la récupération des joueurs : " . $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'finish_turn') {
    try {
        $user_id = $_SESSION['user_id'];
        $game_id = $_POST['game_id'];
        if($game_id){
            //Verifier si c'est au joueur de jouer
            $stmt_game_turn_data = $pdo->prepare("SELECT * FROM games WHERE id = :game_id AND (player_1_id = :user_id OR player_2_id = :user_id) AND status = :status_game");
            $stmt_game_turn_data->execute(['game_id' => $game_id, 'user_id' => $user_id, 'status_game' => 'in_progress']);
            $row_game_turn_data = $stmt_game_turn_data->fetch(PDO::FETCH_ASSOC);
            if ($row_game_turn_data) {
                // Décoder les données du tour depuis la base de données (PHP utilise json_decode)
                $turn_data = json_decode($row_game_turn_data['turn_data'], true);
                $turn = $row_game_turn_data['turn'];
                $new_turn = $row_game_turn_data['new_turn'];
            
                if (($turn % 2 == 0 && $turn_data[1] == $user_id) || ($turn % 2 !== 0 && $turn_data[0] == $user_id) && $new_turn == false) {
                    
                    // Incrémenter la colonne 'turn'
                    $stmt_turn = $pdo->prepare("UPDATE games SET turn = turn + 1 WHERE id = :game_id");
                    $stmt_turn->execute(['game_id' => $game_id]);
            
                    // Vérifier si la mise à jour du tour a été effectuée
                    if ($stmt_turn->rowCount() > 0) {
                        // Si la mise à jour du tour a réussi, mettre à jour 'new_turn'
                        $stmt_new_turn = $pdo->prepare("UPDATE games SET new_turn = true WHERE id = :game_id");
                        $stmt_new_turn->execute(['game_id' => $game_id]);
            
                        // Vérifier si la mise à jour de 'new_turn' a été effectuée
                        if ($stmt_new_turn->rowCount() > 0) {
                            echo json_encode(['success' => true, 'message' => 'Le tour et le statut de nouveau tour ont été mis à jour avec succès']);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Le statut de nouveau tour n\'a pas pu être mis à jour']);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Aucune ligne mise à jour, ID de jeu non trouvé']);
                    }
                } else {
                    // Ce n'est pas le tour de l'utilisateur
                    echo json_encode(['success' => false, 'game' => false, 'message' => "Ce n'est pas votre tour !"]);
                }
            } else {
                // Si les données de tour ne sont pas trouvées
                echo json_encode(['success' => false, 'message' => 'Données de tour non trouvées']);
            }
        } else {
            echo json_encode(['success' => false, 'game' => false,'message' => "Game not found"]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la récupération des joueurs : " . $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'refresh_animation') {
    try {
        $game_id = $_POST['game_id'];
        $user_id = $_POST['user_id'];
        $vue = $_POST['vue'];
        if($game_id){
            if ($vue == "player") {
                // Mettre VIEW_PLAYER à true dans le JSON
                $stmt_update_animation = $pdo->prepare("
                    UPDATE cards_game 
                    SET effect_fight = JSON_SET(effect_fight, '$[0].VIEW_PLAYER', 'true')
                    WHERE game_id = :game_id
                    AND JSON_EXTRACT(effect_fight, '$[0].VIEW_PLAYER') = 'false'
                ");
                $stmt_update_animation->execute(["game_id" => $game_id]);
            } else if ($vue == "ennemy") {
                // Mettre VIEW_ENNEMY à true dans le JSON
                $stmt_update_animation = $pdo->prepare("
                    UPDATE cards_game 
                    SET effect_fight = JSON_SET(effect_fight, '$[0].VIEW_ENNEMY', 'true')
                    WHERE game_id = :game_id
                    AND JSON_EXTRACT(effect_fight, '$[0].VIEW_ENNEMY') = 'false'
                ");
                $stmt_update_animation->execute(["game_id" => $game_id]);
            }

            $stmt_update_animation = $pdo->prepare("
                UPDATE cards_game 
                SET effect_fight = NULL 
                WHERE game_id = :game_id
                AND JSON_EXTRACT(effect_fight, '$[0].VIEW_PLAYER') = 'true' 
                AND JSON_EXTRACT(effect_fight, '$[0].VIEW_ENNEMY') = 'true'
            ");
            $stmt_update_animation->execute(["game_id" => $game_id]);
            echo json_encode(['success' => true, 'test' => "OK"]);
        } else {
            echo json_encode(['success' => false, 'game' => false,'message' => "Game not found"]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la récupération des joueurs : " . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Action non valide"]);
}