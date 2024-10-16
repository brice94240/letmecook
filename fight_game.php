<?php
session_start();

require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'refresh') {
    try {
        $player_id = $_POST['user_id'];
        $game_id = $_POST['game_id'];

        // Récupérer les informations du jeu
        $stmt_game = $pdo->prepare("SELECT * FROM games WHERE id = :game_id AND (player_1_id = :user_id OR player_2_id = :user_id)");
        $stmt_game->execute(['game_id' => $game_id, 'user_id' => $player_id]);
        $game_info = $stmt_game->fetch(PDO::FETCH_ASSOC);
        
        if ($game_info) {
            $new_turn = $game_info['new_turn'];
            if ($new_turn == true) {
                // GERER LES COOLDOWNS
                $stmt = $pdo->prepare("
                UPDATE cards_game 
                SET 
                    skill_1_cooldown = GREATEST(skill_1_cooldown - 1, 0), 
                    skill_2_cooldown = GREATEST(skill_2_cooldown - 1, 0),
                    skill_1_active = 1,
                    skill_2_active = 1

                WHERE 
                    game_id = :game_id 
                    AND player_id = :player_id 
                    AND active = 1 
                    AND hp_left > 0
                ");

                // Exécuter la requête avec les paramètres
                $stmt->execute([
                    'game_id' => $game_id,
                    'player_id' => $player_id
                ]);

                // Mettre à jour 'game.new_turn' à false
                $stmt_update_turn = $pdo->prepare("
                    UPDATE games 
                    SET new_turn = 0
                    WHERE id = :game_id
                ");
                $stmt_update_turn->execute(['game_id' => $game_id]);

                // Gérer les effets des cartes
                $stmt_cards_player_with_affects_effects = $pdo->prepare("
                    SELECT * 
                    FROM cards_game 
                    WHERE game_id = :game_id 
                    AND player_id = :player_id 
                    AND active = 1 
                    AND hp_left > 0 
                    AND affect_effect IS NOT NULL
                ");
                $stmt_cards_player_with_affects_effects->execute(['game_id' => $game_id, 'player_id' => $player_id]);
                $cards_player_with_affects_effects = $stmt_cards_player_with_affects_effects->fetchAll(PDO::FETCH_ASSOC);

                if ($cards_player_with_affects_effects) {
                    foreach ($cards_player_with_affects_effects as $card) {
                        $effects = json_decode($card['affect_effect'], true);
                        if (is_array($effects)) {
                            // Tableau temporaire pour les effets valides
                            $valid_effects = [];
                
                            foreach ($effects as $key => $effect) {
                                // Appliquer les conditions pour chaque type d'effet
                                switch ($effect['TYPE']) {
                                    case 'POISON':
                                        $new_hp_left = $card['hp_left'] - $effect['VALUE'];
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = :new_hp_left 
                                            WHERE id = :card_id
                                        ");
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => max(0, $new_hp_left),
                                            'card_id' => $card['id']
                                        ]);
                
                                        // Attribuer à $card['hp_left'] la nouvelle valeur après la mise à jour
                                        $card['hp_left'] = max(0, $new_hp_left); 
                
                                        // Décrémenter la durée
                                        $effect['DURATION']--;
                
                                        // Vérifier si l'effet doit rester valide
                                        if ($effect['DURATION'] > 0) {
                                            $valid_effects[] = $effect; // Ajouter à la liste des effets valides
                                        }
                                        continue;
                
                                    case 'REGENERATE':
                                        // Appliquer l'effet Heal : augmenter les points de vie
                                        $new_hp_left = $card['hp_left'] + $effect['VALUE'];
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = :new_hp_left 
                                            WHERE id = :card_id
                                        ");
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => min($new_hp_left, $card['hp_max']),
                                            'card_id' => $card['id']
                                        ]);
                
                                        // Attribuer à $card['hp_left'] la nouvelle valeur après la mise à jour
                                        $card['hp_left'] = min($new_hp_left, $card['hp_max']); 
                
                                        // Décrémenter la durée
                                        $effect['DURATION']--;
                
                                        // Vérifier si l'effet doit rester valide
                                        if ($effect['DURATION'] > 0) {
                                            $valid_effects[] = $effect; // Ajouter à la liste des effets valides
                                        }
                                        continue;
                
                                    case 'BLOCK':
                                        // Appliquer l'effet Block : Interdit de jouer
                                        $skill_1_active = 0;
                                        $skill_2_active = 0;
                
                                        $stmt_update_block = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET skill_1_active = :skill_1_active,
                                                skill_2_active = :skill_2_active
                                            WHERE id = :card_id
                                        ");
                                        $stmt_update_block->execute([
                                            'skill_1_active' => $skill_1_active,
                                            'skill_2_active' => $skill_2_active,
                                            'card_id' => $card['id']
                                        ]);
                
                                        // Décrémenter la durée
                                        $effect['DURATION']--;
                                        if ($effect['DURATION'] > 0) {
                                            $valid_effects[] = $effect; // Ajouter à la liste des effets valides
                                        } else {
                                            $stmt_update_active_skill_1 = $pdo->prepare("
                                                UPDATE cards_game 
                                                SET skill_1_active = :skill_1_active,
                                                    skill_2_active = :skill_2_active
                                                WHERE id = :card_id
                                            ");
                                            $stmt_update_active_skill_1->execute([
                                                'skill_1_active' => 1,
                                                'skill_2_active' => 1,
                                                'card_id' => $card['id']
                                            ]);
                                        }
                                        continue;
                                    
                                    case 'BLOCK_SKILL_1':
                                        // Appliquer l'effet Block_Skill_1 : Interdit de jouer le sort 1
                                        $skill_1_active = 0;
                
                                        $stmt_update_block_skill_1 = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET skill_1_active = :skill_1_active
                                            WHERE id = :card_id
                                        ");
                                        $stmt_update_block_skill_1->execute([
                                            'skill_1_active' => $skill_1_active,
                                            'card_id' => $card['id']
                                        ]);
                
                                        // Décrémenter la durée
                                        $effect['DURATION']--;
                                        if ($effect['DURATION'] > 0) {
                                            $valid_effects[] = $effect; // Ajouter à la liste des effets valides
                                        } else {
                                            $stmt_update_active_skill_1 = $pdo->prepare("
                                                UPDATE cards_game 
                                                SET skill_1_active = :skill_1_active
                                                WHERE id = :card_id
                                            ");
                                            $stmt_update_active_skill_1->execute([
                                                'skill_1_active' => 1,
                                                'card_id' => $card['id']
                                            ]);
                                        }
                                        continue;

                                    case 'BLOCK_SKILL_2':
                                        // Appliquer l'effet Block_Skill_2 : Interdit de jouer le sort 2
                                        $skill_2_active = 0;
                
                                        $stmt_update_block_skill_2 = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET skill_2_active = :skill_2_active
                                            WHERE id = :card_id
                                        ");
                                        $stmt_update_block_skill_2->execute([
                                            'skill_2_active' => $skill_2_active,
                                            'card_id' => $card['id']
                                        ]);
                
                                        // Décrémenter la durée
                                        $effect['DURATION']--;
                                        if ($effect['DURATION'] > 0) {
                                            $valid_effects[] = $effect; // Ajouter à la liste des effets valides
                                        } else {
                                            $stmt_update_active_skill_2 = $pdo->prepare("
                                                UPDATE cards_game 
                                                SET skill_2_active = :skill_2_active
                                                WHERE id = :card_id
                                            ");
                                            $stmt_update_active_skill_2->execute([
                                                'skill_2_active' => 1,
                                                'card_id' => $card['id']
                                            ]);
                                        }
                                        continue;
                
                                    default:
                                        echo "Effet inconnu : " . $effect['TYPE'];
                                        continue;
                                }
                            }
                
                            // Mettre à jour les effets dans la base de données uniquement si des effets valides restent
                            if (!empty($valid_effects)) {
                                $updated_effects_json = json_encode($valid_effects);
                                $stmt_update_effects = $pdo->prepare("
                                    UPDATE cards_game 
                                    SET affect_effect = :updated_effects 
                                    WHERE id = :card_id
                                ");
                                $stmt_update_effects->execute([
                                    'updated_effects' => $updated_effects_json,
                                    'card_id' => $card['id']
                                ]);
                            } else {
                                // Si aucun effet ne reste, mettre à jour la colonne pour être vide ou à null
                                $stmt_clear_effects = $pdo->prepare("
                                    UPDATE cards_game 
                                    SET affect_effect = NULL 
                                    WHERE id = :card_id
                                ");
                                $stmt_clear_effects->execute([
                                    'card_id' => $card['id']
                                ]);
                            }
                        }
                    }
                    echo json_encode(['success' => true, 'new_turn' => true]);
                }
                
                else {
                    echo json_encode(['success' => false, 'new_turn' => true]);
                }
            } else {
                // Gérer les skills spéciaux
                $special_skills = [
                    "TRADE_BOOST_FIRE",
                    "TRADE_BOOST_WATER",
                    "TRADE_BOOST_EARTH",
                    "TRADE_BOOST_AIR",
                    "TRADE_BOOST_DARK",
                    "TRADE_BOOST_LIGHT",
                ];

                // Créer une chaîne SQL dynamique pour les conditions OR
                $conditions = [];
                foreach ($special_skills as $skill) {
                    $skill_quoted = $pdo->quote($skill); // Utiliser quote pour échapper les valeurs correctement
                    $conditions[] = "JSON_EXTRACT(skill_1_effect, '$[0].TYPE') = $skill_quoted";
                    $conditions[] = "JSON_EXTRACT(skill_2_effect, '$[0].TYPE') = $skill_quoted";
                }

                // Joindre les conditions avec OR
                $conditions_sql = implode(" OR ", $conditions);

                // Construire la requête SQL complète
                $query = "
                    SELECT * FROM cards_game 
                    WHERE game_id = :game_id
                    AND player_id = :player_id
                    AND active = 1 
                    AND hp_left > 0 
                    AND ($conditions_sql)
                ";


                // Exécuter la requête
                $stmt_cards_player_with_special_skills = $pdo->prepare($query);
                // Exécuter la requête avec les paramètres
                $stmt_cards_player_with_special_skills->execute([
                    ':game_id' => $game_id,       // Assurez-vous d'avoir défini $game_id au préalable
                    ':player_id' => $player_id,   // Assurez-vous d'avoir défini $player_id au préalable
                ]);
                $cards_with_special_skills = $stmt_cards_player_with_special_skills->fetchAll(PDO::FETCH_ASSOC);

                if ($cards_with_special_skills) {
                    foreach ($cards_with_special_skills as $card) {
                        // Récupérer les effets des skills spéciaux
                        $effects1 = json_decode($card['skill_1_effect'], true) ?: [];
                        $effects2 = json_decode($card['skill_2_effect'], true) ?: [];

                        // Ajouter une clé pour indiquer l'origine (skill_1 ou skill_2) avec une fonction anonyme
                        $effects1 = array_map(function($effect) {
                            return array_merge($effect, ['skill' => 'skill_1']);
                        }, $effects1);

                        $effects2 = array_map(function($effect) {
                            return array_merge($effect, ['skill' => 'skill_2']);
                        }, $effects2);

                        // Fusionner les effets
                        $effects = array_merge($effects1, $effects2);
                        
                        if (is_array($effects)) {
                            foreach ($effects as $key => $effect) {
                                // Récupérer le nom du skill spécial
                                $skill_name = $effect['TYPE'];

                                // Appliquer les conditions pour chaque type d'effet
                                switch ($skill_name) {
                                    case 'TRADE_BOOST_FIRE':
                                        $price_boost = $effect['PRICE'] ? $effect['PRICE'] : 1;
                                        $type_boost = str_replace('trade_boost', 'base',strtolower($effect['TYPE'])); //Element requis

                                        $stmt_player_info = $pdo->prepare("
                                            SELECT * 
                                            FROM players_game 
                                            WHERE player_id = :player_id 
                                            AND game_id = :game_id
                                        ");
                                        // Exécuter la requête avec player_id et game_id
                                        $stmt_player_info->execute([
                                            ':player_id' => $player_id,
                                            ':game_id' => $game_id,
                                        ]);
                                        // Récupérer les informations du joueur
                                        $player_info = $stmt_player_info->fetch(PDO::FETCH_ASSOC);
                                        $skill_active_column = $effect['skill'] . '_active';
                                        if($player_info[$type_boost] >= $price_boost){
                                            $active = 1;
                                        } else {
                                            $active = 0;
                                        }
                                        // Préparer la requête de mise à jour
                                        $update_stmt = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET $skill_active_column = :active
                                            WHERE player_id = :player_id 
                                            AND game_id = :game_id
                                            AND card_id = :card_id
                                        ");
                                        // Exécuter la mise à jour
                                        $update_stmt->execute([
                                            ':active' => $active,
                                            ':player_id' => $player_id,
                                            ':game_id' => $game_id,
                                            ':card_id' => $card['card_id'],
                                        ]);

                                        break;
                                    case 'TRADE_BOOST_WATER':
                                        $price_boost = $effect['PRICE'] ? $effect['PRICE'] : 1;
                                        $type_boost = str_replace('trade_boost', 'base',strtolower($effect['TYPE'])); //Element requis

                                        $stmt_player_info = $pdo->prepare("
                                            SELECT * 
                                            FROM players_game 
                                            WHERE player_id = :player_id 
                                            AND game_id = :game_id
                                        ");
                                        // Exécuter la requête avec player_id et game_id
                                        $stmt_player_info->execute([
                                            ':player_id' => $player_id,
                                            ':game_id' => $game_id,
                                        ]);
                                        // Récupérer les informations du joueur
                                        $player_info = $stmt_player_info->fetch(PDO::FETCH_ASSOC);
                                        $skill_active_column = $effect['skill'] . '_active';
                                        if($player_info[$type_boost] >= $price_boost){
                                            $active = 1;
                                        } else {
                                            $active = 0;
                                        }
                                        // Préparer la requête de mise à jour
                                        $update_stmt = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET $skill_active_column = :active
                                            WHERE player_id = :player_id 
                                            AND game_id = :game_id
                                            AND card_id = :card_id
                                        ");
                                        // Exécuter la mise à jour
                                        $update_stmt->execute([
                                            ':active' => $active,
                                            ':player_id' => $player_id,
                                            ':game_id' => $game_id,
                                            ':card_id' => $card['card_id'],
                                        ]);

                                        break;
                                    case 'TRADE_BOOST_EARTH':
                                        $price_boost = $effect['PRICE'] ? $effect['PRICE'] : 1;
                                        $type_boost = str_replace('trade_boost', 'base',strtolower($effect['TYPE'])); //Element requis

                                        $stmt_player_info = $pdo->prepare("
                                            SELECT * 
                                            FROM players_game 
                                            WHERE player_id = :player_id 
                                            AND game_id = :game_id
                                        ");
                                        // Exécuter la requête avec player_id et game_id
                                        $stmt_player_info->execute([
                                            ':player_id' => $player_id,
                                            ':game_id' => $game_id,
                                        ]);
                                        // Récupérer les informations du joueur
                                        $player_info = $stmt_player_info->fetch(PDO::FETCH_ASSOC);
                                        $skill_active_column = $effect['skill'] . '_active';
                                        if($player_info[$type_boost] >= $price_boost){
                                            $active = 1;
                                        } else {
                                            $active = 0;
                                        }
                                        // Préparer la requête de mise à jour
                                        $update_stmt = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET $skill_active_column = :active
                                            WHERE player_id = :player_id 
                                            AND game_id = :game_id
                                            AND card_id = :card_id
                                        ");
                                        // Exécuter la mise à jour
                                        $update_stmt->execute([
                                            ':active' => $active,
                                            ':player_id' => $player_id,
                                            ':game_id' => $game_id,
                                            ':card_id' => $card['card_id'],
                                        ]);

                                        break;
                                    case 'TRADE_BOOST_AIR':
                                        $price_boost = $effect['PRICE'] ? $effect['PRICE'] : 1;
                                        $type_boost = str_replace('trade_boost', 'base',strtolower($effect['TYPE'])); //Element requis

                                        $stmt_player_info = $pdo->prepare("
                                            SELECT * 
                                            FROM players_game 
                                            WHERE player_id = :player_id 
                                            AND game_id = :game_id
                                        ");
                                        // Exécuter la requête avec player_id et game_id
                                        $stmt_player_info->execute([
                                            ':player_id' => $player_id,
                                            ':game_id' => $game_id,
                                        ]);
                                        // Récupérer les informations du joueur
                                        $player_info = $stmt_player_info->fetch(PDO::FETCH_ASSOC);
                                        $skill_active_column = $effect['skill'] . '_active';
                                        if($player_info[$type_boost] >= $price_boost){
                                            $active = 1;
                                        } else {
                                            $active = 0;
                                        }
                                        // Préparer la requête de mise à jour
                                        $update_stmt = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET $skill_active_column = :active
                                            WHERE player_id = :player_id 
                                            AND game_id = :game_id
                                            AND card_id = :card_id
                                        ");
                                        // Exécuter la mise à jour
                                        $update_stmt->execute([
                                            ':active' => $active,
                                            ':player_id' => $player_id,
                                            ':game_id' => $game_id,
                                            ':card_id' => $card['card_id'],
                                        ]);

                                        break;
                                    case 'TRADE_BOOST_DARK':
                                        $price_boost = $effect['PRICE'] ? $effect['PRICE'] : 1;
                                        $type_boost = str_replace('trade_boost', 'base',strtolower($effect['TYPE'])); //Element requis

                                        $stmt_player_info = $pdo->prepare("
                                            SELECT * 
                                            FROM players_game 
                                            WHERE player_id = :player_id 
                                            AND game_id = :game_id
                                        ");
                                        // Exécuter la requête avec player_id et game_id
                                        $stmt_player_info->execute([
                                            ':player_id' => $player_id,
                                            ':game_id' => $game_id,
                                        ]);
                                        // Récupérer les informations du joueur
                                        $player_info = $stmt_player_info->fetch(PDO::FETCH_ASSOC);
                                        $skill_active_column = $effect['skill'] . '_active';
                                        if($player_info[$type_boost] >= $price_boost){
                                            $active = 1;
                                        } else {
                                            $active = 0;
                                        }
                                        // Préparer la requête de mise à jour
                                        $update_stmt = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET $skill_active_column = :active
                                            WHERE player_id = :player_id 
                                            AND game_id = :game_id
                                            AND card_id = :card_id
                                        ");
                                        // Exécuter la mise à jour
                                        $update_stmt->execute([
                                            ':active' => $active,
                                            ':player_id' => $player_id,
                                            ':game_id' => $game_id,
                                            ':card_id' => $card['card_id'],
                                        ]);

                                        break;
                                    case 'TRADE_BOOST_LIGHT':
                                        $price_boost = $effect['PRICE'] ? $effect['PRICE'] : 1;
                                        $type_boost = str_replace('trade_boost', 'base',strtolower($effect['TYPE'])); //Element requis

                                        $stmt_player_info = $pdo->prepare("
                                            SELECT * 
                                            FROM players_game 
                                            WHERE player_id = :player_id 
                                            AND game_id = :game_id
                                        ");
                                        // Exécuter la requête avec player_id et game_id
                                        $stmt_player_info->execute([
                                            ':player_id' => $player_id,
                                            ':game_id' => $game_id,
                                        ]);
                                        // Récupérer les informations du joueur
                                        $player_info = $stmt_player_info->fetch(PDO::FETCH_ASSOC);
                                        $skill_active_column = $effect['skill'] . '_active';
                                        if($player_info[$type_boost] >= $price_boost){
                                            $active = 1;
                                        } else {
                                            $active = 0;
                                        }
                                        // Préparer la requête de mise à jour
                                        $update_stmt = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET $skill_active_column = :active
                                            WHERE player_id = :player_id 
                                            AND game_id = :game_id
                                            AND card_id = :card_id
                                        ");
                                        // Exécuter la mise à jour
                                        $update_stmt->execute([
                                            ':active' => $active,
                                            ':player_id' => $player_id,
                                            ':game_id' => $game_id,
                                            ':card_id' => $card['card_id'],
                                        ]);

                                        break;
                                    default:
                                        break;
                                }
                            }
                        }
                    }
                }
                echo json_encode(['success' => false, 'new_turn' => false]);
            }
        } else {
            echo json_encode(['success' => false, 'game' => false, 'message' => "Game not found"]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la récupération des joueurs : " . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Action non valide"]);
}
