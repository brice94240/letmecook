<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Let Me Cook</title>
    <link href="./css/style.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
</head>
<?php
if (isset($_SESSION['user_id'])) { ?>
    <body>
        <div class="content">
            <header class="header">
                <div id="pseudo"></div>
                <div id="classement">CLASSEMENT</div>
                <div id="collection">COLLECTION</div>
                <div id="shop">BOUTIQUE</div>
                <div id="rank">1000</div>
                <div id="gold">0</div>
            </header>
    <?php
    if (!isset($_GET['profil']) && !isset($_GET['rank']) && !isset($_GET['collection']) && !isset($_GET['shop'])) {?>
        <div id="container_searching_game">
            <div id="searching_game" style="display:none;">RECHERCHE EN COURS</div>
            <div id="match_found" style="display:none;">Partie trouvée !</div>
            <div id="play">Jouer</div>
        </div>
        </div>
    </body>
<?php 
    } else if(isset($_GET['shop']) && !isset($_GET['profil']) && !isset($_GET['rank']) && !isset($_GET['collection'])) { ?>
        <!-- Modal -->
        <div id="cardModal" class="modal">
            <div class="modal-content">
                <span class="close-button" id="closeModal">&times;</span>
                <h2>Nouvelles Cartes Acquises</h2>
                <div id="newCardsContainer"></div>
            </div>
        </div>
        <div id="container_shop">
            <div class="shop-items">
                <div class="shop-item">
                    <div id="image_pack_1"></div>
                    <div id="img_pack_1"></div>
                    <div id="name_pack_1"></div>
                    <div id="desc_pack_1"></div>
                    <button id="buy_1" data-id="1" onclick="buyPack(this)"></button>
                </div>
                <div class="shop-item">
                    <div id="image_pack_2"></div>
                    <div id="img_pack_2"></div>
                    <div id="name_pack_2"></div>
                    <div id="desc_pack_2"></div>
                    <button id="buy_2" data-id="2" onclick="buyPack(this)"></button>
                </div>
                <div class="shop-item">
                    <div id="image_pack_3"></div>
                    <div id="img_pack_3"></div>
                    <div id="name_pack_3"></div>
                    <div id="desc_pack_3"></div>
                    <button id="buy_3" data-id="3" onclick="buyPack(this)"></button>
                </div>
            </div>
            <div class="shop-items">
            </div>
        </div>
        </div>
    </body>
    <?php
    }  else if(isset($_GET['collection']) && !isset($_GET['profil']) && !isset($_GET['rank']) && !isset($_GET['rank'])) { ?>
    <div id="container_collection_and_deck">
        <div id="container_collection">
            <input type="text" id="search_input" placeholder="Rechercher par nom" />
            <select id="type_filter">
                <option value="">Tous les types</option>
                <option value="Fire">Feu</option>
                <option value="Water">Eau</option>
                <option value="Earth">Terre</option>
                <option value="Air">Air</option>
                <option value="Dark">Ténèbre</option>
                <option value="Light">Lumière</option>
                <!-- Ajoutez d'autres types selon vos besoins -->
            </select>
            <div class="collection-items">
                <!-- Ajoutez d'autres collection-items selon vos besoins -->
            </div>
        </div>
        <div id="container_deck">
            <div id="title_container_deck">Mon deck</div>
            <div id="collection-decks-add">
                <div id="deck_container_create">
                    <!-- Les cercles pour les nouveaux decks -->
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                </div>
                <!-- Le bouton pour créer un deck -->
                <div id="create_deck">+</div>
            </div>
            <select name="decks-select" id="decks-select">
            </select>
            <div id="title_container_deck">Autres</div>
            <!-- Collection des decks -->
            <div id="collection-decks"></div>
        </div>
        <div id="container_create_deck" style="display:none;">
            <input type="text" id="title_create_container_deck" placeholder="Deck Name" />
            <div class="collection_create_decks">
                <div id="collection_create_deck_1" class="collection_create_deck">
                    <div id="collection_create_deck_type_1" class="collection_create_deck_type"></div>
                    <div id="collection_create_deck_name_1" class="collection_create_deck_name"></div>
                </div>
                <div id="collection_create_deck_2" class="collection_create_deck">
                    <div id="collection_create_deck_type_2" class="collection_create_deck_type"></div>
                    <div id="collection_create_deck_name_2" class="collection_create_deck_name"></div>
                </div>
                <div id="collection_create_deck_3" class="collection_create_deck">
                    <div id="collection_create_deck_type_2" class="collection_create_deck_type"></div>
                    <div id="collection_create_deck_name_3" class="collection_create_deck_name"></div>
                </div>
                <div id="collection_create_deck_4" class="collection_create_deck">
                    <div id="collection_create_deck_type_2" class="collection_create_deck_type"></div>
                    <div id="collection_create_deck_name_4" class="collection_create_deck_name"></div>
                </div>
            </div>
            <div id="valid_and_cancel">
                <div id="valid_new_deck">Valider</div>
                <div id="cancel_new_deck">Retour</div>
            </div>
        </div>
    </div>
    </div>
    </body>
    <?php
    }
    ?>
    <footer class="footer">
    <div id="amis">AMIS</div>
    <div id="accueil">ACCUEIL</div>
    <div id="deconnexion">DECONNECTION</div>
<?php
} else { ?>
<body>
<div class="content">
    <div class="logo">      
    <img src="./img/logo.jpg" alt="LetMeCook Logo">
</div>
<?php
    if(!isset($_GET['login']) && !isset($_GET['signin'])){
    header("Location: ./?login");
    } elseif(isset($_GET['login']) && !isset($_GET['signin'])){
    ?>
        <div class="connexion_inscription buttons_container">
            <form class="button" action="login_process.php" method="post">
                <div class="buttons_container">
                    <input placeholder="Pseudo" type="text" id="pseudo" name="pseudo" required>
                </div>
                <div class="buttons_container">
                    <input placeholder="Mot de passe" type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="button-submit"><div class="button-container" >Connexion</div></button>
            </form>
            <div class="button button_inscription">
                <a href="./?signin"><div class="button-container" >Inscription</div></a>
            </div>
        </div>
    <?php
    } elseif(isset($_GET['signin']) && !isset($_GET['login'])){
    ?>
        <div class="connexion_inscription buttons_container">
            <form class="button" action="signin_process.php" method="post">
                <div class="buttons_container">
                    <input placeholder="Pseudo" type="text" id="pseudo" name="pseudo" required>
                </div>
                <div class="buttons_container">
                    <input placeholder="Email" type="email" id="email" name="email" required>
                </div>
                <div class="buttons_container">
                    <input placeholder="Mot de passe" type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="button-submit"><div class="button-container" >S'inscrire</div></button>
            </form>
            <div class="button button_inscription">
                <a href="./?login"><div class="button-container" >Se Connecter</div></a>
            </div>
        </div>
    <?php
    } ?>
    </div>
    </div>
    </body>
<?php }
?>
<script>
// Définir la fonction buyPack ici
function buyPack(button) {
    const packId = button.getAttribute('data-id');

    $.ajax({
        url: 'buy.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'buy_pack',
            user_id: '<?php echo $_SESSION['user_id']; ?>',
            packId: packId
        },
        success: function(response) {
            if (response.success && response.new_cards) {
                // Remplir la modal avec les nouvelles cartes
                const newCardsContainer = document.getElementById("newCardsContainer");
                newCardsContainer.innerHTML = ''; // Vider le conteneur avant d'ajouter les nouvelles cartes

                response.new_cards.forEach(card => {
                    const cardElement = document.createElement("div");
                    cardElement.classList.add("card-item");

                    // Créer le contenu de la carte
                    cardElement.innerHTML = `
                        <img src="./img/${card.avatar}" alt="${card.name}" style="width: 100px; height: auto;">
                        <h3>${card.name}</h3>
                        <p>Type: ${card.type}</p>
                        <p>HP: ${card.hp}</p>
                        <p>Rareté: ${card.rarity}</p>
                        <p>Compétence 1: ${card.skill_1_desc} (Cooldown: ${card.skill_1_cooldown})</p>
                        <p>Compétence 2: ${card.skill_2_desc} (Cooldown: ${card.skill_2_cooldown})</p>
                    `;

                    newCardsContainer.appendChild(cardElement);
                });

                // Afficher la modal
                document.getElementById("cardModal").style.display = "block";
            }
        },
        error: function(xhr, status, error) {
            console.error('Erreur:', status, error);
        }
    });
    // Appeler ici la logique d'achat du pack
}

// Fonction pour fermer la modal
if(document.getElementById("closeModal")){
    document.getElementById("closeModal").onclick = function() {
        document.getElementById("cardModal").style.display = "none";
    }
}

// Fermer la modal si on clique en dehors de celle-ci
window.onclick = function(event) {
    const modal = document.getElementById("cardModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
}


$(document).ready(function() {
    $('#shop').off('click').click(function() {
        window.location.replace("./?shop");
    });
    $('#profil').off('click').click(function() {
        window.location.replace("./?profil");
    });
    $('#rank').off('click').click(function() {
        window.location.replace("./?rank");
    });
    $('#collection').off('click').click(function() {
        window.location.replace("./?collection");
    });
    $('#deconnexion').off('click').click(function() {
        window.location.replace("./logout.php");
    });
    $('#accueil').off('click').click(function() {
        window.location.replace("./");
    });
    $('#play').off('click').click(function() {
        SearchGame();
    });
    function GoToGame(gameId) {
        window.location.href = './game.php?game_id='+gameId;
    }
    function FindEnnemies(){
        var player_id = '<?php echo $_SESSION['user_id']; ?>';
        $.ajax({
            url: 'find_ennemies.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'find_ennemies',
                user_id: player_id,
            },
            success: function(response) {
                console.log(response);
            },
            error: function(xhr, status, error) {
                console.error('Erreur:', status, error);
            }
        });
    }

    var change_input = false;
    var change_type = false;

    function RefreshPage(){
        var player_id = '<?php echo $_SESSION['user_id']; ?>';
        $.ajax({
            url: 'refresh_page.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'refresh',
                user_id: player_id,
            },
            success: function(response) {
                var status_queue = response.player_info['queue'];
                var gold = response.player_info['gold'];
                var rank_point = response.player_info['rank_point'];
                if(status_queue == "search"){
                    $('#play').show();
                    $('#play').html("Annuler").css("background-color", "orange");
                    $('#searching_game').show();
                    $('#match_found').hide();
                    FindEnnemies();
                } else if(status_queue == null){
                    $('#play').show();
                    $('#play').html("Jouer").css("background-color", "green");
                    $('#searching_game').hide();
                    $('#match_found').hide();
                } else if(status_queue == "find"){
                    $('#play').hide();
                    $('#searching_game').hide();
                    $('#match_found').show();
                    $('body').css('filter', 'grayscale(1)');
                    setTimeout(function() {
                        GoToGame(response.game.id);
                    }, 3000);
                }
                $('#gold').html(gold+"<img src='./img/gold.png'>");
                $('#rank').html(rank_point+"<img src='./img/gold.png'>");
                $('#pseudo').html(response.player_info['pseudo']);
            },
            error: function(xhr, status, error) {
                console.error('Erreur:', status, error);
            }
        });

        const isShop = <?php echo isset($_GET['shop']) ? 'true' : 'false'; ?>;
        if (isShop) {
            $.ajax({
                url: 'refresh_shop.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'refresh',
                    user_id: player_id,
                },
                success: function(response) {
                    if('<?php echo isset($_GET['shop']); ?>'){
                        if(response.success && response.packs_shop) {
                            response.packs_shop.forEach(function(pack_shop) {
                                // Mettre à jour les informations de chaque pack
                                const packId = pack_shop.id; // Récupérer l'ID du pack
                
                                // Mettre à jour les éléments en fonction de l'ID
                                // Mettre à jour les éléments en fonction de l'ID
                                const imagePackElement = document.getElementById(`image_pack_${packId}`);
                                
                                // Appliquer les styles CSS souhaités
                                imagePackElement.style.backgroundImage = `url(./img/${pack_shop.image})`;
                                imagePackElement.style.width = "6em";
                                imagePackElement.style.height = "9em";
                                imagePackElement.style.backgroundSize = "contain";
                                imagePackElement.style.backgroundPosition = "center"; // Centrer l'image
                                document.getElementById(`name_pack_${packId}`).innerText = pack_shop.name;
                                document.getElementById(`desc_pack_${packId}`).innerText = pack_shop.description;
                                
                                // Ajouter le prix au bouton
                                const button = document.getElementById(`buy_${packId}`);
                                button.innerText = `${pack_shop.price}`;
                            });
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur:', status, error);
                }
            });
        }

        const isCollection = <?php echo isset($_GET['collection']) ? 'true' : 'false'; ?>;
        if (isCollection) {
            const searchInput = document.getElementById('search_input') ? document.getElementById('search_input') : "";
            const typeFilter = document.getElementById('type_filter');
            const searchTerm = searchInput.value.toLowerCase();
            const selectedType = typeFilter.value;

            $('#create_deck').off('click').click(function() {
                $('#container_deck').hide();
                $('#container_create_deck').show();
            });

            $('#cancel_new_deck').off('click').click(function() {
                $('#container_deck').show();
                $('#container_create_deck').hide();
            });

            $('#search_input').on('input', function() {
                change_input = true;
                change_type = true;
            });
            $('#type_filter').on('input', function() {
                change_input = true;
                change_type = true;
            });

            //si je change le texte
            $.ajax({
                url: 'refresh_collection.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'refresh',
                    user_id: player_id,
                    searchTerm : searchTerm,
                    selectedType : selectedType,
                },
                success: function(response) {
                    if((change_input == true || change_type == true) || ($(".collection-item").length == 0)){
                        if (response.collection_player) {
                            const collectionContainer = document.querySelector('.collection-items');
                            collectionContainer.innerHTML = ''; // Vider le conteneur avant d'ajouter les nouvelles cartes
                            response.collection_player.forEach(function(card) {
                                const cardElement = document.createElement('div');
                                cardElement.className = 'collection-item'; // Ajouter une classe pour le style
                                cardElement.setAttribute('data-id', card.id);
                                const ImageValues = {
                                    'Unknow': 'Unknow_bck.png',
                                    'Fire': 'Fire_bck.png',
                                    'Water': 'Water_bck.png',
                                    'Earth': 'Earth_bck.png',
                                    'Air': 'Air_bck.png',
                                    'Dark': 'Dark_bck.png',
                                    'Light': 'Light_bck.png',
                                    // Ajoutez d'autres types de cartes ici
                                };
        
                                const LogoTypeValues = {
                                    'Unknow': 'water.png',
                                    'Fire': 'fire.png',
                                    'Water': 'water.png',
                                    'Earth': 'earth.png',
                                    'Air': 'air.png',
                                    'Dark': 'dark.png',
                                    'Light': 'light.png',
                                    // Ajoutez d'autres types de cartes ici
                                };

                                const ValuesColor = {
                                    'Unknow': 'white',
                                    'Fire': '#d80a16',
                                    'Water': '#006495',
                                    'Earth': '#b5a2a1',
                                    'Air': '#596b00',
                                    'Dark': '#671866',
                                    'Light': '#c6b20e',
                                    // Ajoutez d'autres types de cartes ici
                                };

                                const RarityTextValues = {
                                    'common': 'Commune',
                                    'rare': 'Rare',
                                    'legendary': 'Legendaire',
                                    // Ajoutez d'autres types de cartes ici
                                };

                                const RarityColorValues = {
                                    'common': '#00da00',
                                    'rare': '#eb00ff',
                                    'legendary': '#ff9700',
                                    // Ajoutez d'autres types de cartes ici
                                };

                                const DiffType = [
                                    "BOOST_CRIT",
                                    "REDUCT_BOOSTS",
                                    "REDUCT_BOOST_ALEATORY",
                                    "REDUCT_TWO_BOOSTS_ALEATORY",
                                    "REDUCT_THREE_BOOSTS_ALEATORY",
                                    "REDUCT_FOUR_BOOSTS_ALEATORY",
                                    "AUGMENT_BOOSTS",
                                    "AUGMENT_BOOST_ALEATORY",
                                    "AUGMENT_TWO_BOOSTS_ALEATORY",
                                    "AUGMENT_THREE_BOOSTS_ALEATORY",
                                    "AUGMENT_FOUR_BOOSTS_ALEATORY",
                                    "TRADE_BOOST",
                                    "TRADE_BOOST_FIRE",
                                    "TRADE_BOOST_WATER",
                                    "TRADE_BOOST_EARTH",
                                    "TRADE_BOOST_AIR",
                                    "TRADE_BOOST_DARK",
                                    "TRADE_BOOST_LIGHT"
                                ];

                                const Image = ImageValues[card.type] || 'Unknow_bck.png'; // Utiliser 0deg par défaut si le type n'est pas défini
                                const LogoType = LogoTypeValues[card.type] ||  'water.png';
                                const RarityText = RarityTextValues[card.rarity] ||  'Commune';
                                const RarityColor = RarityColorValues[card.rarity] ||  'green';


                                const skillEffect1 = JSON.parse(card.skill_1_effect); // Conversion de la chaîne JSON en objet
                                const skillEffect2 = JSON.parse(card.skill_2_effect); // Conversion de la chaîne JSON en objet
                                let effect1Display = '';
                                let effect2Display = '';


                                // Boucle à travers les effets pour construire l'affichage
                                skillEffect1.forEach(effect => {
                                    if (DiffType.includes(effect.TYPE)) {
                                        if(effect.TYPE == "TRADE_BOOST_FIRE" || effect.TYPE == "TRADE_BOOST_WATER" || effect.TYPE == "TRADE_BOOST_EARTH" || effect.TYPE == "TRADE_BOOST_AIR" || effect.TYPE == "TRADE_BOOST_DARK" || effect.TYPE == "TRADE_BOOST_LIGHT"){
                                            let element = effect.TYPE.replace('TRADE_BOOST_', '').toLowerCase().replace(/^./, char => char.toUpperCase());
                                            effect1Display = `<span style="vertical-align: text-top;">${card.skill_1_desc}</span><div class='tooltip_base_value' style='color:${ValuesColor[card['type']]}'>(<div class="sell_element" style='display:contents;color:${ValuesColor[element]}'>${effect.PRICE}</div> - ${effect.DURATION})</div>`;
                                        } else if(effect.TYPE == "TRADE_BOOST"){
                                            effect1Display = `<span style="vertical-align: text-top;">${card.skill_1_desc}</span></div>`;
                                        } else {
                                            effect1Display = `<span style="vertical-align: text-top;">${card.skill_1_desc}</span><div class='tooltip_base_value' style='color:${ValuesColor[card['type']]}'>(${effect.DURATION})</div>`;
                                        }
                                    } else {
                                        effect1Display = `<span style="vertical-align: text-top;">${card.skill_1_desc}</span><div class='tooltip_base_value' style='color:${ValuesColor[card['type']]}'>(${card.skill_1_base_value})</div>`;
                                    }
                                });

                                skillEffect2.forEach(effect => {
                                    if (DiffType.includes(effect.TYPE)) {
                                        if(effect.TYPE == "TRADE_BOOST_FIRE" || effect.TYPE == "TRADE_BOOST_WATER" || effect.TYPE == "TRADE_BOOST_EARTH" || effect.TYPE == "TRADE_BOOST_AIR" || effect.TYPE == "TRADE_BOOST_DARK" || effect.TYPE == "TRADE_BOOST_LIGHT"){
                                            let element = effect.TYPE.replace('TRADE_BOOST_', '').toLowerCase().replace(/^./, char => char.toUpperCase());
                                            effect2Display = `<span style="vertical-align: text-top;">${card.skill_2_desc}</span><div class='tooltip_base_value' style='color:${ValuesColor[card['type']]}'>(<div class="sell_element" style='display:contents;color:${ValuesColor[element]}'>${effect.PRICE}</div> - ${effect.DURATION})</div>`;
                                        } else if(effect.TYPE == "TRADE_BOOST"){
                                            effect2Display = `<span style="vertical-align: text-top;">${card.skill_2_desc}</span></div>`;
                                        } else {
                                            effect2Display = `<span style="vertical-align: text-top;">${card.skill_2_desc}</span><div class='tooltip_base_value' style='color:${ValuesColor[card['type']]}'>(${effect.DURATION})</div>`;
                                        }
                                    } else {
                                        effect2Display = `<span style="vertical-align: text-top;">${card.skill_2_desc}</span><div class='tooltip_base_value' style='color:${ValuesColor[card['type']]}'>(${card.skill_2_base_value})</div>`;
                                    }
                                });

                                // Créer le contenu de la carte
                                cardElement.innerHTML = `
                                    <div class="card-avatar" style="background-image: url('./img/${card.avatar}');"></div>
                                    <div class="card-info">
                                        <div class='collection_name_card'>
                                            ${card.name}
                                        </div>
                                        <div class='collection_type_card'>
                                            <img src='./img/${LogoType}'></img>
                                        </div>
                                        <p style="font-weight:900;color:${RarityColor};">${RarityText}</p>
                                        <p>HP: ${card.hp}</p>
                                        <div class='collection_cooldown_card'>
                                            <div class='collection_cooldown_1'>
                                                <img src="./img/cooldown_${card.skill_1_cooldown}.png" alt="Cooldown ${card.skill_1_cooldown}">
                                                <span style="vertical-align: text-top;">${card.skill_1_name}</span>
                                                <div class='tooltipskill1'>
                                                    ${effect1Display}
                                                </div>
                                            </div>
                                            <div class='collection_cooldown_2'>
                                                <img src="./img/cooldown_${card.skill_2_cooldown}.png" alt="Cooldown ${card.skill_1_cooldown}">
                                                <span style="vertical-align: text-top;">${card.skill_2_name}</span>
                                                <div class='tooltipskill2'>
                                                    ${effect2Display}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;

                                // Ajouter la carte au conteneur
                                collectionContainer.appendChild(cardElement);
                            });
                        }
                        change_input = false;
                        change_type = false;
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur:', status, error);
                }
            });
            $.ajax({
                url: 'refresh_decks.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'refresh',
                    user_id: player_id,
                },
                success: function(response) {
                    // Parcourir chaque deck dans la réponse
                    $('#collection-decks').empty();
                    
                    let RefreshInput = false;

                    // Récupérer le nombre d'options dans le select
                    let inputCount = $('#decks-select option').length;

                    // Vérifier si le nombre de decks est différent
                    if (response.decks.length !== inputCount) {
                        $('#decks-select').empty();
                        RefreshInput = true;
                    }

                    response.deck.forEach(function(my_deck) {
                        // Créer une div pour chaque deck
                        let deckHtml = `
                            <div id="deck_container_create_add" class="deck-container" data-id="${my_deck.deck_id}">
                                <!-- Les cercles pour les cartes -->
                                <div class="circle" style="background-image: url('./img/${my_deck.card_1_avatar}'); background-size: cover; background-position: center;"></div>
                                <div class="circle" style="background-image: url('./img/${my_deck.card_2_avatar}'); background-size: cover; background-position: center;"></div>
                                <div class="circle" style="background-image: url('./img/${my_deck.card_3_avatar}'); background-size: cover; background-position: center;"></div>
                                <div class="circle" style="background-image: url('./img/${my_deck.card_4_avatar}'); background-size: cover; background-position: center;"></div>
                                <div id="create_deck">+</div>
                            </div>
                            <div class="name_deck">${my_deck.name}</div>
                        `;

                        // Ajouter le deck dans #collection-decks
                        if(my_deck.deck_id != $('#deck_container_create_add').attr("data-id")){
                            $('#collection-decks-add').empty();
                            $('#collection-decks-add').append(deckHtml);
                        }
                    });
                    
                    response.decks.forEach(function(deck) {
                        // Créer une div pour chaque deck
                        let deckHtml = `
                            <div id="deck_container_${deck.deck_id}" class="deck-container">
                                <!-- Les cercles pour les cartes -->
                                <div class="circle" style="background-image: url('./img/${deck.card_1_avatar}'); background-size: cover; background-position: center;"></div>
                                <div class="circle" style="background-image: url('./img/${deck.card_2_avatar}'); background-size: cover; background-position: center;"></div>
                                <div class="circle" style="background-image: url('./img/${deck.card_3_avatar}'); background-size: cover; background-position: center;"></div>
                                <div class="circle" style="background-image: url('./img/${deck.card_4_avatar}'); background-size: cover; background-position: center;"></div>
                                <div class="deck-controls">
                                    <!-- Bouton pour supprimer le deck -->
                                    <div id="delete_deck_${deck.deck_id}" data-id=${deck.deck_id} class="delete-deck">-</div>
                                </div>
                            </div>
                            <div class="name_deck">${deck.name}</div>
                        `;

                        // Ajouter le deck dans #collection-decks
                        $('#collection-decks').append(deckHtml);
                        // Ajouter chaque deck dans la liste de sélection (select)
                        const selectedDeckId = response.deck[0].deck_id;

                        if (RefreshInput && $('#container_deck').is(':visible')) {
                            $('#decks-select').append(`<option value="${deck.deck_id}">${deck.name}</option>`);
                            $('#decks-select').val(selectedDeckId); // Sélectionner le deck utilisé
                        }
                    });
                    if (RefreshInput && $('#container_deck').is(':visible')) {
                        const selectedDeckId = response.deck[0].deck_id; // On prend le premier deck car c'est un tableau
                        $('#decks-select').val(selectedDeckId);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur:', status, error);
                }
            });
            $('#decks-select').change(function() {
                var selectedDeckId = $(this).val(); // Obtenir l'ID du deck sélectionné
                $.ajax({
                    url: 'refresh_decks.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'select',
                        user_id: player_id,
                        selectedDeckId: selectedDeckId
                    },
                    success: function(response) {
                        // console.log(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur:', status, error);
                    }
                });
                // Ici, vous pouvez ajouter votre logique pour ce qui doit se passer lorsque le deck est changé
            });
            if ($('#container_create_deck').is(':visible')) {
                $(".collection-item").off('click').click(function() {
                    // Récupérer l'ID de la carte à partir de l'attribut data-id
                    let IdCard = $(this).attr('data-id');

                    // Récupérer le nom de la carte
                    let nameCard = $(this).find('.collection_name_card').text().trim();

                    // Récupérer l'image de l'avatar de la carte
                    let avatarUrl = $(this).find('.card-avatar').css('background-image');
                    avatarUrl = avatarUrl.replace(/url\(["']?(.*?)["']?\)/, '$1'); // Extraire l'URL de l'image
                    avatarUrl = avatarUrl.split('/').pop(); // Récupérer seulement le nom du fichier

                    // Récupérer le type de la carte (l'image de type)
                    let typeCard = $(this).find('.collection_type_card img').attr('src');
                    typeCard = typeCard.replace(/url\(["']?(.*?)["']?\)/, '$1'); // Extraire l'URL de l'image
                    typeCard = typeCard.split('/').pop(); // Récupérer seulement le nom du fichier
                    let GoodEmptyDiv; // Déclarer GoodEmptyDiv en dehors de la boucle
                    let DivFind = false; // Assurez-vous que DivFind est initialisé à false

                    $(".collection_create_decks > div").each(function() {
                        if (!DivFind) {
                            let nameDiv = $(this).find('[id^="collection_create_deck_name_"]');

                            if (nameDiv.is(':empty')) {
                                let typeDiv = $(this).find('[id^="collection_create_deck_type_"]');
                                let GoodEmptyDiv = this;

                                DivFind = true;
                                if (GoodEmptyDiv) {
                                    $(GoodEmptyDiv).attr('data-id', IdCard); // Remplace 'newId' par l'ID souhaité
                                    GoodEmptyDiv.style.backgroundImage = "url('./img/" + avatarUrl + "')"; // Appliquer le style
                                    nameDiv.html(nameCard);
                                    typeDiv.html('<img src="./img/' + typeCard + '" alt="Type de carte">');
                                }
                            }
                        }
                    });
                });
                $(".collection_create_deck").off('click').click(function() {
                    // Récupérer l'ID de la carte à partir de l'attribut data-id
                    let IdCard = $(this).attr('data-id');

                    if (IdCard) {
                        let Card = $(this)[0];
                        let nameCard = $(this).find('.collection_create_deck_name');
                        let typeCard = $(this).find('.collection_create_deck_type');

                        // Apply the background image style
                        Card.style.backgroundImage = "url('')"; 
                        
                        // Set the HTML content for nameCard and typeCard
                        nameCard.html("");  // Clear the HTML content of the name card
                        typeCard.html("");  // Clear the HTML content of the type card
                        $(this).attr('data-id', "");
                    }
                });
                $("#valid_new_deck").off('click').click(function() {
                    let TitleDeck = $('#title_create_container_deck').val();
                    // Vérifier si TitleDeck n'est pas vide
                    if (TitleDeck.trim() !== "") {
                        let dataIds = [];
                        let allDecksHaveId = true;

                        // Vérifier chaque deck et stocker les data-id
                        for (let i = 1; i <= 4; i++) {
                            let dataId = $("#collection_create_deck_" + i).attr('data-id');

                            if (dataId) {
                                // Vérifier si le data-id est déjà présent
                                if (dataIds.includes(dataId)) {
                                    allDecksHaveId = false; // Un data-id en double trouvé
                                    break; // Pas besoin de continuer
                                }
                                dataIds.push(dataId); // Ajouter à la liste des data-id
                            } else {
                                allDecksHaveId = false; // Un deck n'a pas d'id
                                break; // Pas besoin de continuer
                            }
                        }

                        // Vérifier si tous les decks ont un id et qu'il n'y a pas de doublons
                        if (allDecksHaveId) {
                            $.ajax({
                                url: 'refresh_decks.php',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    action: 'add',
                                    user_id: player_id,
                                    dataIds: dataIds,
                                    TitleDeck: TitleDeck
                                },
                                success: function(response) {
                                    $('#container_deck').show();
                                    $('#container_create_deck').hide();
                                },
                                error: function(xhr, status, error) {
                                    console.error('Erreur:', status, error);
                                }
                            });
                        } else {
                            console.log("Carte en double dans votre deck !");
                        }
                    } else {
                        console.log("Vous devez donner un nom a votre deck !");
                    }
                });
            }
            $(document).on('click', '.delete-deck', function() {
                let dataId = $(this).attr('data-id');
                console.log(dataId);
                $.ajax({
                    url: 'refresh_decks.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'delete',
                        user_id: player_id,
                        dataId: dataId,
                    },
                    success: function(response) {
                        // console.log(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur:', status, error);
                    }
                });
            });
        }

    }
    function SearchGame(){
        var player_id = '<?php echo $_SESSION['user_id']; ?>';
        $.ajax({
            url: 'search_or_cancel_game.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'search',
                user_id: player_id,
            },
            success: function(response) {
                if(response.status_queue == "search"){
                    $('#play').html("Annuler").css("background-color", "orange");
                    $('#searching_game').show();
                } else if(response.status_queue == null){
                    $('#play').html("Jouer").css("background-color", "green");;
                    $('#searching_game').hide();
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur:', status, error);
            }
        });
    }

    RefreshPage();
    setInterval(function() {
        RefreshPage(); // Recharger les parties toutes les 2 secondes (2000 ms)
    }, 500); // Répéter toutes les 2 secondes (2000 ms)
});
</script>
</html>