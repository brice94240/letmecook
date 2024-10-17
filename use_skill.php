<?php
session_start();

require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'choice_skill') {
    try {
        $player_id = $_POST['user_id'];
        $game_id = $_POST['game_id'];
        $sort = $_POST['sort'];

        // Récupérer les informations du jeu
        $stmt_game = $pdo->prepare("SELECT * FROM games WHERE id = :game_id AND (player_1_id = :user_id OR player_2_id = :user_id)");
        $stmt_game->execute(['game_id' => $game_id, 'user_id' => $player_id]);
        $game_info = $stmt_game->fetch(PDO::FETCH_ASSOC);
        
        if ($game_info) {
            $turn = $game_info['turn'];
            $turn_data = json_decode($game_info['turn_data']);
            //imaginons turn_data = [1,2]
            $turn_player = $turn % 2 == 0 ? $turn_data[1] : $turn_data[0];
            if($turn_player == $player_id) {
                preg_match('/skill_(\d+)_carte_player_(\d+)/', $sort, $matches);
                $numero_skill = intval($matches[1]);
                $numero_carte = intval($matches[2]);

                // Récupérer les informations du jeu
                $stmt_player_playing = $pdo->prepare("SELECT * FROM players WHERE id = :player_id");
                $stmt_player_playing->execute(['player_id' => $player_id]);
                $player_playing = $stmt_player_playing->fetch(PDO::FETCH_ASSOC);
                $deck_use = $player_playing['deck'];

                $stmt_card_use = $pdo->prepare("SELECT * FROM decks WHERE player_id = :player_id AND deck_id = :deck_id");
                $stmt_card_use->execute(['player_id' => $player_id, 'deck_id' => $deck_use]);
                $cards_use = $stmt_card_use->fetch(PDO::FETCH_ASSOC);
                $card_id = $cards_use["card_".$numero_carte."_id"];

                //VERIFIER SI LE SKILL N'EST PAS BLOQUER ET SI LE COOLDOWN EST PAS A 0
                $stmt_card_info = $pdo->prepare("SELECT * FROM cards_game WHERE player_id = :player_id AND deck_id = :deck_id AND game_id = :game_id AND card_id = :card_id");
                $stmt_card_info->execute(['player_id' => $player_id, 'deck_id' => $cards_use['id'], 'game_id' => $game_id, 'card_id' => $card_id]);
                $card_info = $stmt_card_info->fetch(PDO::FETCH_ASSOC);
                
                echo json_encode(['success' => true, 'game' => true, 'use_skill' => true, 'turn_player' => $turn_player, 'numero_skill' => $numero_skill, 'numero_player' => $numero_carte, "card_id" => $card_id, "card_info" => $card_info]);
            } else {
                echo json_encode(['success' => false, 'game' => true, 'message' => "Pas votre tour !"]);
            }
            
        } else {
            echo json_encode(['success' => false, 'game' => false, 'message' => "Game not found"]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la récupération du sort : " . $e->getMessage()]);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'use_skill_ennemie') {
    try {
        $player_id = $_POST['user_id'];
        $game_id = $_POST['game_id'];
        $TabTarget = $_POST['TabTarget'];

        // Récupérer les informations du jeu
        $stmt_game = $pdo->prepare("SELECT * FROM games WHERE id = :game_id AND (player_1_id = :user_id OR player_2_id = :user_id)");
        $stmt_game->execute(['game_id' => $game_id, 'user_id' => $player_id]);
        $game_info = $stmt_game->fetch(PDO::FETCH_ASSOC);
        
        if ($game_info) {
            $turn = $game_info['turn'];
            $turn_data = json_decode($game_info['turn_data']);
            $turn_player = $turn % 2 == 0 ? $turn_data[1] : $turn_data[0];
            $ennemy_id = intval($game_info['player_1_id']) !== intval($player_id) ? intval($game_info['player_1_id']) : intval($game_info['player_2_id']);
            if($turn_player == $player_id) {
                $numero_carte = intval($TabTarget[0]);
                $numero_skill = intval($TabTarget[1]);

                $stmt_players_game_info = $pdo->prepare("SELECT * FROM players_game WHERE player_id = :player_id AND game_id = :game_id");
                $stmt_players_game_info->execute(['player_id' => $player_id, 'game_id' => $game_id]);
                $players_game_info = $stmt_players_game_info->fetch(PDO::FETCH_ASSOC);

                $stmt_ennemy_game_info = $pdo->prepare("SELECT * FROM players_game WHERE player_id = :ennemy_id AND game_id = :game_id");
                $stmt_ennemy_game_info->execute(['ennemy_id' => $card_info_ennemy['player_id'], 'game_id' => $game_id]);
                $ennemy_game_info = $stmt_ennemy_game_info->fetch(PDO::FETCH_ASSOC);

                $stmt_card_info = $pdo->prepare("SELECT * FROM cards_game WHERE player_id = :player_id AND card_id = :card_id AND game_id = :game_id AND card_id = :card_id");
                $stmt_card_info->execute(['player_id' => $player_id, 'card_id' => $numero_carte, 'game_id' => $game_id]);
                $card_info = $stmt_card_info->fetch(PDO::FETCH_ASSOC);

                $skill_use_name = $numero_skill == 1 ? $card_info['skill_1_name'] : $card_info['skill_2_name'];
                $skill_use_active = $numero_skill == 1 ? $card_info['skill_1_active'] : $card_info['skill_2_active'];
                $skill_use_base_value = $numero_skill == 1 ? $card_info['skill_1_base_value'] : $card_info['skill_2_base_value'];
                $skill_use_effect = $numero_skill == 1 ? $card_info['skill_1_effect'] : $card_info['skill_2_effect'];
                $skill_use_type = $numero_skill == 1 ? strtolower($card_info['type']) : strtolower($card_info['type']);
                $skill_use_crit = $numero_skill == 1 ? $card_info['skill_1_crit'] : $card_info['skill_2_crit'];
                $skill_use_crit_dmg = $numero_skill == 1 ? $card_info['skill_1_crit_dmg'] : $card_info['skill_2_crit_dmg'];
                $skill_use_precision = $numero_skill == 1 ? $card_info['skill_1_precision'] : $card_info['skill_2_precision'];
                $skill_use_cooldown = $numero_skill == 1 ? $card_info['skill_1_cooldown'] : $card_info['skill_2_cooldown'];

                $boost_value = $players_game_info["base_" . $skill_use_type];
                $boost_crit = $players_game_info['base_crit'];
                $boost_crit_dmg = $players_game_info['base_crit_dmg'];
                $boost_precision = $players_game_info['base_precision'];
                $precision = false;
                $crit = false;

                $effects = json_decode($skill_use_effect, true);

                //Ma chance de reussir le sort = $skill_use_precision + $boost_precision ca me fait un pourcentage
                $chance_reussite = $skill_use_precision + $boost_precision; // Chance totale de réussite
                // Générer un nombre aléatoire entre 0 et 100
                $random_precision = rand(0, 100);
                // Condition pour déterminer si le sort réussit
                if($skill_use_cooldown == 0){
                    if ($random_precision <= $chance_reussite) {
                        $precision = true;
                        //Ma chance de reussir le sort = $skill_use_precision + $boost_precision ca me fait un pourcentage
                        $chance_crit = $skill_use_crit + $boost_crit; // Chance totale de réussite
                        $random_crit = rand(0, 100);
                        if ($random_crit <= $chance_crit) {
                            $crit = true;
                            // Calcul des dégâts pour un coup critique
                            $skill_final_damage = (($skill_use_base_value + $boost_value)*($skill_use_crit_dmg+$boost_crit_dmg))/100;
                        } else {
                            $crit = false;
                            // Dégâts normaux
                            $skill_final_damage = $skill_use_base_value + $boost_value;
                        }
                    }
    
                    if($precision == true){ 
                        // Parcourir les effets
                        foreach ($effects as $effect) {
                            if (isset($effect['TYPE'])) {
                                switch ($effect['TYPE']) {
                                    case 'REDUCT_BOOSTS':
                                        $boost_reduc = !empty($effect['DURATION']) ? $effect['DURATION'] : 1;  // Utiliser 'DURATION' ou 1 par défaut
                                        $stmt_update_boost_element = $pdo->prepare("
                                            UPDATE players_game 
                                            SET base_fire = GREATEST(0, base_fire - :boost_reduc), base_water = GREATEST(0, base_water - :boost_reduc), base_earth = GREATEST(0, base_earth - :boost_reduc),
                                            base_air = GREATEST(0, base_air - :boost_reduc), base_dark = GREATEST(0, base_dark - :boost_reduc), base_light = GREATEST(0, base_light - :boost_reduc)
                                            WHERE player_id = :ennemy_id AND game_id = :game_id
                                        ");
                                    
                                        // Exécuter la mise à jour du boost
                                        $stmt_update_boost_element->execute([
                                            'boost_reduc' => max(0, $boost_reduc),
                                            'ennemy_id' => $ennemy_id,  // Corrigé : utiliser l'ID du joueur
                                            'game_id' => $game_id
                                        ]);
                                        break;
    
                                    case 'REDUCT_BOOST_ALEATORY':
                                        $boost_reduc = !empty($effect['DURATION']) ? $effect['DURATION'] : 1;  // Utiliser 'DURATION' ou 1 par défaut
                                        // Tableau avec les éléments
                                        $elements = [
                                            'base_fire',
                                            'base_water',
                                            'base_earth',
                                            'base_air',
                                            'base_dark',
                                            'base_light'
                                        ];
    
                                        // Tirer un élément aléatoire
                                        $random_element = $elements[array_rand($elements)];  // Récupère un élément aléatoire du tableau
    
                                        $stmt_update_boost_element = $pdo->prepare("
                                            UPDATE players_game 
                                            SET ".$random_element." = GREATEST(0, ".$random_element." - :boost_reduc)
                                            WHERE player_id = :ennemy_id AND game_id = :game_id
                                        ");
                                    
                                        // Exécuter la mise à jour du boost
                                        $stmt_update_boost_element->execute([
                                            'boost_reduc' => max(0, $boost_reduc),
                                            'ennemy_id' => $ennemy_id,  // Corrigé : utiliser l'ID du joueur
                                            'game_id' => $game_id
                                        ]);
                                        break;
                                    case 'REDUCT_TWO_BOOSTS_ALEATORY':
                                        $boost_reduc = !empty($effect['DURATION']) ? $effect['DURATION'] : 1;  // Utiliser 'DURATION' ou 1 par défaut
                                        // Tableau avec les éléments
                                        $elements = [
                                            'base_fire',
                                            'base_water',
                                            'base_earth',
                                            'base_air',
                                            'base_dark',
                                            'base_light'
                                        ];
                                        
                                        // Tirer un premier élément aléatoire
                                        $random_key = array_rand($elements);  // Récupère la clé aléatoire
                                        $random_element = $elements[$random_key];  // Stocke l'élément
                                        unset($elements[$random_key]);  // Supprime cet élément du tableau
                                        
                                        // Tirer un deuxième élément aléatoire
                                        $random_key_2 = array_rand($elements);  // Récupère une nouvelle clé aléatoire
                                        $random_element_2 = $elements[$random_key_2];  // Stocke le deuxième élément
    
                                        $stmt_update_boost_element = $pdo->prepare("
                                            UPDATE players_game 
                                            SET ".$random_element." = GREATEST(0, ".$random_element." - :boost_reduc),
                                                ".$random_element_2." = GREATEST(0, ".$random_element_2." - :boost_reduc)
                                            WHERE player_id = :ennemy_id AND game_id = :game_id
                                        ");
                                    
                                        // Exécuter la mise à jour du boost
                                        $stmt_update_boost_element->execute([
                                            'boost_reduc' => max(0, $boost_reduc),
                                            'ennemy_id' => $ennemy_id,  // Corrigé : utiliser l'ID du joueur
                                            'game_id' => $game_id
                                        ]);
                                        break;
      
                                    case 'REDUCT_THREE_BOOSTS_ALEATORY':
                                        $boost_reduc = !empty($effect['DURATION']) ? $effect['DURATION'] : 1;  // Utiliser 'DURATION' ou 1 par défaut
                                        // Tableau avec les éléments
                                        $elements = [
                                            'base_fire',
                                            'base_water',
                                            'base_earth',
                                            'base_air',
                                            'base_dark',
                                            'base_light'
                                        ];
                                        
                                        $random_key = array_rand($elements);  // Récupère la clé aléatoire
                                        $random_element = $elements[$random_key];  // Stocke l'élément
                                        unset($elements[$random_key]);  // Supprime cet élément du tableau
                                        
                                        $random_key_2 = array_rand($elements);  // Récupère la clé aléatoire
                                        $random_element_2 = $elements[$random_key_2];  // Stocke l'élément
                                        unset($elements[$random_key_2]);  // Supprime cet élément du tableau
    
                                        $random_key_3 = array_rand($elements);  // Récupère la clé aléatoire
                                        $random_element_3 = $elements[$random_key_3];  // Stocke l'élément
                                        unset($elements[$random_key_3]);  // Supprime cet élément du tableau
    
                                        $stmt_update_boost_element = $pdo->prepare("
                                            UPDATE players_game 
                                            SET ".$random_element." = GREATEST(0, ".$random_element." - :boost_reduc),
                                                ".$random_element_2." = GREATEST(0, ".$random_element_2." - :boost_reduc),
                                                ".$random_element_3." = GREATEST(0, ".$random_element_3." - :boost_reduc)
                                            WHERE player_id = :ennemy_id AND game_id = :game_id
                                        ");
                                    
                                        // Exécuter la mise à jour du boost
                                        $stmt_update_boost_element->execute([
                                            'boost_reduc' => max(0, $boost_reduc),
                                            'ennemy_id' => $ennemy_id,  // Corrigé : utiliser l'ID du joueur
                                            'game_id' => $game_id
                                        ]);
                                        break;
        
                                    case 'REDUCT_FOUR_BOOSTS_ALEATORY':
                                        $boost_reduc = !empty($effect['DURATION']) ? $effect['DURATION'] : 1;  // Utiliser 'DURATION' ou 1 par défaut
                                        // Tableau avec les éléments
                                        $random_key = array_rand($elements);  // Récupère la clé aléatoire
                                        $random_element = $elements[$random_key];  // Stocke l'élément
                                        unset($elements[$random_key]);  // Supprime cet élément du tableau
                                        
                                        $random_key_2 = array_rand($elements);  // Récupère la clé aléatoire
                                        $random_element_2 = $elements[$random_key_2];  // Stocke l'élément
                                        unset($elements[$random_key_2]);  // Supprime cet élément du tableau
    
                                        $random_key_3 = array_rand($elements);  // Récupère la clé aléatoire
                                        $random_element_3 = $elements[$random_key_3];  // Stocke l'élément
                                        unset($elements[$random_key_3]);  // Supprime cet élément du tableau
    
                                        $random_key_4 = array_rand($elements);  // Récupère la clé aléatoire
                                        $random_element_4 = $elements[$random_key_4];  // Stocke l'élément
                                        unset($elements[$random_key_4]);  // Supprime cet élément du tableau
    
                                        $stmt_update_boost_element = $pdo->prepare("
                                            UPDATE players_game 
                                            SET ".$random_element." = GREATEST(0, ".$random_element." - :boost_reduc),
                                                ".$random_element_2." = GREATEST(0, ".$random_element_2." - :boost_reduc),
                                                ".$random_element_3." = GREATEST(0, ".$random_element_3." - :boost_reduc),
                                                 ".$random_element_4." = GREATEST(0, ".$random_element_4." - :boost_reduc)
                                            WHERE player_id = :ennemy_id AND game_id = :game_id
                                        ");
                                    
                                        // Exécuter la mise à jour du boost
                                        $stmt_update_boost_element->execute([
                                            'boost_reduc' => max(0, $boost_reduc),
                                            'ennemy_id' => $ennemy_id,  // Corrigé : utiliser l'ID du joueur
                                            'game_id' => $game_id
                                        ]);
                                        break;
        
                                    case 'HIT_ALL':
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $skill_final_damage;
                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = GREATEST(0, hp_left - :new_hp_left)
                                            WHERE player_id = :ennemy_id AND game_id = :game_id AND hp_left > 0 AND active = 1
                                        ");
    
                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => $new_hp_left,
                                            'ennemy_id' => $ennemy_id,
                                            'game_id' => $game_id
                                        ]);
                                        break;
                                    case 'HIT_ALEATORY':
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $skill_final_damage;
                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = GREATEST(0, hp_left - :new_hp_left)
                                            WHERE player_id = :ennemy_id AND game_id = :game_id AND hp_left > 0
                                            AND active = 1
                                            ORDER BY RAND()
                                            LIMIT 1
                                        ");
    
                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => $new_hp_left,
                                            'ennemy_id' => $ennemy_id,
                                            'game_id' => $game_id
                                        ]);
                                        break;
    
                                    case 'HIT_TWO_ALEATORY':
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $skill_final_damage;
                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = GREATEST(0, hp_left - :new_hp_left)
                                            WHERE player_id = :ennemy_id AND game_id = :game_id AND hp_left > 0
                                            AND active = 1
                                            ORDER BY RAND()
                                            LIMIT 2
                                        ");
                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => $new_hp_left,
                                            'ennemy_id' => $ennemy_id,
                                            'game_id' => $game_id
                                        ]);
                                        break; 
    
                                    case 'HIT_THREE_ALEATORY':
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $skill_final_damage;
                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = GREATEST(0, hp_left - :new_hp_left)
                                            WHERE player_id = :ennemy_id AND game_id = :game_id AND hp_left > 0
                                            AND active = 1
                                            ORDER BY RAND()
                                            LIMIT 3
                                        ");
    
                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => $new_hp_left,
                                            'ennemy_id' => $ennemy_id,
                                            'game_id' => $game_id
                                        ]);
                                        break;
                                    case 'DEFAULT':
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $card_info_ennemy['hp_left'] - $skill_final_damage;
                                    
                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = :new_hp_left 
                                            WHERE id = :card_id AND game_id = :game_id
                                        ");
                                    
                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => max(0, $new_hp_left),  // Limiter les PV à 0 minimum
                                            'card_id' => $card_info_ennemy['id'],  // Utiliser l'ID de l'ennemi
                                            'game_id' => $game_id
                                        ]);
                                        break;
                                    
                                    default:
                                        // // Calcul des nouveaux PV après application de l'effet
                                        // $new_hp_left = $card_info_ennemy['hp_left'] - $skill_final_damage;
                                    
                                        // // Préparer la requête pour mettre à jour les PV
                                        // $stmt_update_hp = $pdo->prepare("
                                        //     UPDATE cards_game 
                                        //     SET hp_left = :new_hp_left 
                                        //     WHERE id = :card_id AND game_id = :game_id
                                        // ");
                                    
                                        // // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        // $stmt_update_hp->execute([
                                        //     'new_hp_left' => max(0, $new_hp_left),  // Limiter les PV à 0 minimum
                                        //     'card_id' => $card_info_ennemy['id'],  // Utiliser l'ID de l'ennemi
                                        //     'game_id' => $game_id
                                        // ]);
    
                                }
                            }
                        }
                        
                        // Vérifier si la colonne 'affect_effect' existe déjà dans la base de données
                        $stmt_card_info = $pdo->prepare("
                        SELECT affect_effect 
                        FROM cards_game 
                        WHERE player_id = :ennemy_id 
                        AND deck_id = :deck_id 
                        AND game_id = :game_id 
                        AND card_id = :card_id
                        ");
                        $stmt_card_info->execute([
                        'ennemy_id' => $ennemy_id,
                        'deck_id' => $cards_use['id'],
                        'game_id' => $game_id,
                        'card_id' => $card_id
                        ]);
    
                        $current_effects_json = $stmt_card_info->fetchColumn(); // Récupérer les effets existants
    
                        // Vérifier si la chaîne est vide ou si le décodage a échoué
                        if ($current_effects_json === false || empty($current_effects_json)) {
                        $current_effects = []; // Initialiser un tableau vide
                        } else {
                        // Essayer de décoder les effets existants
                        $current_effects = json_decode($current_effects_json, true);
    
                        // Si le décodage échoue, initialiser un tableau vide
                        if (!is_array($current_effects)) {
                            $current_effects = [];
                        }
                        }
                        
                        if (!empty($effects_to_add)) {
                            // Fusionner les effets existants avec les nouveaux effets
                            $current_effects = array_merge($current_effects, $effects_to_add);
    
                            // Encoder les effets fusionnés en JSON
                            $card_info['affect_effect'] = json_encode($current_effects);
    
                            // Optionnel : mettre à jour la base de données avec les nouveaux effets
                            $stmt_update = $pdo->prepare("
                            UPDATE cards_game 
                            SET affect_effect = :affect_effect 
                            WHERE player_id = :ennemy_id 
                            AND deck_id = :deck_id 
                            AND game_id = :game_id 
                            AND card_id = :card_id
                            ");
                            $stmt_update->execute([
                            'affect_effect' => $card_info['affect_effect'],
                            'ennemy_id' => $ennemy_id,
                            'deck_id' => $cards_use['id'],
                            'game_id' => $game_id,
                            'card_id' => $card_id
                            ]);
                        }
                    }
                    // Prépare la requête pour récupérer le cooldown du sort
                    $stmt_skill_info_cooldown = $pdo->prepare("SELECT * FROM cards WHERE id = :card_id");
                    $stmt_skill_info_cooldown->execute(['card_id' => $numero_carte]); // Exécute la requête avec le paramètre
                    // Récupère les résultats
                    $skill_info_cooldown = $stmt_skill_info_cooldown->fetch(PDO::FETCH_ASSOC);
                    if($skill_info_cooldown){
                        $skill_cooldown_base = $numero_skill == 1 ? $skill_info_cooldown['skill_1_cooldown'] : $skill_info_cooldown['skill_2_cooldown'];
                        $skill_cooldown_base_req = $numero_skill == 1 ? 'skill_1_cooldown' : 'skill_2_cooldown';
                        $stmt_update_skill_cooldown_base = $pdo->prepare("UPDATE cards_game SET ".$skill_cooldown_base_req." = :skill_cooldown_base WHERE player_id = :player_id AND game_id = :game_id AND card_id = :card_id");
                        $stmt_update_skill_cooldown_base->execute([
                            'skill_cooldown_base' => $skill_cooldown_base,
                            'player_id' => $player_id,
                            'game_id' => $game_id,
                            'card_id' => $numero_carte
                        ]);
                    }
    
                    echo json_encode(['success' => true, 'precision' => $precision, 'crit' => $crit, 'numero_skill' => $numero_skill, 'numero_carte' => $numero_carte, "skill_final_damage" => $skill_final_damage, "skill_info_cooldown" => $skill_info_cooldown, "card_use" => $cards_use['id']]);
                } else {
                    echo json_encode(['success' => false, 'game' => true, 'message' => "Vous ne pouvez pas utiliser ce sort !"]);
                }
                
            } else {
                echo json_encode(['success' => false, 'game' => true, 'message' => "Pas votre tour !"]);
            }
            
        } else {
            echo json_encode(['success' => false, 'game' => false, 'message' => "Game not found"]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la récupération du sort : " . $e->getMessage()]);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'use_skill_ennemie_cards') {
    try {
        $player_id = $_POST['user_id'];
        $game_id = $_POST['game_id'];
        $TabTarget = $_POST['TabTarget'];

        // Récupérer les informations du jeu
        $stmt_game = $pdo->prepare("SELECT * FROM games WHERE id = :game_id AND (player_1_id = :user_id OR player_2_id = :user_id)");
        $stmt_game->execute(['game_id' => $game_id, 'user_id' => $player_id]);
        $game_info = $stmt_game->fetch(PDO::FETCH_ASSOC);
        
        if ($game_info) {
            $turn = $game_info['turn'];
            $turn_data = json_decode($game_info['turn_data']);
            //imaginons turn_data = [1,2]
            $turn_player = $turn % 2 == 0 ? $turn_data[1] : $turn_data[0];
            $ennemy_id = intval($game_info['player_1_id']) !== intval($player_id) ? intval($game_info['player_1_id']) : intval($game_info['player_2_id']);
            if($turn_player == $player_id) {
                $numero_carte = intval($TabTarget[0]);
                $numero_skill = intval($TabTarget[1]);
                $numero_carte_ennemy = preg_match('/\d+$/', $TabTarget[3], $matches);
                $numero_carte_ennemy = intval($matches[0]);

                // Récupérer les informations du jeu
                $stmt_player_playing = $pdo->prepare("SELECT * FROM players WHERE id = :ennemy_id");
                $stmt_player_playing->execute(['ennemy_id' => $ennemy_id]);
                $player_playing = $stmt_player_playing->fetch(PDO::FETCH_ASSOC);
                $deck_use = $player_playing['deck'];


                $stmt_card_use = $pdo->prepare("SELECT * FROM decks WHERE player_id = :ennemy_id AND deck_id = :deck_id");
                $stmt_card_use->execute(['ennemy_id' => $ennemy_id, 'deck_id' => $deck_use]);
                $cards_use = $stmt_card_use->fetch(PDO::FETCH_ASSOC);

                $card_id = $cards_use["card_".$numero_carte_ennemy."_id"];

                $stmt_card_info = $pdo->prepare("SELECT * FROM cards_game WHERE player_id = :ennemy_id AND deck_id = :deck_id AND game_id = :game_id AND card_id = :card_id");
                $stmt_card_info->execute(['ennemy_id' => $ennemy_id, 'deck_id' => $cards_use['id'], 'game_id' => $game_id, 'card_id' => $card_id]);
                $card_info_ennemy = $stmt_card_info->fetch(PDO::FETCH_ASSOC);

                $stmt_players_game_info = $pdo->prepare("SELECT * FROM players_game WHERE player_id = :player_id AND game_id = :game_id");
                $stmt_players_game_info->execute(['player_id' => $player_id, 'game_id' => $game_id]);
                $players_game_info = $stmt_players_game_info->fetch(PDO::FETCH_ASSOC);

                $stmt_ennemy_game_info = $pdo->prepare("SELECT * FROM players_game WHERE player_id = :ennemy_id AND game_id = :game_id");
                $stmt_ennemy_game_info->execute(['ennemy_id' => $card_info_ennemy['player_id'], 'game_id' => $game_id]);
                $ennemy_game_info = $stmt_ennemy_game_info->fetch(PDO::FETCH_ASSOC);

                $stmt_card_info = $pdo->prepare("SELECT * FROM cards_game WHERE player_id = :player_id AND card_id = :card_id AND game_id = :game_id AND card_id = :card_id");
                $stmt_card_info->execute(['player_id' => $player_id, 'card_id' => $numero_carte, 'game_id' => $game_id]);
                $card_info = $stmt_card_info->fetch(PDO::FETCH_ASSOC);

                $skill_use_name = $numero_skill == 1 ? $card_info['skill_1_name'] : $card_info['skill_2_name'];
                $skill_use_active = $numero_skill == 1 ? $card_info['skill_1_active'] : $card_info['skill_2_active'];
                $skill_use_base_value = $numero_skill == 1 ? $card_info['skill_1_base_value'] : $card_info['skill_2_base_value'];
                $skill_use_effect = $numero_skill == 1 ? $card_info['skill_1_effect'] : $card_info['skill_2_effect'];
                $skill_use_type = $numero_skill == 1 ? strtolower($card_info['type']) : strtolower($card_info['type']);
                $skill_use_crit = $numero_skill == 1 ? $card_info['skill_1_crit'] : $card_info['skill_2_crit'];
                $skill_use_crit_dmg = $numero_skill == 1 ? $card_info['skill_1_crit_dmg'] : $card_info['skill_2_crit_dmg'];
                $skill_use_precision = $numero_skill == 1 ? $card_info['skill_1_precision'] : $card_info['skill_2_precision'];
                $skill_use_cooldown = $numero_skill == 1 ? $card_info['skill_1_cooldown'] : $card_info['skill_2_cooldown'];

                $boost_value = $players_game_info["base_" . $skill_use_type];
                $boost_crit = $players_game_info['base_crit'];
                $boost_crit_dmg = $players_game_info['base_crit_dmg'];
                $boost_precision = $players_game_info['base_precision'];
                $precision = false;
                $crit = false;

                $effects = json_decode($skill_use_effect, true);

                //Ma chance de reussir le sort = $skill_use_precision + $boost_precision ca me fait un pourcentage
                $chance_reussite = $skill_use_precision + $boost_precision; // Chance totale de réussite
                // Générer un nombre aléatoire entre 0 et 100
                $random_precision = rand(0, 100);
                // Condition pour déterminer si le sort réussit
                if($skill_use_cooldown == 0){
                    if ($random_precision <= $chance_reussite) {
                        $precision = true;
                        //Ma chance de reussir le sort = $skill_use_precision + $boost_precision ca me fait un pourcentage
                        $chance_crit = $skill_use_crit + $boost_crit; // Chance totale de réussite
                        $random_crit = rand(0, 100);
                        if ($random_crit <= $chance_crit) {
                            $crit = true;
                            // Calcul des dégâts pour un coup critique
                            $skill_final_damage = (($skill_use_base_value + $boost_value)*($skill_use_crit_dmg+$boost_crit_dmg))/100;
                        } else {
                            $crit = false;
                            // Dégâts normaux
                            $skill_final_damage = $skill_use_base_value + $boost_value;
                        }
                    }

                    if($precision == true){ 
                        // Parcourir les effets
                        foreach ($effects as $effect) {
                            if (isset($effect['TYPE'])) {
                                switch ($effect['TYPE']) {
                                    case 'POISON':
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $card_info_ennemy['hp_left'] - $skill_final_damage;

                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = :new_hp_left 
                                            WHERE id = :card_id AND game_id = :game_id
                                        ");

                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => max(0, $new_hp_left),  // Limiter les PV à 0 minimum
                                            'card_id' => $card_info_ennemy['id'],  // Corrigé : $card_info_ennemy['id']
                                            'game_id' => $game_id
                                        ]);
                                        $poison_effect = [
                                            "TYPE" => "POISON",
                                            "VALUE" => $skill_final_damage,
                                            "DURATION" => $effect['DURATION']
                                        ];
                                        $effects_to_add[] = $poison_effect; // Ajouter à la liste des effets à ajouter
                                        break;
        
                                    case 'BLOCK':
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $card_info_ennemy['hp_left'] - $skill_final_damage;

                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = :new_hp_left,
                                                skill_1_active = 0,
                                                skill_2_active = 0
                                            WHERE id = :card_id AND game_id = :game_id
                                        ");

                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => max(0, $new_hp_left),  // Limiter les PV à 0 minimum
                                            'card_id' => $card_info_ennemy['id'],  // Corrigé : $card_info_ennemy['id']
                                            'game_id' => $game_id
                                        ]);
                                        $block_effect = [
                                            "TYPE" => "BLOCK",
                                            "VALUE" => $skill_final_damage,
                                            "DURATION" => $effect['DURATION']+1
                                        ];
                                        $effects_to_add[] = $block_effect; // Ajouter à la liste des effets à ajouter
                                        break;

                                    case 'BLOCK_SKILL_1':
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $card_info_ennemy['hp_left'] - $skill_final_damage;

                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = :new_hp_left,
                                                skill_1_active = 0 
                                            WHERE id = :card_id AND game_id = :game_id
                                        ");

                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => max(0, $new_hp_left),  // Limiter les PV à 0 minimum
                                            'card_id' => $card_info_ennemy['id'],  // Corrigé : $card_info_ennemy['id']
                                            'game_id' => $game_id
                                        ]);
                                        $block_skill_1_effect = [
                                            "TYPE" => "BLOCK_SKILL_1",
                                            "VALUE" => $skill_final_damage,
                                            "DURATION" => $effect['DURATION']+1
                                        ];
                                        $effects_to_add[] = $block_skill_1_effect; // Ajouter à la liste des effets à ajouter
                                        break;
                                    
                                    case 'BLOCK_SKILL_2':
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $card_info_ennemy['hp_left'] - $skill_final_damage;

                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = :new_hp_left,
                                                skill_2_active = 0 
                                            WHERE id = :card_id AND game_id = :game_id
                                        ");

                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => max(0, $new_hp_left),  // Limiter les PV à 0 minimum
                                            'card_id' => $card_info_ennemy['id'],  // Corrigé : $card_info_ennemy['id']
                                            'game_id' => $game_id
                                        ]);
                                        $block_skill_2_effect = [
                                            "TYPE" => "BLOCK_SKILL_2",
                                            "VALUE" => $skill_final_damage,
                                            "DURATION" => $effect['DURATION']+1
                                        ];
                                        $effects_to_add[] = $block_skill_2_effect; // Ajouter à la liste des effets à ajouter
                                        break;
                                    
                                    case 'SLOW':
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $card_info_ennemy['hp_left'] - $skill_final_damage;

                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = :new_hp_left 
                                            WHERE id = :card_id AND game_id = :game_id
                                        ");

                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => max(0, $new_hp_left),  // Limiter les PV à 0 minimum
                                            'card_id' => $card_info_ennemy['id'],  // Corrigé : $card_info_ennemy['id']
                                            'game_id' => $game_id
                                        ]);

                                        $skill_ennemy_slow = rand(1, 2) == 1 ? "skill_1_cooldown" : "skill_2_cooldown";
                                        $new_cooldown = $card_info_ennemy[$skill_ennemy_slow] + 1;

                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_slow = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET ".$skill_ennemy_slow." = :new_cooldown 
                                            WHERE id = :card_id AND game_id = :game_id
                                        ");

                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_slow->execute([
                                            'new_cooldown' => min($new_cooldown, 9),
                                            'card_id' => $card_info_ennemy['id'],  // Corrigé : $card_info_ennemy['id']
                                            'game_id' => $game_id
                                        ]);
                                        break;

                                    case 'BOOST_ELEMENT':
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $card_info_ennemy['hp_left'] - $skill_final_damage;
                                    
                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = :new_hp_left 
                                            WHERE id = :card_id AND game_id = :game_id
                                        ");
                                    
                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => max(0, $new_hp_left),  // Limiter les PV à 0 minimum
                                            'card_id' => $card_info_ennemy['id'],  // Utiliser l'ID de l'ennemi
                                            'game_id' => $game_id
                                        ]);
                                    
                                        // Calcul du boost élémentaire
                                        $boost_value = $players_game_info["base_" . $skill_use_type];  // Récupérer la valeur de base actuelle
                                        $mount_boost = !empty($effect['DURATION']) ? $effect['DURATION'] : 1;  // Utiliser 'DURATION' ou 1 par défaut
                                        $new_boost_value = $boost_value + $mount_boost;  // Appliquer le boost
                                        // Préparer la requête pour mettre à jour le boost de l'élément
                                        $stmt_update_boost_element = $pdo->prepare("
                                            UPDATE players_game 
                                            SET base_" . $skill_use_type ." = :new_boost_value 
                                            WHERE player_id = :player_id AND game_id = :game_id
                                        ");
                                    
                                        // Exécuter la mise à jour du boost
                                        $stmt_update_boost_element->execute([
                                            'new_boost_value' => $new_boost_value,
                                            'player_id' => $player_id,  // Corrigé : utiliser l'ID du joueur
                                            'game_id' => $game_id
                                        ]);
                                        break;

                                    case 'STEAL_ELEMENT':
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $card_info_ennemy['hp_left'] - $skill_final_damage;
                                    
                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = :new_hp_left 
                                            WHERE id = :card_id AND game_id = :game_id
                                        ");
                                    
                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => max(0, $new_hp_left),  // Limiter les PV à 0 minimum
                                            'card_id' => $card_info_ennemy['id'],  // Utiliser l'ID de l'ennemi
                                            'game_id' => $game_id
                                        ]);
                                    
                                        // Calcul du boost élémentaire
                                        $boost_value = $players_game_info["base_" . $skill_use_type];  // Récupérer la valeur de base actuelle
                                        $mount_boost = !empty($effect['DURATION']) ? $effect['DURATION'] : 1;  // Utiliser 'DURATION' ou 1 par défaut
                                        $new_boost_value = $boost_value + $mount_boost;  // Appliquer le boost

                                        $boost_value_ennemy = $ennemy_game_info["base_" . $skill_use_type];
                                        $new_boost_value_ennemy = $boost_value_ennemy - $mount_boost;

                                        // Préparer la requête pour mettre à jour le boost de l'élément
                                        $stmt_update_boost_element = $pdo->prepare("
                                            UPDATE players_game 
                                            SET base_" . $skill_use_type ." = :new_boost_value 
                                            WHERE player_id = :player_id AND game_id = :game_id
                                        ");
                                        // Exécuter la mise à jour du boost
                                        $stmt_update_boost_element->execute([
                                            'new_boost_value' => $new_boost_value,
                                            'player_id' => $player_id,  // Corrigé : utiliser l'ID du joueur
                                            'game_id' => $game_id
                                        ]);

                                        // Préparer la requête pour mettre à jour le boost de l'élément
                                        $stmt_update_steal_element = $pdo->prepare("
                                            UPDATE players_game 
                                            SET base_" . $skill_use_type ." = :new_boost_value_ennemy
                                            WHERE player_id = :ennemy_id AND game_id = :game_id
                                        ");
                                        // Exécuter la mise à jour du boost
                                        $stmt_update_steal_element->execute([
                                            'new_boost_value_ennemy' => max(0, $new_boost_value_ennemy),
                                            'ennemy_id' => $ennemy_id,  // Corrigé : utiliser l'ID du joueur
                                            'game_id' => $game_id
                                        ]);
                                        break;

                                    case 'DEFAULT':
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $card_info_ennemy['hp_left'] - $skill_final_damage;
                                    
                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = :new_hp_left 
                                            WHERE id = :card_id AND game_id = :game_id
                                        ");
                                    
                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => max(0, $new_hp_left),  // Limiter les PV à 0 minimum
                                            'card_id' => $card_info_ennemy['id'],  // Utiliser l'ID de l'ennemi
                                            'game_id' => $game_id
                                        ]);
                                        break;
                                
                                    default:
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $card_info_ennemy['hp_left'] - $skill_final_damage;
                                    
                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = :new_hp_left 
                                            WHERE id = :card_id AND game_id = :game_id
                                        ");
                                    
                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => max(0, $new_hp_left),  // Limiter les PV à 0 minimum
                                            'card_id' => $card_info_ennemy['id'],  // Utiliser l'ID de l'ennemi
                                            'game_id' => $game_id
                                        ]);

                                }
                            }
                        }
                        
                        // Vérifier si la colonne 'affect_effect' existe déjà dans la base de données
                        $stmt_card_info = $pdo->prepare("
                            SELECT affect_effect 
                            FROM cards_game 
                            WHERE player_id = :ennemy_id 
                            AND deck_id = :deck_id 
                            AND game_id = :game_id 
                            AND card_id = :card_id
                            ");
                        $stmt_card_info->execute([
                            'ennemy_id' => $ennemy_id,
                            'deck_id' => $cards_use['id'],
                            'game_id' => $game_id,
                            'card_id' => $card_id
                        ]);

                        $current_effects_json = $stmt_card_info->fetchColumn(); // Récupérer les effets existants

                        // Vérifier si la chaîne est vide ou si le décodage a échoué
                        if ($current_effects_json === false || empty($current_effects_json)) {
                        $current_effects = []; // Initialiser un tableau vide
                        } else {
                            // Essayer de décoder les effets existants
                            $current_effects = json_decode($current_effects_json, true);

                            // Si le décodage échoue, initialiser un tableau vide
                            if (!is_array($current_effects)) {
                                $current_effects = [];
                            }
                        }
                        
                        if (!empty($effects_to_add)) {
                            // Fusionner les effets existants avec les nouveaux effets
                            $current_effects = array_merge($current_effects, $effects_to_add);

                            // Encoder les effets fusionnés en JSON
                            $card_info['affect_effect'] = json_encode($current_effects);

                            // Optionnel : mettre à jour la base de données avec les nouveaux effets
                            $stmt_update = $pdo->prepare("
                            UPDATE cards_game 
                            SET affect_effect = :affect_effect 
                            WHERE player_id = :ennemy_id 
                            AND deck_id = :deck_id 
                            AND game_id = :game_id 
                            AND card_id = :card_id
                            ");
                            $stmt_update->execute([
                            'affect_effect' => $card_info['affect_effect'],
                            'ennemy_id' => $ennemy_id,
                            'deck_id' => $cards_use['id'],
                            'game_id' => $game_id,
                            'card_id' => $card_id
                            ]);
                        }
                    }

                    // Prépare la requête pour récupérer le cooldown du sort
                    $stmt_skill_info_cooldown = $pdo->prepare("SELECT * FROM cards WHERE id = :card_id");
                    $stmt_skill_info_cooldown->execute(['card_id' => $numero_carte]); // Exécute la requête avec le paramètre
                    // Récupère les résultats
                    $skill_info_cooldown = $stmt_skill_info_cooldown->fetch(PDO::FETCH_ASSOC);
                    // Créer un tableau associatif avec les informations
                    $effectFight = [
                        [
                            "TYPE" => "DAMAGE",    // Exemple de type, à adapter selon le contexte
                            "VALUE" => $skill_final_damage,
                            "CRIT" => $crit,       // True ou False
                            "SUCCESS" => $precision, // True ou False
                            "VIEW_PLAYER" => false, // False
                            "VIEW_ENNEMY" => false, // False
                        ]
                    ];
                    $effectFightJSON = json_encode($effectFight);
                    $stmt_update_animation = $pdo->prepare("
                        UPDATE cards_game 
                        SET effect_fight = :effect_fight 
                        WHERE player_id = :ennemy_id 
                        AND deck_id = :deck_id 
                        AND game_id = :game_id 
                        AND card_id = :card_id
                    ");
                    $stmt_update_animation->execute([
                        'effect_fight' => $effectFightJSON,
                        'ennemy_id' => $ennemy_id,
                        'deck_id' => $cards_use['id'],
                        'game_id' => $game_id,
                        'card_id' => $card_id
                    ]);

                    if($skill_info_cooldown){
                        $skill_cooldown_base = $numero_skill == 1 ? $skill_info_cooldown['skill_1_cooldown'] : $skill_info_cooldown['skill_2_cooldown'];
                        $skill_cooldown_base_req = $numero_skill == 1 ? 'skill_1_cooldown' : 'skill_2_cooldown';
                        $stmt_update_skill_cooldown_base = $pdo->prepare("UPDATE cards_game SET ".$skill_cooldown_base_req." = :skill_cooldown_base WHERE player_id = :player_id AND game_id = :game_id AND card_id = :card_id");
                        $stmt_update_skill_cooldown_base->execute([
                            'skill_cooldown_base' => $skill_cooldown_base,
                            'player_id' => $player_id,
                            'game_id' => $game_id,
                            'card_id' => $numero_carte
                        ]);
                    }

                    echo json_encode(['success' => true, 'precision' => $precision, 'crit' => $crit, 'card_id' => $card_info, 'card_info_ennemy' => $card_info_ennemy, 'numero_skill' => $numero_skill, 'numero_carte_ennemy' => $numero_carte_ennemy, "card_id" => $card_id, "skill_final_damage" => $skill_final_damage, "skill_info_cooldown" => $skill_info_cooldown, "card_use" => $cards_use['id'], 'effectFightJSON' => $effectFightJSON]);
                } else {
                    echo json_encode(['success' => false, 'game' => true, 'message' => "Vous ne pouvez pas utiliser ce sort !"]);
                }
            } else {
                echo json_encode(['success' => false, 'game' => true, 'message' => "Pas votre tour !"]);
            }
            
        } else {
            echo json_encode(['success' => false, 'game' => false, 'message' => "Game not found"]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la récupération du sort : " . $e->getMessage()]);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'use_skill_allie') {
    try {
        $player_id = $_POST['user_id'];
        $game_id = $_POST['game_id'];
        $TabTarget = $_POST['TabTarget'];

        // Récupérer les informations du jeu
        $stmt_game = $pdo->prepare("SELECT * FROM games WHERE id = :game_id AND (player_1_id = :user_id OR player_2_id = :user_id)");
        $stmt_game->execute(['game_id' => $game_id, 'user_id' => $player_id]);
        $game_info = $stmt_game->fetch(PDO::FETCH_ASSOC);
        
        if ($game_info) {
            $turn = $game_info['turn'];
            $turn_data = json_decode($game_info['turn_data']);
            $turn_player = $turn % 2 == 0 ? $turn_data[1] : $turn_data[0];
            $ennemy_id = intval($game_info['player_1_id']) !== intval($player_id) ? intval($game_info['player_1_id']) : intval($game_info['player_2_id']);
            if($turn_player == $player_id) {
                $numero_carte = intval($TabTarget[0]);
                $numero_skill = intval($TabTarget[1]);

                $stmt_players_game_info = $pdo->prepare("SELECT * FROM players_game WHERE player_id = :player_id AND game_id = :game_id");
                $stmt_players_game_info->execute(['player_id' => $player_id, 'game_id' => $game_id]);
                $players_game_info = $stmt_players_game_info->fetch(PDO::FETCH_ASSOC);

                $stmt_card_info = $pdo->prepare("SELECT * FROM cards_game WHERE player_id = :player_id AND card_id = :card_id AND game_id = :game_id AND card_id = :card_id");
                $stmt_card_info->execute(['player_id' => $player_id, 'card_id' => $numero_carte, 'game_id' => $game_id]);
                $card_info = $stmt_card_info->fetch(PDO::FETCH_ASSOC);

                $skill_use_name = $numero_skill == 1 ? $card_info['skill_1_name'] : $card_info['skill_2_name'];
                $skill_use_active = $numero_skill == 1 ? $card_info['skill_1_active'] : $card_info['skill_2_active'];
                $skill_use_base_value = $numero_skill == 1 ? $card_info['skill_1_base_value'] : $card_info['skill_2_base_value'];
                $skill_use_cooldown = $numero_skill == 1 ? $card_info['skill_1_cooldown'] : $card_info['skill_2_cooldown'];
                $skill_use_effect = $numero_skill == 1 ? $card_info['skill_1_effect'] : $card_info['skill_2_effect'];
                $skill_use_type = $numero_skill == 1 ? strtolower($card_info['type']) : strtolower($card_info['type']);
                $skill_use_crit = $numero_skill == 1 ? $card_info['skill_1_crit'] : $card_info['skill_2_crit'];
                $skill_use_crit_dmg = $numero_skill == 1 ? $card_info['skill_1_crit_dmg'] : $card_info['skill_2_crit_dmg'];
                $skill_use_precision = $numero_skill == 1 ? $card_info['skill_1_precision'] : $card_info['skill_2_precision'];
                $skill_use_cooldown = $numero_skill == 1 ? $card_info['skill_1_cooldown'] : $card_info['skill_2_cooldown'];

                $boost_value = $players_game_info["base_" . $skill_use_type];
                $boost_crit = $players_game_info['base_crit'];
                $boost_crit_dmg = $players_game_info['base_crit_dmg'];
                $boost_precision = $players_game_info['base_precision'];
                $precision = false;
                $crit = false;

                $effects = json_decode($skill_use_effect, true);

                //Ma chance de reussir le sort = $skill_use_precision + $boost_precision ca me fait un pourcentage
                $chance_reussite = $skill_use_precision + $boost_precision; // Chance totale de réussite
                // Générer un nombre aléatoire entre 0 et 100
                $random_precision = rand(0, 100);
                // Condition pour déterminer si le sort réussit
                if($skill_use_cooldown == 0){
                    if ($random_precision <= $chance_reussite) {
                        $precision = true;
                        //Ma chance de reussir le sort = $skill_use_precision + $boost_precision ca me fait un pourcentage
                        $chance_crit = $skill_use_crit + $boost_crit; // Chance totale de réussite
                        $random_crit = rand(0, 100);
                        if ($random_crit <= $chance_crit) {
                            $crit = true;
                            // Calcul des dégâts pour un coup critique
                            $skill_final_damage = (($skill_use_base_value + $boost_value)*($skill_use_crit_dmg+$boost_crit_dmg))/100;
                        } else {
                            $crit = false;
                            // Dégâts normaux
                            $skill_final_damage = $skill_use_base_value + $boost_value;
                        }
                    }

                    if($precision == true){ 
                        // Parcourir les effets
                        foreach ($effects as $effect) {
                            if (isset($effect['TYPE'])) {
                                switch ($effect['TYPE']) {
                                    case 'AUGMENT_BOOSTS':
                                        $boost_augment = !empty($effect['DURATION']) ? $effect['DURATION'] : 1;  // Utiliser 'DURATION' ou 1 par défaut
                                        $stmt_update_boost_element = $pdo->prepare("
                                            UPDATE players_game 
                                            SET base_fire = GREATEST(0, base_fire + :boost_augment), base_water = GREATEST(0, base_water + :boost_augment), base_earth = GREATEST(0, base_earth + :boost_augment),
                                            base_air = GREATEST(0, base_air + :boost_augment), base_dark = GREATEST(0, base_dark + :boost_augment), base_light = GREATEST(0, base_light + :boost_augment)
                                            WHERE player_id = :player_id AND game_id = :game_id
                                        ");
                                    
                                        // Exécuter la mise à jour du boost
                                        $stmt_update_boost_element->execute([
                                            'boost_augment' => max(0, $boost_augment),
                                            'player_id' => $player_id,  // Corrigé : utiliser l'ID du joueur
                                            'game_id' => $game_id
                                        ]);
                                        break;

                                    case 'AUGMENT_BOOST_ALEATORY':
                                        $boost_augment = !empty($effect['DURATION']) ? $effect['DURATION'] : 1;  // Utiliser 'DURATION' ou 1 par défaut
                                        // Tableau avec les éléments
                                        $elements = [
                                            'base_fire',
                                            'base_water',
                                            'base_earth',
                                            'base_air',
                                            'base_dark',
                                            'base_light'
                                        ];

                                        // Tirer un élément aléatoire
                                        $random_element = $elements[array_rand($elements)];  // Récupère un élément aléatoire du tableau

                                        $stmt_update_boost_element = $pdo->prepare("
                                            UPDATE players_game 
                                            SET ".$random_element." = GREATEST(0, ".$random_element." + :boost_augment)
                                            WHERE player_id = :player_id AND game_id = :game_id
                                        ");
                                    
                                        // Exécuter la mise à jour du boost
                                        $stmt_update_boost_element->execute([
                                            'boost_augment' => max(0, $boost_augment),
                                            'player_id' => $player_id,  // Corrigé : utiliser l'ID du joueur
                                            'game_id' => $game_id
                                        ]);
                                        break;

                                    case 'AUGMENT_TWO_BOOSTS_ALEATORY':
                                        $boost_augment = !empty($effect['DURATION']) ? $effect['DURATION'] : 1;  // Utiliser 'DURATION' ou 1 par défaut
                                        // Tableau avec les éléments
                                        $elements = [
                                            'base_fire',
                                            'base_water',
                                            'base_earth',
                                            'base_air',
                                            'base_dark',
                                            'base_light'
                                        ];
                                        
                                        // Tirer un premier élément aléatoire
                                        $random_key = array_rand($elements);  // Récupère la clé aléatoire
                                        $random_element = $elements[$random_key];  // Stocke l'élément
                                        unset($elements[$random_key]);  // Supprime cet élément du tableau
                                        
                                        // Tirer un deuxième élément aléatoire
                                        $random_key_2 = array_rand($elements);  // Récupère une nouvelle clé aléatoire
                                        $random_element_2 = $elements[$random_key_2];  // Stocke le deuxième élément

                                        $stmt_update_boost_element = $pdo->prepare("
                                            UPDATE players_game 
                                            SET ".$random_element." = GREATEST(0, ".$random_element." + :boost_augment),
                                                ".$random_element_2." = GREATEST(0, ".$random_element_2." + :boost_augment)
                                            WHERE player_id = :player_id AND game_id = :game_id
                                        ");
                                    
                                        // Exécuter la mise à jour du boost
                                        $stmt_update_boost_element->execute([
                                            'boost_augment' => max(0, $boost_augment),
                                            'player_id' => $player_id,  // Corrigé : utiliser l'ID du joueur
                                            'game_id' => $game_id
                                        ]);
                                        break;
    
                                    case 'AUGMENT_THREE_BOOSTS_ALEATORY':
                                        $boost_augment = !empty($effect['DURATION']) ? $effect['DURATION'] : 1;  // Utiliser 'DURATION' ou 1 par défaut
                                        // Tableau avec les éléments
                                        $elements = [
                                            'base_fire',
                                            'base_water',
                                            'base_earth',
                                            'base_air',
                                            'base_dark',
                                            'base_light'
                                        ];
                                        
                                        $random_key = array_rand($elements);  // Récupère la clé aléatoire
                                        $random_element = $elements[$random_key];  // Stocke l'élément
                                        unset($elements[$random_key]);  // Supprime cet élément du tableau
                                        
                                        $random_key_2 = array_rand($elements);  // Récupère la clé aléatoire
                                        $random_element_2 = $elements[$random_key_2];  // Stocke l'élément
                                        unset($elements[$random_key_2]);  // Supprime cet élément du tableau

                                        $random_key_3 = array_rand($elements);  // Récupère la clé aléatoire
                                        $random_element_3 = $elements[$random_key_3];  // Stocke l'élément
                                        unset($elements[$random_key_3]);  // Supprime cet élément du tableau

                                        $stmt_update_boost_element = $pdo->prepare("
                                            UPDATE players_game 
                                            SET ".$random_element." = GREATEST(0, ".$random_element." + :boost_augment),
                                                ".$random_element_2." = GREATEST(0, ".$random_element_2." + :boost_augment),
                                                ".$random_element_3." = GREATEST(0, ".$random_element_3." + :boost_augment)
                                            WHERE player_id = :player_id AND game_id = :game_id
                                        ");
                                    
                                        // Exécuter la mise à jour du boost
                                        $stmt_update_boost_element->execute([
                                            'boost_augment' => max(0, $boost_augment),
                                            'player_id' => $player_id,  // Corrigé : utiliser l'ID du joueur
                                            'game_id' => $game_id
                                        ]);
                                        break;
        
                                    case 'AUGMENT_FOUR_BOOSTS_ALEATORY':
                                        $boost_augment = !empty($effect['DURATION']) ? $effect['DURATION'] : 1;  // Utiliser 'DURATION' ou 1 par défaut
                                        // Tableau avec les éléments
                                        $elements = [
                                            'base_fire',
                                            'base_water',
                                            'base_earth',
                                            'base_air',
                                            'base_dark',
                                            'base_light'
                                        ];

                                        $random_key = array_rand($elements);  // Récupère la clé aléatoire
                                        $random_element = $elements[$random_key];  // Stocke l'élément
                                        unset($elements[$random_key]);  // Supprime cet élément du tableau
                                        
                                        $random_key_2 = array_rand($elements);  // Récupère la clé aléatoire
                                        $random_element_2 = $elements[$random_key_2];  // Stocke l'élément
                                        unset($elements[$random_key_2]);  // Supprime cet élément du tableau

                                        $random_key_3 = array_rand($elements);  // Récupère la clé aléatoire
                                        $random_element_3 = $elements[$random_key_3];  // Stocke l'élément
                                        unset($elements[$random_key_3]);  // Supprime cet élément du tableau

                                        $random_key_4 = array_rand($elements);  // Récupère la clé aléatoire
                                        $random_element_4 = $elements[$random_key_4];  // Stocke l'élément
                                        unset($elements[$random_key_4]);  // Supprime cet élément du tableau

                                        $stmt_update_boost_element = $pdo->prepare("
                                            UPDATE players_game 
                                            SET ".$random_element." = GREATEST(0, ".$random_element." + :boost_augment),
                                                ".$random_element_2." = GREATEST(0, ".$random_element_2." + :boost_augment),
                                                ".$random_element_3." = GREATEST(0, ".$random_element_3." + :boost_augment),
                                                ".$random_element_4." = GREATEST(0, ".$random_element_4." + :boost_augment)
                                            WHERE player_id = :player_id AND game_id = :game_id
                                        ");
                                    
                                        // Exécuter la mise à jour du boost
                                        $stmt_update_boost_element->execute([
                                            'boost_augment' => max(0, $boost_augment),
                                            'player_id' => $player_id,  // Corrigé : utiliser l'ID du joueur
                                            'game_id' => $game_id
                                        ]);
                                        break;
        
                                    case 'TRADE_BOOST':
                                        $boost_element = $boost_value;
                                        $new_boost_element = ($boost_element * 2);

                                        // Tableau avec les éléments
                                        $elements = [
                                            'base_fire',
                                            'base_water',
                                            'base_earth',
                                            'base_air',
                                            'base_dark',
                                            'base_light'
                                        ];

                                        // Tirer un élément aléatoire
                                        $random_element = $elements[array_rand($elements)];  // Récupère un élément aléatoire du tableau
                                        
                                        $stmt_update_use_element = $pdo->prepare("
                                            UPDATE players_game 
                                            SET base_" . $skill_use_type." = 0
                                            WHERE player_id = :player_id AND game_id = :game_id
                                        ");

                                        $stmt_update_boost_element = $pdo->prepare("
                                            UPDATE players_game 
                                            SET ".$random_element." = GREATEST(0, ".$random_element." + :new_boost_element)
                                            WHERE player_id = :player_id AND game_id = :game_id
                                        ");

                                        // Exécuter la mise à jour du boost
                                        $stmt_update_use_element->execute([
                                            'player_id' => $player_id,
                                            'game_id' => $game_id
                                        ]);
                                    
                                        // Exécuter la mise à jour du boost
                                        $stmt_update_boost_element->execute([
                                            'new_boost_element' => max(0, $new_boost_element),
                                            'player_id' => $player_id,
                                            'game_id' => $game_id
                                        ]);
                                        break;

                                    case 'TRADE_BOOST_FIRE':
                                        $boost_augment = !empty($effect['DURATION']) ? $effect['DURATION'] : 1; //Taux de boost
                                        $boost_price = !empty($effect['PRICE']) ? $effect['PRICE'] : 1; //Prix de boost

                                        $boost_element = $boost_value; //Boost de base
                                        $new_boost_element = ($boost_element + $boost_augment); //Boost apres l'achat

                                        $element = str_replace('trade_boost', 'base',strtolower($effect['TYPE'])); //Monnaie d'achat
                                        $element_count = $players_game_info[$element]; //Nombre de Monnaie

                                        if($element_count >= $boost_price){ //Si j'ai assez de monnaie
                                            $new_element_count = $element_count - $boost_price;
                                            $stmt_update_boost_element = $pdo->prepare("
                                                UPDATE players_game 
                                                SET base_" . $skill_use_type." = :new_boost_element,
                                                    ".$element." = :new_element_count
                                                WHERE player_id = :player_id AND game_id = :game_id
                                            ");
                                            // Exécuter la mise à jour du boost
                                            $stmt_update_boost_element->execute([
                                                'new_boost_element' => $new_boost_element,
                                                'new_element_count' => max(0, $new_element_count),
                                                'player_id' => $player_id,
                                                'game_id' => $game_id
                                            ]);
                                        }
                                        break;
                                    case 'TRADE_BOOST_WATER':
                                        $boost_augment = !empty($effect['DURATION']) ? $effect['DURATION'] : 1; //Taux de boost
                                        $boost_price = !empty($effect['PRICE']) ? $effect['PRICE'] : 1; //Prix de boost

                                        $boost_element = $boost_value; //Boost de base
                                        $new_boost_element = ($boost_element + $boost_augment); //Boost apres l'achat

                                        $element = str_replace('trade_boost', 'base',strtolower($effect['TYPE'])); //Monnaie d'achat
                                        $element_count = $players_game_info[$element]; //Nombre de Monnaie

                                        if($element_count >= $boost_price){ //Si j'ai assez de monnaie
                                            $new_element_count = $element_count - $boost_price;
                                            $stmt_update_boost_element = $pdo->prepare("
                                                UPDATE players_game 
                                                SET base_" . $skill_use_type." = :new_boost_element,
                                                    ".$element." = :new_element_count
                                                WHERE player_id = :player_id AND game_id = :game_id
                                            ");
                                            // Exécuter la mise à jour du boost
                                            $stmt_update_boost_element->execute([
                                                'new_boost_element' => $new_boost_element,
                                                'new_element_count' => max(0, $new_element_count),
                                                'player_id' => $player_id,
                                                'game_id' => $game_id
                                            ]);
                                        }
                                        break;
                                    case 'TRADE_BOOST_EARTH':
                                        $boost_augment = !empty($effect['DURATION']) ? $effect['DURATION'] : 1; //Taux de boost
                                        $boost_price = !empty($effect['PRICE']) ? $effect['PRICE'] : 1; //Prix de boost

                                        $boost_element = $boost_value; //Boost de base
                                        $new_boost_element = ($boost_element + $boost_augment); //Boost apres l'achat

                                        $element = str_replace('trade_boost', 'base',strtolower($effect['TYPE'])); //Monnaie d'achat
                                        $element_count = $players_game_info[$element]; //Nombre de Monnaie

                                        if($element_count >= $boost_price){ //Si j'ai assez de monnaie
                                            $new_element_count = $element_count - $boost_price;
                                            $stmt_update_boost_element = $pdo->prepare("
                                                UPDATE players_game 
                                                SET base_" . $skill_use_type." = :new_boost_element,
                                                    ".$element." = :new_element_count
                                                WHERE player_id = :player_id AND game_id = :game_id
                                            ");
                                            // Exécuter la mise à jour du boost
                                            $stmt_update_boost_element->execute([
                                                'new_boost_element' => $new_boost_element,
                                                'new_element_count' => max(0, $new_element_count),
                                                'player_id' => $player_id,
                                                'game_id' => $game_id
                                            ]);
                                        }
                                        break;
                                    case 'TRADE_BOOST_AIR':
                                        $boost_augment = !empty($effect['DURATION']) ? $effect['DURATION'] : 1; //Taux de boost
                                        $boost_price = !empty($effect['PRICE']) ? $effect['PRICE'] : 1; //Prix de boost

                                        $boost_element = $boost_value; //Boost de base
                                        $new_boost_element = ($boost_element + $boost_augment); //Boost apres l'achat

                                        $element = str_replace('trade_boost', 'base',strtolower($effect['TYPE'])); //Monnaie d'achat
                                        $element_count = $players_game_info[$element]; //Nombre de Monnaie

                                        if($element_count >= $boost_price){ //Si j'ai assez de monnaie
                                            $new_element_count = $element_count - $boost_price;
                                            $stmt_update_boost_element = $pdo->prepare("
                                                UPDATE players_game 
                                                SET base_" . $skill_use_type." = :new_boost_element,
                                                    ".$element." = :new_element_count
                                                WHERE player_id = :player_id AND game_id = :game_id
                                            ");
                                            // Exécuter la mise à jour du boost
                                            $stmt_update_boost_element->execute([
                                                'new_boost_element' => $new_boost_element,
                                                'new_element_count' => max(0, $new_element_count),
                                                'player_id' => $player_id,
                                                'game_id' => $game_id
                                            ]);
                                        }
                                        break;
                                    case 'TRADE_BOOST_DARK':
                                        $boost_augment = !empty($effect['DURATION']) ? $effect['DURATION'] : 1; //Taux de boost
                                        $boost_price = !empty($effect['PRICE']) ? $effect['PRICE'] : 1; //Prix de boost

                                        $boost_element = $boost_value; //Boost de base
                                        $new_boost_element = ($boost_element + $boost_augment); //Boost apres l'achat

                                        $element = str_replace('trade_boost', 'base',strtolower($effect['TYPE'])); //Monnaie d'achat
                                        $element_count = $players_game_info[$element]; //Nombre de Monnaie

                                        if($element_count >= $boost_price){ //Si j'ai assez de monnaie
                                            $new_element_count = $element_count - $boost_price;
                                            $stmt_update_boost_element = $pdo->prepare("
                                                UPDATE players_game 
                                                SET base_" . $skill_use_type." = :new_boost_element,
                                                    ".$element." = :new_element_count
                                                WHERE player_id = :player_id AND game_id = :game_id
                                            ");
                                            // Exécuter la mise à jour du boost
                                            $stmt_update_boost_element->execute([
                                                'new_boost_element' => $new_boost_element,
                                                'new_element_count' => max(0, $new_element_count),
                                                'player_id' => $player_id,
                                                'game_id' => $game_id
                                            ]);
                                        }
                                        break;
                                    case 'TRADE_BOOST_LIGHT':
                                        $boost_augment = !empty($effect['DURATION']) ? $effect['DURATION'] : 1; //Taux de boost
                                        $boost_price = !empty($effect['PRICE']) ? $effect['PRICE'] : 1; //Prix de boost

                                        $boost_element = $boost_value; //Boost de base
                                        $new_boost_element = ($boost_element + $boost_augment); //Boost apres l'achat

                                        $element = str_replace('trade_boost', 'base',strtolower($effect['TYPE'])); //Monnaie d'achat
                                        $element_count = $players_game_info[$element]; //Nombre de Monnaie

                                        if($element_count >= $boost_price){ //Si j'ai assez de monnaie
                                            $new_element_count = $element_count - $boost_price;
                                            $stmt_update_boost_element = $pdo->prepare("
                                                UPDATE players_game 
                                                SET base_" . $skill_use_type." = :new_boost_element,
                                                    ".$element." = :new_element_count
                                                WHERE player_id = :player_id AND game_id = :game_id
                                            ");
                                            // Exécuter la mise à jour du boost
                                            $stmt_update_boost_element->execute([
                                                'new_boost_element' => $new_boost_element,
                                                'new_element_count' => max(0, $new_element_count),
                                                'player_id' => $player_id,
                                                'game_id' => $game_id
                                            ]);
                                        }
                                        break;
                                    case 'HEAL_ALL':
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $skill_final_damage;
                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = LEAST(hp_left + :new_hp_left, hp_max)
                                            WHERE player_id = :player_id AND game_id = :game_id AND hp_left > 0 AND active = 1
                                        ");

                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => $new_hp_left,
                                            'player_id' => $player_id,
                                            'game_id' => $game_id
                                        ]);
                                        break;
                                    case 'HEAL_ALEATORY':
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $skill_final_damage;
                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = LEAST(hp_left + :new_hp_left, hp_max)
                                            WHERE player_id = :player_id AND game_id = :game_id AND hp_left > 0
                                            AND hp_left < hp_max AND active = 1
                                            ORDER BY RAND()
                                            LIMIT 1
                                        ");

                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => $new_hp_left,
                                            'player_id' => $player_id,
                                            'game_id' => $game_id
                                        ]);
                                        break;

                                    case 'HEAL_TWO_ALEATORY':
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $skill_final_damage;
                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = LEAST(hp_left + :new_hp_left, hp_max)
                                            WHERE player_id = :player_id AND game_id = :game_id AND hp_left > 0
                                            AND hp_left < hp_max AND active = 1
                                            ORDER BY RAND()
                                            LIMIT 2
                                        ");

                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => $new_hp_left,
                                            'player_id' => $player_id,
                                            'game_id' => $game_id
                                        ]);
                                        break; 

                                    case 'HEAL_THREE_ALEATORY':
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $skill_final_damage;
                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = LEAST(hp_left + :new_hp_left, hp_max)
                                            WHERE player_id = :player_id AND game_id = :game_id AND hp_left > 0
                                            AND hp_left < hp_max AND active = 1
                                            ORDER BY RAND()
                                            LIMIT 3
                                        ");

                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => $new_hp_left,
                                            'player_id' => $player_id,
                                            'game_id' => $game_id
                                        ]);
                                        break; 
                                        
                                    case 'DEFAULT':
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $card_info_ennemy['hp_left'] - $skill_final_damage;
                                    
                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = :new_hp_left 
                                            WHERE id = :card_id AND game_id = :game_id
                                        ");
                                    
                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => max(0, $new_hp_left),  // Limiter les PV à 0 minimum
                                            'card_id' => $card_info_ennemy['id'],  // Utiliser l'ID de l'ennemi
                                            'game_id' => $game_id
                                        ]);
                                        break;
                                    
                                    
                                    default:
                                        // // Calcul des nouveaux PV après application de l'effet
                                        // $new_hp_left = $card_info_ennemy['hp_left'] - $skill_final_damage;
                                    
                                        // // Préparer la requête pour mettre à jour les PV
                                        // $stmt_update_hp = $pdo->prepare("
                                        //     UPDATE cards_game 
                                        //     SET hp_left = :new_hp_left 
                                        //     WHERE id = :card_id AND game_id = :game_id
                                        // ");
                                    
                                        // // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        // $stmt_update_hp->execute([
                                        //     'new_hp_left' => max(0, $new_hp_left),  // Limiter les PV à 0 minimum
                                        //     'card_id' => $card_info_ennemy['id'],  // Utiliser l'ID de l'ennemi
                                        //     'game_id' => $game_id
                                        // ]);
                                }
                            }
                        }
                    }
                    // Prépare la requête pour récupérer le cooldown du sort
                    $stmt_skill_info_cooldown = $pdo->prepare("SELECT * FROM cards WHERE id = :card_id");
                    $stmt_skill_info_cooldown->execute(['card_id' => $numero_carte]); // Exécute la requête avec le paramètre
                    // Récupère les résultats
                    $skill_info_cooldown = $stmt_skill_info_cooldown->fetch(PDO::FETCH_ASSOC);
                    if($skill_info_cooldown){
                        $skill_cooldown_base = $numero_skill == 1 ? $skill_info_cooldown['skill_1_cooldown'] : $skill_info_cooldown['skill_2_cooldown'];
                        $skill_cooldown_base_req = $numero_skill == 1 ? 'skill_1_cooldown' : 'skill_2_cooldown';
                        $stmt_update_skill_cooldown_base = $pdo->prepare("UPDATE cards_game SET ".$skill_cooldown_base_req." = :skill_cooldown_base WHERE player_id = :player_id AND game_id = :game_id AND card_id = :card_id");
                        $stmt_update_skill_cooldown_base->execute([
                            'skill_cooldown_base' => $skill_cooldown_base,
                            'player_id' => $player_id,
                            'game_id' => $game_id,
                            'card_id' => $numero_carte
                        ]);
                    }

                    echo json_encode(['success' => true, 'precision' => $precision, 'crit' => $crit, 'card_id' => $card_info, 'numero_skill' => $numero_skill, "skill_final_damage" => $skill_final_damage, "skill_info_cooldown" => $skill_info_cooldown]);
                } else {
                    echo json_encode(['success' => false, 'game' => true, 'message' => "Vous ne pouvez pas utiliser ce sort !"]);
                }
            } else {
                echo json_encode(['success' => false, 'game' => true, 'message' => "Pas votre tour !"]);
            }
            
        } else {
            echo json_encode(['success' => false, 'game' => false, 'message' => "Game not found"]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la récupération du sort : " . $e->getMessage()]);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'use_skill_allie_cards') {
    try {
        $player_id = $_POST['user_id'];
        $game_id = $_POST['game_id'];
        $TabTarget = $_POST['TabTarget'];

        // Récupérer les informations du jeu
        $stmt_game = $pdo->prepare("SELECT * FROM games WHERE id = :game_id AND (player_1_id = :user_id OR player_2_id = :user_id)");
        $stmt_game->execute(['game_id' => $game_id, 'user_id' => $player_id]);
        $game_info = $stmt_game->fetch(PDO::FETCH_ASSOC);
        
        if ($game_info) {
            $turn = $game_info['turn'];
            $turn_data = json_decode($game_info['turn_data']);
            $turn_player = $turn % 2 == 0 ? $turn_data[1] : $turn_data[0];
            if($turn_player == $player_id) {
                $card_id = intval($TabTarget[0]);
                $numero_skill = intval($TabTarget[1]);
                $numero_carte_target = preg_match('/\d+$/', $TabTarget[3], $matches);
                $numero_carte_target = intval($matches[0]);

                // Récupérer les informations du jeu
                $stmt_player_playing = $pdo->prepare("SELECT * FROM players WHERE id = :player_id");
                $stmt_player_playing->execute(['player_id' => $player_id]);
                $player_playing = $stmt_player_playing->fetch(PDO::FETCH_ASSOC);
                $deck_use = $player_playing['deck'];

                $stmt_card_use = $pdo->prepare("SELECT * FROM decks WHERE player_id = :player_id AND deck_id = :deck_id");
                $stmt_card_use->execute(['player_id' => $player_id, 'deck_id' => $deck_use]);
                $cards_use = $stmt_card_use->fetch(PDO::FETCH_ASSOC);
                $card_target_id = $cards_use["card_".$numero_carte_target."_id"];
                

                $stmt_card_info = $pdo->prepare("SELECT * FROM cards_game WHERE player_id = :player_id AND deck_id = :deck_id AND game_id = :game_id AND card_id = :card_id");
                $stmt_card_info->execute(['player_id' => $player_id, 'deck_id' => $cards_use['id'], 'game_id' => $game_id, 'card_id' => $card_id]);
                $card_info_player = $stmt_card_info->fetch(PDO::FETCH_ASSOC);

                $stmt_card_info_target = $pdo->prepare("SELECT * FROM cards_game WHERE player_id = :player_id AND deck_id = :deck_id AND game_id = :game_id AND card_id = :card_id");
                $stmt_card_info_target->execute(['player_id' => $player_id, 'deck_id' => $cards_use['id'], 'game_id' => $game_id, 'card_id' => $card_target_id]);
                $card_info_target = $stmt_card_info_target->fetch(PDO::FETCH_ASSOC);

                $stmt_players_game_info = $pdo->prepare("SELECT * FROM players_game WHERE player_id = :player_id AND game_id = :game_id");
                $stmt_players_game_info->execute(['player_id' => $player_id, 'game_id' => $game_id]);
                $players_game_info = $stmt_players_game_info->fetch(PDO::FETCH_ASSOC);

                $skill_use_name = $numero_skill == 1 ? $card_info_player['skill_1_name'] : $card_info_player['skill_2_name'];
                $skill_use_active = $numero_skill == 1 ? $card_info_player['skill_1_active'] : $card_info_player['skill_2_active'];
                $skill_use_base_value = $numero_skill == 1 ? $card_info_player['skill_1_base_value'] : $card_info_player['skill_2_base_value'];
                $skill_use_cooldown = $numero_skill == 1 ? $card_info_player['skill_1_cooldown'] : $card_info_player['skill_2_cooldown'];
                $skill_use_effect = $numero_skill == 1 ? $card_info_player['skill_1_effect'] : $card_info_player['skill_2_effect'];
                $skill_use_type = $numero_skill == 1 ? strtolower($card_info_player['type']) : strtolower($card_info_player['type']);
                $skill_use_crit = $numero_skill == 1 ? $card_info_player['skill_1_crit'] : $card_info_player['skill_2_crit'];
                $skill_use_crit_dmg = $numero_skill == 1 ? $card_info_player['skill_1_crit_dmg'] : $card_info_player['skill_2_crit_dmg'];
                $skill_use_precision = $numero_skill == 1 ? $card_info_player['skill_1_precision'] : $card_info_player['skill_2_precision'];
                $skill_use_cooldown = $numero_skill == 1 ? $card_info['skill_1_cooldown'] : $card_info['skill_2_cooldown'];

                $boost_value = $players_game_info["base_" . $skill_use_type];
                $boost_crit = $players_game_info['base_crit'];
                $boost_crit_dmg = $players_game_info['base_crit_dmg'];
                $boost_precision = $players_game_info['base_precision'];
                $precision = false;
                $crit = false;

                $effects = json_decode($skill_use_effect, true);

                //Ma chance de reussir le sort = $skill_use_precision + $boost_precision ca me fait un pourcentage
                $chance_reussite = $skill_use_precision + $boost_precision; // Chance totale de réussite
                // Générer un nombre aléatoire entre 0 et 100
                $random_precision = rand(0, 100);
                if($skill_use_cooldown == 0){
                    // Condition pour déterminer si le sort réussit
                    if ($random_precision <= $chance_reussite) {
                        $precision = true;
                        //Ma chance de reussir le sort = $skill_use_precision + $boost_precision ca me fait un pourcentage
                        $chance_crit = $skill_use_crit + $boost_crit; // Chance totale de réussite
                        $random_crit = rand(0, 100);
                        if ($random_crit <= $chance_crit) {
                            $crit = true;
                            // Calcul des dégâts pour un coup critique
                            $skill_final_damage = (($skill_use_base_value + $boost_value)*($skill_use_crit_dmg+$boost_crit_dmg))/100;
                        } else {
                            $crit = false;
                            // Dégâts normaux
                            $skill_final_damage = $skill_use_base_value + $boost_value;
                        }
                    }

                    if($precision == true){ 
                        // Parcourir les effets
                        foreach ($effects as $effect) {
                            if (isset($effect['TYPE'])) {
                                switch ($effect['TYPE']) {
                                    case 'HEAL':
                                        // Calcul des nouveaux PV après application de l'effet
                                        $new_hp_left = $card_info_target['hp_left'] + $skill_final_damage;
                                        // Préparer la requête pour mettre à jour les PV
                                        $stmt_update_hp = $pdo->prepare("
                                            UPDATE cards_game 
                                            SET hp_left = :new_hp_left 
                                            WHERE card_id = :card_id AND game_id = :game_id
                                        ");

                                        // Exécuter la mise à jour des PV en s'assurant que les PV ne tombent pas sous 0
                                        $stmt_update_hp->execute([
                                            'new_hp_left' => min($new_hp_left, $card_info_target['hp_max']),
                                            'card_id' => $card_info_target['card_id'],
                                            'game_id' => $game_id
                                        ]);
                                        break;
                                    case 'BOOST_CRIT':
                                        $skill_target_crit = rand(1, 2) == 1 ? "skill_1_crit" : "skill_2_crit";
                                        $new_crit = $card_info_target[$skill_target_crit] + 10;
                                        
                                        // Créer la requête SQL en concaténant la colonne dynamiquement
                                        $sql = "
                                            UPDATE cards_game 
                                            SET $skill_target_crit = :new_crit 
                                            WHERE card_id = :card_id AND game_id = :game_id
                                        ";
                                        
                                        // Préparer la requête
                                        $stmt_update_crit = $pdo->prepare($sql);
                                        
                                        // Exécuter la requête avec les valeurs appropriées
                                        $stmt_update_crit->execute([
                                            'new_crit' => min($new_crit, 100), // Limiter le crit à 100
                                            'card_id' => $card_info_target['card_id'],
                                            'game_id' => $game_id
                                        ]);
                                        break;

                                    case 'BOOST_PRECISION':
                                        $skill_target_precision = rand(1, 2) == 1 ? "skill_1_precision" : "skill_2_precision";
                                        $new_precision = $card_info_target[$skill_target_precision] + 10;
                                        
                                        // Créer la requête SQL en concaténant la colonne dynamiquement
                                        $sql = "
                                            UPDATE cards_game 
                                            SET $skill_target_precision = :new_precision 
                                            WHERE card_id = :card_id AND game_id = :game_id
                                        ";
                                        
                                        // Préparer la requête
                                        $stmt_update_precision = $pdo->prepare($sql);
                                        
                                        // Exécuter la requête avec les valeurs appropriées
                                        $stmt_update_precision->execute([
                                            'new_precision' => min($new_precision, 100), // Limiter le crit à 100
                                            'card_id' => $card_info_target['card_id'],
                                            'game_id' => $game_id
                                        ]);
                                        break;

                                    default:
                                        break;
                                }
                            }
                        }
                        
                        // Vérifier si la colonne 'affect_effect' existe déjà dans la base de données
                        $stmt_card_info = $pdo->prepare("
                        SELECT affect_effect 
                        FROM cards_game 
                        WHERE player_id = :player_id 
                        AND deck_id = :deck_id 
                        AND game_id = :game_id 
                        AND card_id = :card_id
                        ");
                        $stmt_card_info->execute([
                        'player_id' => $player_id,
                        'deck_id' => $cards_use['id'],
                        'game_id' => $game_id,
                        'card_id' => $card_id
                        ]);

                        $current_effects_json = $stmt_card_info->fetchColumn(); // Récupérer les effets existants

                        // Vérifier si la chaîne est vide ou si le décodage a échoué
                        if ($current_effects_json === false || empty($current_effects_json)) {
                        $current_effects = []; // Initialiser un tableau vide
                        } else {
                        // Essayer de décoder les effets existants
                        $current_effects = json_decode($current_effects_json, true);

                        // Si le décodage échoue, initialiser un tableau vide
                        if (!is_array($current_effects)) {
                            $current_effects = [];
                        }
                        }
                        
                        if (!empty($effects_to_add)) {
                            // Fusionner les effets existants avec les nouveaux effets
                            $current_effects = array_merge($current_effects, $effects_to_add);

                            // Encoder les effets fusionnés en JSON
                            $card_info['affect_effect'] = json_encode($current_effects);

                            // Optionnel : mettre à jour la base de données avec les nouveaux effets
                            $stmt_update = $pdo->prepare("
                            UPDATE cards_game 
                            SET affect_effect = :affect_effect 
                            WHERE player_id = :player_id 
                            AND deck_id = :deck_id 
                            AND game_id = :game_id 
                            AND card_id = :card_id
                            ");
                            $stmt_update->execute([
                            'affect_effect' => $card_info['affect_effect'],
                            'player_id' => $player_id,
                            'deck_id' => $cards_use['id'],
                            'game_id' => $game_id,
                            'card_id' => $card_id
                            ]);
                        }
                    }
                    // Prépare la requête pour récupérer le cooldown du sort
                    $stmt_skill_info_cooldown = $pdo->prepare("SELECT * FROM cards WHERE id = :card_id");
                    $stmt_skill_info_cooldown->execute(['card_id' => $card_id]); // Exécute la requête avec le paramètre
                    // Récupère les résultats
                    $skill_info_cooldown = $stmt_skill_info_cooldown->fetch(PDO::FETCH_ASSOC);
                    if($skill_info_cooldown){
                        $skill_cooldown_base = $numero_skill == 1 ? $skill_info_cooldown['skill_1_cooldown'] : $skill_info_cooldown['skill_2_cooldown'];
                        $skill_cooldown_base_req = $numero_skill == 1 ? 'skill_1_cooldown' : 'skill_2_cooldown';
                        $stmt_update_skill_cooldown_base = $pdo->prepare("UPDATE cards_game SET ".$skill_cooldown_base_req." = :skill_cooldown_base WHERE player_id = :player_id AND game_id = :game_id AND card_id = :card_id");
                        $stmt_update_skill_cooldown_base->execute([
                            'skill_cooldown_base' => $skill_cooldown_base,
                            'player_id' => $player_id,
                            'game_id' => $game_id,
                            'card_id' => $card_id
                        ]);
                    }
                    echo json_encode(['success' => true, 'precision' => $precision, 'crit' => $crit, 'card_id' => $card_info, 'card_info_player' => $card_info_player, 'numero_skill' => $numero_skill, 'numero_carte_target' => $numero_carte_target, "card_id" => $card_id, "skill_final_damage" => $skill_final_damage, "skill_info_cooldown" => $skill_info_cooldown, "new_crit" => $new_crit]); 
                } else {
                    echo json_encode(['success' => false, 'game' => true, 'message' => "Vous ne pouvez pas utiliser ce sort !"]);
                }
            } else {
                echo json_encode(['success' => false, 'game' => true, 'message' => "Pas votre tour !"]);
            }
            
        } else {
            echo json_encode(['success' => false, 'game' => false, 'message' => "Game not found"]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la récupération du sort : " . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Action non valide"]);
}
