<?php
require_once 'config.php';

// Démarrer la session
session_start();

// Obtenir les données du formulaire
$pseudo = $_POST['pseudo'];
$password = $_POST['password'];

// Vérifier si les données sont présentes
if (empty($pseudo) || empty($password)) {
    echo "Veuillez remplir tous les champs.";
    exit;
}

try {
    // Vérifier si l'utilisateur existe et récupérer son mot de passe haché
    $stmt = $pdo->prepare("SELECT id, password FROM players WHERE pseudo = :pseudo");
    $stmt->execute(['pseudo' => $pseudo]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Connexion réussie, créer une session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['pseudo'] = $pseudo;

        /// Vérifier le nombre de cartes de l'utilisateur
        $stmt_count_cards = $pdo->prepare("SELECT COUNT(*) AS card_count FROM cards_players WHERE player_id = :player_id");
        $stmt_count_cards->execute(['player_id' => $user['id']]);
        $card_count = $stmt_count_cards->fetchColumn();

        // Si l'utilisateur n'a pas au moins 4 cartes
        if ($card_count < 4) {
            // Insérer les cartes
            $cards_to_insert = [3, 4, 6, 8]; // IDs des cartes à insérer
            $stmt_insert_card = $pdo->prepare("INSERT INTO cards_players (card_id, player_id) VALUES (:card_id, :player_id)");

            foreach ($cards_to_insert as $card_id) {
                $stmt_insert_card->execute([
                    'card_id' => $card_id,
                    'player_id' => $user['id']
                ]);
            }

            // Insérer le deck
            $stmt_insert_deck = $pdo->prepare("
                INSERT INTO decks (name, deck_id, player_id, card_1_id, card_2_id, card_3_id, card_4_id) 
                VALUES (:name, :deck_id, :player_id, :card_1_id, :card_2_id, :card_3_id, :card_4_id)
            ");
            $stmt_insert_deck->execute([
                'name' => 'Starter',
                'deck_id' => 1,
                'player_id' => $user['id'],
                'card_1_id' => 3,
                'card_2_id' => 4,
                'card_3_id' => 6,
                'card_4_id' => 8
            ]);

            // Mettre à jour le deck du joueur
            $stmt_update_deck = $pdo->prepare("
                UPDATE players 
                SET deck = 1 
                WHERE id = :player_id
            ");
            $stmt_update_deck->execute(['player_id' => $user['id']]);
        }
        
        echo "Connexion réussie !";
        // Rediriger vers une page protégée
        header("Location: ./index.php");
        exit;
    } else {
        header("Location: ./?login");
        exit;
    }
} catch (PDOException $e) {
    echo "Erreur lors de la connexion : " . $e->getMessage();
}
?>