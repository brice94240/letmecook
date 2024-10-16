<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ./?login");
} elseif(!isset($_GET['game_id'])) {
    header("Location: ./");
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Let Me Cook</title>
    <link href="./css/game.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
     <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
</head>
<body>
    <div class="content">
    <div id="ennemie">
        <div id="pseudo_ennemie"></div>
        <div id="rank_ennemie"></div>
        <div id="avatar_ennemie"></div>
    </div>
    <!-- Zones des cartes -->
    <div class="map-interactive-area" id="carte_player_1">
        <div id="type_carte_player_1"></div>
        <div id="name_carte_player_1"></div>
        <div id="img_carte_player_1">
            <div class="map-interactive-area-effects" id="effects_carte_player_1"></div>
        </div>
        <div id="skill_1_carte_player_1" class="skill_1_carte_player">
            <div id="cooldown_skill_1_carte_player_1"></div>
        </div>
        <div id="skill_2_carte_player_1" class="skill_2_carte_player">
            <div id="cooldown_skill_2_carte_player_1"></div>
        </div>
        <div id="hp_carte_player_1"></div>
    </div>
    <div class="map-interactive-area" id="carte_player_2">
        <div id="type_carte_player_2"></div>
        <div id="name_carte_player_2"></div>
        <div id="img_carte_player_2">
            <div class="map-interactive-area-effects" id="effects_carte_player_2"></div>
        </div>
        <div id="skill_1_carte_player_2" class="skill_1_carte_player">
            <div id="cooldown_skill_1_carte_player_2"></div>
        </div>
        <div id="skill_2_carte_player_2" class="skill_2_carte_player">
            <div id="cooldown_skill_2_carte_player_2"></div>
        </div>
        <div id="hp_carte_player_2"></div>
    </div>
    <div class="map-interactive-area" id="carte_player_3">
        <div id="type_carte_player_3"></div>
        <div id="name_carte_player_3"></div>
        <div id="img_carte_player_3">
            <div class="map-interactive-area-effects" id="effects_carte_player_3"></div>
        </div>
        <div id="skill_1_carte_player_3" class="skill_1_carte_player">
            <div id="cooldown_skill_1_carte_player_3"></div>
        </div>
        <div id="skill_2_carte_player_3" class="skill_2_carte_player">
            <div id="cooldown_skill_2_carte_player_3"></div>
        </div>
        <div id="hp_carte_player_3"></div>
    </div>
    <div class="map-interactive-area" id="carte_player_4">
        <div id="type_carte_player_4"></div>
        <div id="name_carte_player_4"></div>
        <div id="img_carte_player_4">
            <div class="map-interactive-area-effects" id="effects_carte_player_4"></div>
        </div>
        <div id="skill_1_carte_player_4" class="skill_1_carte_player">
            <div id="cooldown_skill_1_carte_player_4"></div>
        </div>
        <div id="skill_2_carte_player_4" class="skill_2_carte_player">
            <div id="cooldown_skill_2_carte_player_4"></div>
        </div>
        <div id="hp_carte_player_4"></div>
    </div>

    <div class="map-interactive-area" id="carte_ennemy_1">
        <div id="type_carte_ennemy_1"></div>
        <div id="name_carte_ennemy_1"></div>
        <div id="img_carte_ennemy_1">
            <div class="map-interactive-area-effects" id="effects_carte_ennemy_1"></div>
        </div>
        <div id="skill_1_carte_ennemy_1" class="skill_1_carte_ennemy">
            <div id="cooldown_skill_1_carte_ennemy_1"></div>
        </div>
        <div id="skill_2_carte_ennemy_1" class="skill_2_carte_ennemy">
            <div id="cooldown_skill_2_carte_ennemy_1"></div>
        </div>
        <div id="hp_carte_ennemy_1"></div>
    </div>
    <div class="map-interactive-area" id="carte_ennemy_2">
        <div id="type_carte_ennemy_2"></div>
        <div id="name_carte_ennemy_2"></div>
        <div id="img_carte_ennemy_2">
            <div class="map-interactive-area-effects" id="effects_carte_ennemy_2"></div>
        </div>
        <div id="skill_1_carte_ennemy_2" class="skill_1_carte_ennemy">
            <div id="cooldown_skill_1_carte_ennemy_2"></div>
        </div>
        <div id="skill_2_carte_ennemy_2" class="skill_2_carte_ennemy">
            <div id="cooldown_skill_2_carte_ennemy_2"></div>
        </div>
        <div id="hp_carte_ennemy_2"></div>
    </div>
    <div class="map-interactive-area" id="carte_ennemy_3">
        <div id="type_carte_ennemy_3"></div>
        <div id="name_carte_ennemy_3"></div>
        <div id="img_carte_ennemy_3">
            <div class="map-interactive-area-effects" id="effects_carte_ennemy_3"></div>
        </div>
        <div id="skill_1_carte_ennemy_3" class="skill_1_carte_ennemy">
            <div id="cooldown_skill_1_carte_ennemy_3"></div>
        </div>
        <div id="skill_2_carte_ennemy_3" class="skill_2_carte_ennemy">
            <div id="cooldown_skill_2_carte_ennemy_3"></div>
        </div>
        <div id="hp_carte_ennemy_3"></div>
    </div>
    <div class="map-interactive-area" id="carte_ennemy_4">
        <div id="type_carte_ennemy_4"></div>
        <div id="name_carte_ennemy_4"></div>
        <div id="img_carte_ennemy_4">
            <div class="map-interactive-area-effects" id="effects_carte_ennemy_4"></div>
        </div>
        <div id="skill_1_carte_ennemy_4" class="skill_1_carte_ennemy">
            <div id="cooldown_skill_1_carte_ennemy_4"></div>
        </div>
        <div id="skill_2_carte_ennemy_4" class="skill_2_carte_ennemy">
            <div id="cooldown_skill_2_carte_ennemy_4"></div>
        </div>
        <div id="hp_carte_ennemy_4"></div>
    </div>
    <div class="map-interactive-area-elements" id="elements_player_1">
        <div id="elements_player_fire" class="elements_player_elements">
            <div id="value_content_player_fire" class="value_content_player_elements">
                <div id="value_player_fire" class="value_player_element">0</div>
            </div>
        </div>
        <div id="elements_player_water" class="elements_player_elements">
            <div id="value_content_player_water" class="value_content_player_elements">
                <div id="value_player_water" class="value_player_element">0</div>
            </div>
        </div>
        <div id="elements_player_earth" class="elements_player_elements">
            <div id="value_content_player_earth" class="value_content_player_elements">
                <div id="value_player_earth" class="value_player_element">0</div>
            </div>
        </div>
    </div>
    <div class="map-interactive-area-elements" id="elements_player_2">
        <div id="elements_player_air" class="elements_player_elements">
            <div id="value_content_player_air" class="value_content_player_elements">
                <div id="value_player_air" class="value_player_element">0</div>
            </div>
        </div>
        <div id="elements_player_dark" class="elements_player_elements">
            <div id="value_content_player_dark" class="value_content_player_elements">
                <div id="value_player_dark" class="value_player_element">0</div>
            </div>
        </div>
        <div id="elements_player_light" class="elements_player_elements">
            <div id="value_content_player_light" class="value_content_player_elements">
                <div id="value_player_light" class="value_player_element">0</div>
            </div>
        </div>
    </div>
    <<div class="map-interactive-area-elements" id="elements_ennemy_1">
        <div id="elements_ennemy_fire" class="elements_ennemy_elements">
            <div id="value_content_ennemy_fire" class="value_content_ennemy_elements">
                <div id="value_ennemy_fire" class="value_ennemy_element">0</div>
            </div>
        </div>
        <div id="elements_ennemy_water" class="elements_ennemy_elements">
            <div id="value_content_ennemy_water" class="value_content_ennemy_elements">
                <div id="value_ennemy_water" class="value_ennemy_element">0</div>
            </div>
        </div>
        <div id="elements_ennemy_earth" class="elements_ennemy_elements">
            <div id="value_content_ennemy_earth" class="value_content_ennemy_elements">
                <div id="value_ennemy_earth" class="value_ennemy_element">0</div>
            </div>
        </div>
    </div>
    <div class="map-interactive-area-elements" id="elements_ennemy_2">
        <div id="elements_ennemy_air" class="elements_ennemy_elements">
            <div id="value_content_ennemy_air" class="value_content_ennemy_elements">
                <div id="value_ennemy_air" class="value_ennemy_element">0</div>
            </div>
        </div>
        <div id="elements_ennemy_dark" class="elements_ennemy_elements">
            <div id="value_content_ennemy_dark" class="value_content_ennemy_elements">
                <div id="value_ennemy_dark" class="value_ennemy_element">0</div>
            </div>
        </div>
        <div id="elements_ennemy_light" class="elements_ennemy_elements">
            <div id="value_content_ennemy_light" class="value_content_ennemy_elements">
                <div id="value_ennemy_light" class="value_ennemy_element">0</div>
            </div>
        </div>
    </div>
    <div class="map-interactive-area-finish-turn" id="finish_turn"><img src="./img/finish_turn.png"></img>
    </div>
    
    <div id="joueur">
        <div id="pseudo_joueur"></div>
        <div id="rank_joueur"></div>
        <div id="avatar_joueur"></div>
    </div>
    </div>
</body>
</html>
<script>
$(document).ready(function() {
    var TabTarget = [];
    var div_skill = "";

    function RefreshGame(){
        var game_id = '<?php echo $_GET['game_id']; ?>';
        var player_id = '<?php echo $_SESSION['user_id']; ?>';
        $.ajax({
            url: 'refresh_game.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'refresh',
                user_id: player_id,
                game_id: game_id
            },
            success: function(response) {
                if(response.game === true){
                    // Parcourir le tableau response.players_info
                    response.players_info.forEach(function(player) {
                        if (player.id == '<?php echo $_SESSION['user_id']; ?>') {
                            $('#pseudo_joueur').html(player.pseudo);
                            $('#rank_joueur').html("Elo : "+player.rank_point);
                            $('#avatar_joueur').html("<img src='./img/" + player.avatar + "' />");
                        } else {
                            $('#pseudo_ennemie').html(player.pseudo);
                            $('#rank_ennemie').html("Elo : "+player.rank_point);
                            $('#avatar_ennemie').html("<img src='./img/" + player.avatar + "' />");    
                        }
                    });

                    let TurnData = response.game_info['turn_data']; //[3, 1]
                    let Turn =  response.game_info['turn']; //1
                    let PlayerPlaying = false;
                    TurnData = JSON.parse(TurnData);
                    if(TurnData && Turn){
                        if(Turn % 2 == 0){
                            PlayerPlaying = TurnData[1];
                        } else {
                            PlayerPlaying = TurnData[0];
                        }
                    }
                    if (PlayerPlaying == player_id) {
                        //QUAND ON JOUE
                        $('.map-interactive-area-finish-turn > img').css({
                            'filter': 'hue-rotate(327deg)',
                            'cursor': 'pointer'
                        });

                        // Activer l'effet hover pour le joueur actif
                        $('.map-interactive-area-finish-turn > img').off('mouseenter mouseleave').hover(function() {
                            $(this).css({
                                'transform': 'translateX(0%) rotate(360deg)',
                                'filter': 'hue-rotate(79deg)'
                            });
                        }, function() {
                            // Réinitialiser les styles lorsque la souris quitte l'élément
                            $(this).css({
                                'transform': 'unset',
                                'filter': 'hue-rotate(327deg)'
                            });
                        });
                        $('#finish_turn').off('click').click(function() {
                            var player_id = '<?php echo $_SESSION['user_id']; ?>';
                            var game_id = response.game_info['id'];
                            $('#skill_1_carte_player_1, #skill_2_carte_player_1, #skill_1_carte_player_2, #skill_2_carte_player_2, #skill_1_carte_player_3, #skill_2_carte_player_3, #skill_1_carte_player_4, #skill_2_carte_player_4').css({
                                'color': 'white',
                            });
                            $.ajax({
                                url: 'refresh_game.php',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    action: 'finish_turn',
                                    user_id: player_id,
                                    game_id: game_id
                                },
                                success: function(response) {
                                },
                                error: function(xhr, status, error) {
                                    console.error('Erreur:', status, error);
                                }
                            });
                        });

                        //GERER LE FIGHT
                        $.ajax({
                            url: 'fight_game.php',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                action: 'refresh',
                                user_id: player_id,
                                game_id: game_id
                            },
                            success: function(response) {
                            },
                            error: function(xhr, status, error) {
                                console.error('Erreur:', status, error);
                            }
                        });

                    } else {
                        $('.map-interactive-area-finish-turn > img').css({
                            'filter': 'grayscale(1)',
                            'cursor': 'not-allowed'
                        });

                        // Désactiver l'effet hover pour le joueur inactif
                        $('.map-interactive-area-finish-turn > img').off('mouseenter mouseleave').hover(function() {
                            $(this).css({
                                'transform': 'unset',
                                'filter': 'grayscale(1)'
                            });
                        });
                    }

                    if (response.cards_game) {

                        let playerCardIndex = 1;
                        let ennemyCardIndex = 1;
    
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

                        const DamageTypeValues = {
                            'Unknow': 'water.png',
                            'Fire': 'base_fire',
                            'Water': 'base_water',
                            'Earth': 'base_earth',
                            'Air': 'base_air',
                            'Dark': 'base_dark',
                            'Light': 'base_light',
                            // Ajoutez d'autres types de cartes ici
                        };

                        const EffectTypeValue = {
                            'POISON': 'poison.png',
                            'REGENERATE': 'heal.png',
                            'BLOCK_SKILL_1': 'block.png',
                            'BLOCK_SKILL_2': 'block.png',
                            'BLOCK': 'block.png',
                            // Ajoutez d'autres types de cartes ici
                        };

                        const EffectTypeColors = {
                            'POISON': 'purple',
                            'REGENERATE': 'green',
                            'BLOCK_SKILL_1': 'red',
                            'BLOCK_SKILL_2': 'red',
                            'BLOCK': 'red',
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

                        const EffectFightType = {
                            'DAMAGE': 'red',
                            'HEAL': 'green'
                        };

                        const EffectFightSuccessType = {
                            'PRECISION': 'orange',
                            'CRIT': 'purple'
                        };

                        response.cards_game.forEach(function(card) {
                            if (card.player_id == '<?php echo $_SESSION['user_id']; ?>') {
                                if (card.effect_fight) {
                                    const EffectFight = JSON.parse(card.effect_fight);
                                    const imgCartePlayer = document.getElementById('img_carte_player_' + playerCardIndex);

                                    EffectFight.forEach(effect_fight => {
                                        let Type = effect_fight.TYPE;
                                        let Value = effect_fight.VALUE;
                                        let Crit = effect_fight.CRIT;
                                        let Success = effect_fight.SUCCESS;

                                        const effectDiv = document.createElement('div');
                                        effectDiv.className = 'effect_fight';

                                        if (Type === "DAMAGE") {
                                            const swordImg = document.createElement('img');
                                            swordImg.src = "./img/sword.png";
                                            swordImg.className = 'sword-effect';
                                            swordImg.alt = "Épée";
                                            if(Success === true){
                                                if(Crit=== true) {
                                                    effectDiv.innerHTML = `<div class="crit_damage">Critical</div>`;
                                                } else {
                                                    effectDiv.innerHTML = `<div class="normal_damage">${Value}</div>`;
                                                }
                                            } else {
                                                effectDiv.innerHTML = `<div class="miss_damage">MISS</div>`;
                                            }

                                            effectDiv.appendChild(swordImg);

                                            setTimeout(() => {
                                                swordImg.classList.add('cut-animation');
                                            }, 0);
                                        }
                                        if($(".effect_fight").length == 0){
                                            // Si aucun effet similaire n'est déjà présent, ajouter l'effet sous l'image
                                            if (imgCartePlayer.nextElementSibling && !imgCartePlayer.nextElementSibling.classList.contains('effect_fight')) {
                                                imgCartePlayer.parentNode.insertBefore(effectDiv, imgCartePlayer.nextElementSibling);
                                            } else {
                                                imgCartePlayer.parentNode.appendChild(effectDiv);
                                            }
                                            $.ajax({
                                                url: 'refresh_game.php',
                                                type: 'POST',
                                                dataType: 'json',
                                                data: {
                                                    action: 'refresh_animation',
                                                    user_id: player_id,
                                                    game_id: game_id,
                                                    vue : "player"
                                                },
                                                success: function(response) {
                                                    setTimeout(function() {
                                                        $('.effect_fight').remove(); // Suppression de l'élément après 1 seconde (1000 millisecondes)
                                                    }, 1000);
                                                },
                                                error: function(xhr, status, error) {
                                                    console.error('Erreur:', status, error);
                                                }
                                            });
                                        }
                                    });
                                }
                                if(card.hp_left > 0){
                                    // Déterminer le filtre hue-rotate basé sur le type de carte
                                    const Image = ImageValues[card.type] || 'Unknow_bck.png'; // Utiliser 0deg par défaut si le type n'est pas défini
                                    const LogoType = LogoTypeValues[card.type] ||  'water.png';
                                    const effects = JSON.parse(card.affect_effect);
                                    const effectsContainer = document.getElementById('effects_carte_player_' + playerCardIndex);
                                    const skillDesc1 = JSON.parse(card.skill_1_effect); // Conversion de la chaîne JSON en objet
                                    const skillDesc2 = JSON.parse(card.skill_2_effect); // Conversion de la chaîne JSON en objet
                                    let DescSkill1Display = '';
                                    let DescSkill2Display = '';

                                    // Boucle à travers les effets pour construire l'affichage
                                    skillDesc1.forEach(effect => {
                                        if (DiffType.includes(effect.TYPE)) {
                                            let Duration = effect.DURATION ? effect.DURATION : 1;
                                            // Vérifier si TYPE est un effet de type "TRADE_BOOST"
                                            if (["TRADE_BOOST_FIRE", "TRADE_BOOST_WATER", "TRADE_BOOST_EARTH", "TRADE_BOOST_AIR", "TRADE_BOOST_DARK", "TRADE_BOOST_LIGHT"].includes(effect.TYPE)) {
                                                let element = effect.TYPE.replace('TRADE_BOOST_', '').toLowerCase().replace(/^./, char => char.toUpperCase());
                                                DescSkill1Display = 
                                                `<div class="skill_1_desc_tooltip">${card['skill_1_desc']}
                                                    <div class="${DamageTypeValues[card['type']]}">(<div class="${DamageTypeValues[element]}" style="display:contents;">${effect.PRICE}</div> - ${effect.DURATION})</div>
                                                    <div class="base_crit">
                                                        Crit : (${card.skill_1_crit + response.players_game.find(player => player.player_id === card.player_id)['base_crit']}%)
                                                    </div>
                                                    <div class="base_precision">
                                                        Réussite : (${card.skill_1_precision + response.players_game.find(player => player.player_id === card.player_id)['base_precision']}%)
                                                    </div>
                                                </div>`;
                                            } else if (["TRADE_BOOST"].includes(effect.TYPE)) {
                                                let element = effect.TYPE.replace('TRADE_BOOST_', '').toLowerCase().replace(/^./, char => char.toUpperCase());
                                                DescSkill1Display = 
                                                `<div class="skill_1_desc_tooltip">${card['skill_1_desc']}
                                                    <div class="${DamageTypeValues[card['type']]}">(<div class="${DamageTypeValues[element]}" style="display:contents;">${effect.PRICE}</div> - ${effect.DURATION})</div>
                                                    <div class="base_crit">
                                                        Crit : (${card.skill_1_crit + response.players_game.find(player => player.player_id === card.player_id)['base_crit']}%)
                                                    </div>
                                                    <div class="base_precision">
                                                        Réussite : (${card.skill_1_precision + response.players_game.find(player => player.player_id === card.player_id)['base_precision']}%)
                                                    </div>
                                                </div>`;
                                            } else {
                                                DescSkill1Display = 
                                                `<div class="skill_1_desc_tooltip">${card['skill_1_desc']}
                                                    <div class="${DamageTypeValues[card['type']]}">(${effect.DURATION})</div>
                                                    <div class="base_crit">
                                                        Crit : (${card.skill_1_crit+response.players_game.find(player => player.player_id === card.player_id)['base_crit']}%)
                                                    </div>
                                                    <div class="base_precision">
                                                        Réussite : (${card.skill_1_precision+response.players_game.find(player => player.player_id === card.player_id)['base_precision']}%)
                                                    </div>
                                                </div>`;
                                            }
                                        } else {
                                            DescSkill1Display = 
                                            `<div class="skill_1_desc_tooltip">${card['skill_1_desc']} 
                                                <div class="${DamageTypeValues[card['type']]}">
                                                    Dgts : (${card['skill_1_base_value']+response.players_game.find(player => player.player_id === card.player_id)[DamageTypeValues[card['type']]]})
                                                </div>
                                                <div class="base_crit">
                                                    Crit : (${card.skill_1_crit+response.players_game.find(player => player.player_id === card.player_id)['base_crit']}%)
                                                </div>
                                                <div class="base_precision">
                                                    Réussite : (${card.skill_1_precision+response.players_game.find(player => player.player_id === card.player_id)['base_precision']}%)
                                                </div>
                                            </div>`;
                                        }
                                    });

                                    skillDesc2.forEach(effect => {
                                        if (DiffType.includes(effect.TYPE)) {
                                            let Duration = effect.DURATION ? effect.DURATION : 1;
                                            // Vérifier si TYPE est un effet de type "TRADE_BOOST"
                                            if (["TRADE_BOOST_FIRE", "TRADE_BOOST_WATER", "TRADE_BOOST_EARTH", "TRADE_BOOST_AIR", "TRADE_BOOST_DARK", "TRADE_BOOST_LIGHT"].includes(effect.TYPE)) {
                                                let element = effect.TYPE.replace('TRADE_BOOST_', '').toLowerCase().replace(/^./, char => char.toUpperCase());
                                                DescSkill2Display = 
                                                `<div class="skill_2_desc_tooltip">${card['skill_2_desc']}
                                                    <div class="${DamageTypeValues[card['type']]}">(<div class="${DamageTypeValues[element]}" style="display:contents;">${effect.PRICE}</div> - ${effect.DURATION})</div>
                                                    <div class="base_crit">
                                                        Crit : (${card.skill_2_crit + response.players_game.find(player => player.player_id === card.player_id)['base_crit']}%)
                                                    </div>
                                                    <div class="base_precision">
                                                        Réussite : (${card.skill_2_precision + response.players_game.find(player => player.player_id === card.player_id)['base_precision']}%)
                                                    </div>
                                                </div>`;
                                            } else if (["TRADE_BOOST"].includes(effect.TYPE)) {
                                                let element = effect.TYPE.replace('TRADE_BOOST_', '').toLowerCase().replace(/^./, char => char.toUpperCase());
                                                DescSkill2Display = 
                                                `<div class="skill_2_desc_tooltip">${card['skill_2_desc']}
                                                    <div class="${DamageTypeValues[card['type']]}">(<div class="${DamageTypeValues[element]}" style="display:contents;">${effect.PRICE}</div> - ${effect.DURATION})</div>
                                                    <div class="base_crit">
                                                        Crit : (${card.skill_2_crit + response.players_game.find(player => player.player_id === card.player_id)['base_crit']}%)
                                                    </div>
                                                    <div class="base_precision">
                                                        Réussite : (${card.skill_2_precision + response.players_game.find(player => player.player_id === card.player_id)['base_precision']}%)
                                                    </div>
                                                </div>`;
                                            } else {
                                                DescSkill2Display = 
                                                `<div class="skill_2_desc_tooltip">${card['skill_2_desc']}
                                                    <div class="${DamageTypeValues[card['type']]}">(${effect.DURATION})</div>
                                                    <div class="base_crit">
                                                        Crit : (${card.skill_2_crit+response.players_game.find(player => player.player_id === card.player_id)['base_crit']}%)
                                                    </div>
                                                    <div class="base_precision">
                                                        Réussite : (${card.skill_2_precision+response.players_game.find(player => player.player_id === card.player_id)['base_precision']}%)
                                                    </div>
                                                </div>`;
                                            }
                                        } else {
                                            DescSkill2Display = 
                                            `<div class="skill_2_desc_tooltip">${card['skill_2_desc']} 
                                                <div class="${DamageTypeValues[card['type']]}">
                                                    Dgts : (${card['skill_2_base_value']+response.players_game.find(player => player.player_id === card.player_id)[DamageTypeValues[card['type']]]})
                                                </div>
                                                <div class="base_crit">
                                                    Crit : (${card.skill_2_crit+response.players_game.find(player => player.player_id === card.player_id)['base_crit']}%)
                                                </div>
                                                <div class="base_precision">
                                                    Réussite : (${card.skill_2_precision+response.players_game.find(player => player.player_id === card.player_id)['base_precision']}%)
                                                </div>
                                            </div>`;
                                        }
                                    });

                                    if (effects) {   
                                        effectsContainer.innerHTML = ''; // Vider le conteneur avant d'ajouter de nouveaux effets
                                        effects.forEach(function(effect) {
                                            // Créer une nouvelle div pour afficher l'effet
                                            const effectDiv = document.createElement('div');

                                            // Créer une image pour l'effet
                                            const effectImage = document.createElement('img');
                                            effectImage.src = `./img/${EffectTypeValue[effect['TYPE']]}`; // L'URL de l'image
                                            effectImage.className = 'effect'; // Ajouter la classe 'effect'
                                            effectImage.setAttribute('data-value', effect.VALUE);
                                            effectImage.setAttribute('data-duration', effect.DURATION);

                                            // Créer un span pour le tooltip
                                            const tooltip = document.createElement('span');
                                            tooltip.className = 'tooltip';
                                            tooltip.innerHTML = `
                                            <div class="effect_type_tooltip" style="color:${EffectTypeColors[effect['TYPE']]}">${effect['TYPE']}</div>
                                            <div class="effect_time_tooltip" style="color:${EffectTypeColors[effect['TYPE']]}">${effect.DURATION} Tour(s)</div>
                                            <div class="effect_value_tooltip" style="color:${EffectTypeColors[effect['TYPE']]}">Valeur : ${effect.VALUE}</div>`;

                                            // Ajouter l'image et le tooltip dans la div d'effet
                                            effectDiv.appendChild(effectImage);
                                            effectDiv.appendChild(tooltip);

                                            // Ajouter la div d'effet dans le conteneur des effets
                                            effectsContainer.appendChild(effectDiv);
                                        });
                                    }
                                    // Mettre à jour les éléments du joueur
                                    $('#type_carte_player_' + playerCardIndex).html("<img src='./img/"+LogoType+"'></img>"); // Type de carte
                                    $('#name_carte_player_' + playerCardIndex).html(card.name); // Nom de la carte
                                    $('#img_carte_player_' + playerCardIndex).css({
                                        'background-image': "url('./img/" + card.avatar + "')",
                                        'background-size': 'cover',
                                        'background-position': 'center',
                                        'margin-left': 'auto',
                                        'height': '18vh',
                                        'margin-right': 'auto',
                                        'box-shadow' : 'inset 0 0 10px 10px rgba(0, 0, 0, 0.7)'
                                    });
                                    $('#carte_player_' + playerCardIndex).css({
                                        'background-image': `url('./img/${Image}')`, // Appliquer le background
                                    });
                                    let skill = {};
                                    const style_inactive_1 = card.skill_1_active == 1 && card.skill_1_cooldown == 0 ? "" : "color:#c8c8c8;";
                                    const style_inactive_2 = card.skill_2_active == 1 && card.skill_2_cooldown == 0 ? "" : "color:#c8c8c8;";
                                    skill[`skill_1_carte_player_${playerCardIndex}`] = 
                                        `<div id="cooldown_skill_1_carte_player_${playerCardIndex}">
                                            <div style="display: flex; align-items: center;">
                                                <img src="./img/cooldown_${card.skill_1_cooldown}.png" alt="Cooldown ${card.skill_1_cooldown}">
                                                <span style="margin-left: 10px; ${style_inactive_1}">${card.skill_1_name}</span>
                                            </div>
                                        </div>
                                        <div class="tooltipskill1">
                                            ${DescSkill1Display}
                                        </div>`;
                                    if(skill[`skill_1_carte_player_${playerCardIndex}`] !== $('#skill_1_carte_player_' + playerCardIndex).html()){
                                        $('#skill_1_carte_player_' + playerCardIndex).html(skill[`skill_1_carte_player_${playerCardIndex}`]);
                                    }

                                    skill[`skill_2_carte_player_${playerCardIndex}`] = 
                                        `<div id="cooldown_skill_2_carte_player_${playerCardIndex}">
                                            <div style="display: flex; align-items: center;">
                                                <img src="./img/cooldown_${card.skill_2_cooldown}.png" alt="Cooldown ${card.skill_2_cooldown}">
                                                <span style="margin-left: 10px; ${style_inactive_2}">${card.skill_2_name}</span>
                                            </div>
                                        </div>
                                        <div class="tooltipskill2">
                                            ${DescSkill2Display}
                                        </div>`;
                                    if(skill[`skill_2_carte_player_${playerCardIndex}`] !== $('#skill_2_carte_player_' + playerCardIndex).html()){
                                        $('#skill_2_carte_player_' + playerCardIndex).html(skill[`skill_2_carte_player_${playerCardIndex}`]);
                                    }
                                
                                    var hpLeft = card.hp_left;
                                    var hpMax = card.hp_max;
                                    // Créer ou mettre à jour la barre de vie pour un ennemi spécifique
                                    const healthBar = $('#hp-bar-player-' + playerCardIndex);

                                    if (healthBar.length === 0) {
                                        // Créer la barre de vie si elle n'existe pas
                                        const healthBarContainer = `<div class="health-bar-container">
                                                                        <div id="hp-bar-player-${playerCardIndex}" class="health-bar"></div>
                                                                        <div class="health-bar-text" id="hp-text-player-${playerCardIndex}">${hpLeft} / ${hpMax}</div>
                                                                    </div>`;
                                        $('#hp_carte_player_' + playerCardIndex).before(healthBarContainer);
                                    }

                                    // Calculer le pourcentage de HP et mettre à jour la barre
                                    const hpPercentage = (hpLeft / hpMax) * 100;
                                    $('#hp-bar-player-' + playerCardIndex).css({
                                        'width': hpPercentage + '%',
                                        'background-color': hpPercentage > 50 ? '#4caf50' : (hpPercentage > 20 ? '#ffeb3b' : '#f44336'), // Couleur en fonction du pourcentage de vie
                                    });

                                    // Mise à jour du texte des HP (le texte reste au centre et ne bouge pas)
                                    $('#hp-text-player-' + playerCardIndex).html(hpLeft + " / " + hpMax);
                                } else {
                                    $('#carte_player_' + playerCardIndex).hide();
                                }
                                playerCardIndex++; // Incrémentez l'index pour la prochaine carte du joueur
        
                            } else {
                                if (card.effect_fight) {
                                    const EffectFight = JSON.parse(card.effect_fight); // Conversion de la chaîne JSON en objet
                                    const imgCarteEnnemy = document.getElementById('img_carte_ennemy_' + ennemyCardIndex);

                                    EffectFight.forEach(effect_fight => {
                                        let Type = effect_fight.TYPE;
                                        let Value = effect_fight.VALUE; // Par exemple, cela vaut 10
                                        let Crit = effect_fight.CRIT;
                                        let Success = effect_fight.SUCCESS;

                                        // Créer un nouvel élément pour afficher l'effet
                                        const effectDiv = document.createElement('div');
                                        effectDiv.className = 'effect_fight'; // Ajoutez une classe pour le style si nécessaire

                                        if (Type === "DAMAGE") {
                                            // Créer l'élément d'épée
                                            const swordImg = document.createElement('img');
                                            swordImg.src = "./img/sword.png"; // Chemin vers l'image de l'épée
                                            swordImg.className = 'sword-effect'; // Classe pour l'effet de coupe
                                            swordImg.alt = "Épée";
                                            if(Success == true){
                                                if(Crit == true) {
                                                    effectDiv.innerHTML = `<div class="crit_damage">Critical</div>`;
                                                } else {
                                                    effectDiv.innerHTML = `<div class="normal_damage">${Value}</div>`;
                                                }
                                            } else {
                                                effectDiv.innerHTML = `<div class="miss_damage">MISS</div>`;
                                            }

                                            effectDiv.appendChild(swordImg);

                                            // Démarrer l'animation après l'ajout de l'effet
                                            setTimeout(() => {
                                                swordImg.classList.add('cut-animation'); // Ajouter la classe d'animation
                                            }, 0);
                                        }
                                        if($(".effect_fight").length == 0){
                                            // Si aucun effet similaire n'est déjà présent, ajouter l'effet sous l'image
                                            if (imgCarteEnnemy.nextElementSibling && !imgCarteEnnemy.nextElementSibling.classList.contains('effect_fight')) {
                                                imgCarteEnnemy.parentNode.insertBefore(effectDiv, imgCarteEnnemy.nextElementSibling);
                                            } else {
                                                imgCarteEnnemy.parentNode.appendChild(effectDiv);
                                            }
                                            $.ajax({
                                                url: 'refresh_game.php',
                                                type: 'POST',
                                                dataType: 'json',
                                                data: {
                                                    action: 'refresh_animation',
                                                    user_id: player_id,
                                                    game_id: game_id,
                                                    vue : "ennemy"
                                                },
                                                success: function(response) {
                                                    setTimeout(function() {
                                                        $('.effect_fight').remove(); // Suppression de l'élément après 1 seconde (1000 millisecondes)
                                                    }, 1000);
                                                },
                                                error: function(xhr, status, error) {
                                                    console.error('Erreur:', status, error);
                                                }
                                            });
                                        }
                                    });
                                }
                                if (card.hp_left > 0) {
                                    // Sinon, c'est l'ennemi
                                    // Déterminer le filtre hue-rotate basé sur le type de carte
                                    const Image = ImageValues[card.type] || 'Unknow_bck.png'; // Utiliser 0deg par défaut si le type n'est pas défini
                                    const LogoType = LogoTypeValues[card.type] ||  'water.png';
                                    const effects = JSON.parse(card.affect_effect);
                                    const effectsContainer = document.getElementById('effects_carte_ennemy_' + ennemyCardIndex);
                                    const skillDesc1 = JSON.parse(card.skill_1_effect); // Conversion de la chaîne JSON en objet
                                    const skillDesc2 = JSON.parse(card.skill_2_effect); // Conversion de la chaîne JSON en objet
                                    let DescSkill1Display = '';
                                    let DescSkill2Display = '';

                                    // Boucle à travers les effets pour construire l'affichage
                                    skillDesc1.forEach(effect => {
                                        if (DiffType.includes(effect.TYPE)) {
                                            let Duration = effect.DURATION ? effect.DURATION : 1;
                                            // Vérifier si TYPE est un effet de type "TRADE_BOOST"
                                            if (["TRADE_BOOST_FIRE", "TRADE_BOOST_WATER", "TRADE_BOOST_EARTH", "TRADE_BOOST_AIR", "TRADE_BOOST_DARK", "TRADE_BOOST_LIGHT"].includes(effect.TYPE)) {
                                                let element = effect.TYPE.replace('TRADE_BOOST_', '').toLowerCase().replace(/^./, char => char.toUpperCase());
                                                DescSkill1Display = 
                                                `<div class="skill_1_desc_tooltip">${card['skill_1_desc']}
                                                    <div class="${DamageTypeValues[card['type']]}">(<div class="${DamageTypeValues[element]}" style="display:contents;">${effect.PRICE}</div> - ${effect.DURATION})</div>
                                                    <div class="base_crit">
                                                        Crit : (${card.skill_1_crit + response.players_game.find(player => player.player_id === card.player_id)['base_crit']}%)
                                                    </div>
                                                    <div class="base_precision">
                                                        Réussite : (${card.skill_1_precision + response.players_game.find(player => player.player_id === card.player_id)['base_precision']}%)
                                                    </div>
                                                </div>`;
                                            } else if (["TRADE_BOOST"].includes(effect.TYPE)) {
                                                let element = effect.TYPE.replace('TRADE_BOOST_', '').toLowerCase().replace(/^./, char => char.toUpperCase());
                                                DescSkill1Display = 
                                                `<div class="skill_1_desc_tooltip">${card['skill_1_desc']}
                                                    <div class="${DamageTypeValues[card['type']]}">(<div class="${DamageTypeValues[element]}" style="display:contents;">${effect.PRICE}</div> - ${effect.DURATION})</div>
                                                    <div class="base_crit">
                                                        Crit : (${card.skill_1_crit + response.players_game.find(player => player.player_id === card.player_id)['base_crit']}%)
                                                    </div>
                                                    <div class="base_precision">
                                                        Réussite : (${card.skill_1_precision + response.players_game.find(player => player.player_id === card.player_id)['base_precision']}%)
                                                    </div>
                                                </div>`;
                                            } else {
                                                DescSkill1Display = 
                                                `<div class="skill_1_desc_tooltip">${card['skill_1_desc']}
                                                    <div class="${DamageTypeValues[card['type']]}">(${effect.DURATION})</div>
                                                    <div class="base_crit">
                                                        Crit : (${card.skill_1_crit+response.players_game.find(player => player.player_id === card.player_id)['base_crit']}%)
                                                    </div>
                                                    <div class="base_precision">
                                                        Réussite : (${card.skill_1_precision+response.players_game.find(player => player.player_id === card.player_id)['base_precision']}%)
                                                    </div>
                                                </div>`;
                                            }
                                        } else {
                                            DescSkill1Display = 
                                            `<div class="skill_1_desc_tooltip">${card['skill_1_desc']} 
                                                <div class="${DamageTypeValues[card['type']]}">
                                                    Dgts : (${card['skill_1_base_value']+response.players_game.find(player => player.player_id === card.player_id)[DamageTypeValues[card['type']]]})
                                                </div>
                                                <div class="base_crit">
                                                    Crit : (${card.skill_1_crit+response.players_game.find(player => player.player_id === card.player_id)['base_crit']}%)
                                                </div>
                                                <div class="base_precision">
                                                    Réussite : (${card.skill_1_precision+response.players_game.find(player => player.player_id === card.player_id)['base_precision']}%)
                                                </div>
                                            </div>`;
                                        }
                                    });

                                    skillDesc2.forEach(effect => {
                                        if (DiffType.includes(effect.TYPE)) {
                                            let Duration = effect.DURATION ? effect.DURATION : 1;
                                            // Vérifier si TYPE est un effet de type "TRADE_BOOST"
                                            if (["TRADE_BOOST_FIRE", "TRADE_BOOST_WATER", "TRADE_BOOST_EARTH", "TRADE_BOOST_AIR", "TRADE_BOOST_DARK", "TRADE_BOOST_LIGHT"].includes(effect.TYPE)) {
                                                let element = effect.TYPE.replace('TRADE_BOOST_', '').toLowerCase().replace(/^./, char => char.toUpperCase());
                                                DescSkill2Display = 
                                                `<div class="skill_2_desc_tooltip">${card['skill_2_desc']}
                                                    <div class="${DamageTypeValues[card['type']]}">(<div class="${DamageTypeValues[element]}" style="display:contents;">${effect.PRICE}</div> - ${effect.DURATION})</div>
                                                    <div class="base_crit">
                                                        Crit : (${card.skill_2_crit + response.players_game.find(player => player.player_id === card.player_id)['base_crit']}%)
                                                    </div>
                                                    <div class="base_precision">
                                                        Réussite : (${card.skill_2_precision + response.players_game.find(player => player.player_id === card.player_id)['base_precision']}%)
                                                    </div>
                                                </div>`;
                                            } else if (["TRADE_BOOST"].includes(effect.TYPE)) {
                                                let element = effect.TYPE.replace('TRADE_BOOST_', '').toLowerCase().replace(/^./, char => char.toUpperCase());
                                                DescSkill2Display = 
                                                `<div class="skill_2_desc_tooltip">${card['skill_2_desc']}
                                                    <div class="${DamageTypeValues[card['type']]}">(<div class="${DamageTypeValues[element]}" style="display:contents;">${effect.PRICE}</div> - ${effect.DURATION})</div>
                                                    <div class="base_crit">
                                                        Crit : (${card.skill_2_crit + response.players_game.find(player => player.player_id === card.player_id)['base_crit']}%)
                                                    </div>
                                                    <div class="base_precision">
                                                        Réussite : (${card.skill_2_precision + response.players_game.find(player => player.player_id === card.player_id)['base_precision']}%)
                                                    </div>
                                                </div>`;
                                            } else {
                                                DescSkill2Display = 
                                                `<div class="skill_2_desc_tooltip">${card['skill_2_desc']}
                                                    <div class="${DamageTypeValues[card['type']]}">(${effect.DURATION})</div>
                                                    <div class="base_crit">
                                                        Crit : (${card.skill_2_crit+response.players_game.find(player => player.player_id === card.player_id)['base_crit']}%)
                                                    </div>
                                                    <div class="base_precision">
                                                        Réussite : (${card.skill_2_precision+response.players_game.find(player => player.player_id === card.player_id)['base_precision']}%)
                                                    </div>
                                                </div>`;
                                            }
                                        } else {
                                            DescSkill2Display = 
                                            `<div class="skill_2_desc_tooltip">${card['skill_2_desc']} 
                                                <div class="${DamageTypeValues[card['type']]}">
                                                    Dgts : (${card['skill_2_base_value']+response.players_game.find(player => player.player_id === card.player_id)[DamageTypeValues[card['type']]]})
                                                </div>
                                                <div class="base_crit">
                                                    Crit : (${card.skill_2_crit+response.players_game.find(player => player.player_id === card.player_id)['base_crit']}%)
                                                </div>
                                                <div class="base_precision">
                                                    Réussite : (${card.skill_2_precision+response.players_game.find(player => player.player_id === card.player_id)['base_precision']}%)
                                                </div>
                                            </div>`;
                                        }
                                    });

                                    if (effects) {
                                        effectsContainer.innerHTML = ''; // Vider le conteneur avant d'ajouter de nouveaux effets
                                        effects.forEach(function(effect) {
                                            // Créer une nouvelle div pour afficher l'effet
                                            const effectDiv = document.createElement('div');

                                            // Créer une image pour l'effet
                                            const effectImage = document.createElement('img');
                                            effectImage.src = `./img/${EffectTypeValue[effect['TYPE']]}`; // L'URL de l'image
                                            effectImage.className = 'effect'; // Ajouter la classe 'effect'
                                            effectImage.setAttribute('data-value', effect.VALUE);
                                            effectImage.setAttribute('data-duration', effect.DURATION);

                                            // Créer un span pour le tooltip
                                            const tooltip = document.createElement('span');
                                            tooltip.className = 'tooltip';
                                            tooltip.innerHTML = `
                                            <div class="effect_type_tooltip" style="color:${EffectTypeColors[effect['TYPE']]}">${effect['TYPE']}</div>
                                            <div class="effect_time_tooltip" style="color:${EffectTypeColors[effect['TYPE']]}">${effect.DURATION} Tour(s)</div>
                                            <div class="effect_value_tooltip" style="color:${EffectTypeColors[effect['TYPE']]}">Valeur : ${effect.VALUE}</div>`;

                                            // Ajouter l'image et le tooltip dans la div d'effet
                                            effectDiv.appendChild(effectImage);
                                            effectDiv.appendChild(tooltip);

                                            // Ajouter la div d'effet dans le conteneur des effets
                                            effectsContainer.appendChild(effectDiv);
                                        });
                                    }
                                    $('#type_carte_ennemy_' + ennemyCardIndex).html("<img src='./img/"+LogoType+"'></img>"); // Type de carte
                                    $('#name_carte_ennemy_' + ennemyCardIndex).html(card.name); // Nom de la carte
                                    $('#img_carte_ennemy_' + ennemyCardIndex).css({
                                        'background-image': "url('./img/" + card.avatar + "')",
                                        'background-size': 'cover',
                                        'background-position': 'center',
                                        'margin-left': 'auto',
                                        'height': '18vh',
                                        'margin-right': 'auto',
                                        'box-shadow' : 'inset 0 0 10px 10px rgba(0, 0, 0, 0.7)'
                                    });
                                    $('#carte_ennemy_' + ennemyCardIndex).css({
                                        'background-image': `url('./img/${Image}')`, // Appliquer le background
                                    });
                                    let skill = {};
                                    const style_inactive_1 = card.skill_1_active == 1 && card.skill_1_cooldown == 0 ? "" : "color:#c8c8c8;";
                                    const style_inactive_2 = card.skill_2_active == 1 && card.skill_2_cooldown == 0 ? "" : "color:#c8c8c8;";
                                    skill[`skill_1_carte_ennemy_${ennemyCardIndex}`] = 
                                        `<div id="cooldown_skill_1_carte_ennemy_${ennemyCardIndex}">
                                            <div style="display: flex; align-items: center;">
                                                <img src="./img/cooldown_${card.skill_1_cooldown}.png" alt="Cooldown ${card.skill_1_cooldown}">
                                                <span style="margin-left: 10px; ${style_inactive_1}">${card.skill_1_name}</span>
                                            </div>
                                        </div>
                                        <div class="tooltipskill1">
                                            ${DescSkill1Display}
                                        </div>`;
                                    if(skill[`skill_1_carte_ennemy_${ennemyCardIndex}`] !== $('#skill_1_carte_ennemy_' + ennemyCardIndex).html()){
                                        $('#skill_1_carte_ennemy_' + ennemyCardIndex).html(skill[`skill_1_carte_ennemy_${ennemyCardIndex}`]);
                                    }
                                    skill[`skill_2_carte_ennemy_${ennemyCardIndex}`] = 
                                        `<div id="cooldown_skill_2_carte_ennemy_${ennemyCardIndex}">
                                            <div style="display: flex; align-items: center;">
                                                <img src="./img/cooldown_${card.skill_2_cooldown}.png" alt="Cooldown ${card.skill_2_cooldown}">
                                                <span style="margin-left: 10px; ${style_inactive_2}">${card.skill_2_name}</span>
                                            </div>
                                        </div>
                                        <div class="tooltipskill2">
                                            ${DescSkill2Display}
                                        </div>`;
                                    if(skill[`skill_2_carte_ennemy_${ennemyCardIndex}`] !== $('#skill_2_carte_ennemy_' + ennemyCardIndex).html()){
                                        $('#skill_2_carte_ennemy_' + ennemyCardIndex).html(skill[`skill_2_carte_ennemy_${ennemyCardIndex}`]);
                                    }
                                    var hpLeft = card.hp_left;
                                    var hpMax = card.hp_max;
                                    const healthBar = $('#hp-bar-ennemy-' + ennemyCardIndex);

                                    if (healthBar.length === 0) {
                                        // Créer la barre de vie si elle n'existe pas
                                        const healthBarContainer = `<div class="health-bar-container">
                                                                        <div id="hp-bar-ennemy-${ennemyCardIndex}" class="health-bar"></div>
                                                                        <div class="health-bar-text" id="hp-text-ennemy-${ennemyCardIndex}">${hpLeft} / ${hpMax}</div>
                                                                    </div>`;
                                        $('#hp_carte_ennemy_' + ennemyCardIndex).before(healthBarContainer);
                                    }

                                    // Calculer le pourcentage de HP et mettre à jour la barre
                                    const hpPercentage = (hpLeft / hpMax) * 100;
                                    $('#hp-bar-ennemy-' + ennemyCardIndex).css({
                                        'width': hpPercentage + '%',
                                        'background-color': hpPercentage > 50 ? '#4caf50' : (hpPercentage > 20 ? '#ffeb3b' : '#f44336'), // Couleur en fonction du pourcentage de vie
                                    });

                                    // Mise à jour du texte des HP (le texte reste au centre et ne bouge pas)
                                    $('#hp-text-ennemy-' + ennemyCardIndex).html(hpLeft + " / " + hpMax);
                                } else {
                                    $('#carte_ennemy_' + ennemyCardIndex).hide();
                                }
                                ennemyCardIndex++; // Incrémentez l'index pour la prochaine carte de l'ennemi
                            }
                        });
                    }
                    if (response.players_game) {
                        response.players_game.forEach(function(player) {
                            // Vérifier si c'est le joueur actuel
                            if (player.player_id == '<?php echo $_SESSION['user_id']; ?>') {
                                if ($('#value_player_fire').html() != player.base_fire) {
                                    $('#value_player_fire').html(player.base_fire);
                                    $('#value_content_player_fire').css('animation', 'none');
                                    $('#value_content_player_fire')[0].offsetHeight;
                                    $('#value_content_player_fire').css('animation', 'popLight 1s ease-in-out forwards');
                                }
                                if ($('#value_player_water').html() != player.base_water) {
                                    $('#value_player_water').html(player.base_water);
                                    $('#value_content_player_water').css('animation', 'none');
                                    $('#value_content_player_water')[0].offsetHeight;
                                    $('#value_content_player_water').css('animation', 'popLight 1s ease-in-out forwards');
                                }
                                if ($('#value_player_earth').html() != player.base_earth) {
                                    $('#value_player_earth').html(player.base_earth);
                                    $('#value_content_player_earth').css('animation', 'none');
                                    $('#value_content_player_earth')[0].offsetHeight;
                                    $('#value_content_player_earth').css('animation', 'popLight 1s ease-in-out forwards');
                                }
                                if ($('#value_player_air').html() != player.base_air) {
                                    $('#value_player_air').html(player.base_air);
                                    $('#value_content_player_air').css('animation', 'none');
                                    $('#value_content_player_air')[0].offsetHeight;
                                    $('#value_content_player_air').css('animation', 'popLight 1s ease-in-out forwards');
                                }
                                if ($('#value_player_dark').html() != player.base_dark) {
                                    $('#value_player_dark').html(player.base_dark);
                                    $('#value_content_player_dark').css('animation', 'none');
                                    $('#value_content_player_dark')[0].offsetHeight;
                                    $('#value_content_player_dark').css('animation', 'popLight 1s ease-in-out forwards');
                                }
                                if ($('#value_player_light').html() != player.base_light) {
                                    $('#value_player_light').html(player.base_light);
                                    $('#value_content_player_light').css('animation', 'none');
                                    $('#value_content_player_light')[0].offsetHeight;
                                    $('#value_content_player_light').css('animation', 'popLight 1s ease-in-out forwards');
                                }
                            } else {
                                if ($('#value_ennemy_fire').html() != player.base_fire) {
                                    $('#value_ennemy_fire').html(player.base_fire);
                                    $('#value_content_ennemy_fire').css('animation', 'none');
                                    $('#value_content_ennemy_fire')[0].offsetHeight;
                                    $('#value_content_ennemy_fire').css('animation', 'popLight 1s ease-in-out forwards');
                                }
                                if ($('#value_ennemy_water').html() != player.base_water) {
                                    $('#value_ennemy_water').html(player.base_water);
                                    $('#value_content_ennemy_water').css('animation', 'none');
                                    $('#value_content_ennemy_water')[0].offsetHeight;
                                    $('#value_content_ennemy_water').css('animation', 'popLight 1s ease-in-out forwards');
                                }
                                if ($('#value_ennemy_earth').html() != player.base_earth) {
                                    $('#value_ennemy_earth').html(player.base_earth);
                                    $('#value_content_ennemy_earth').css('animation', 'none');
                                    $('#value_content_ennemy_earth')[0].offsetHeight;
                                    $('#value_content_ennemy_earth').css('animation', 'popLight 1s ease-in-out forwards');
                                }
                                if ($('#value_ennemy_air').html() != player.base_air) {
                                    $('#value_ennemy_air').html(player.base_air);
                                    $('#value_content_ennemy_air').css('animation', 'none');
                                    $('#value_content_ennemy_air')[0].offsetHeight;
                                    $('#value_content_ennemy_air').css('animation', 'popLight 1s ease-in-out forwards');
                                }
                                if ($('#value_ennemy_dark').html() != player.base_dark) {
                                    $('#value_ennemy_dark').html(player.base_dark);
                                    $('#value_content_ennemy_dark').css('animation', 'none');
                                    $('#value_content_ennemy_dark')[0].offsetHeight;
                                    $('#value_content_ennemy_dark').css('animation', 'popLight 1s ease-in-out forwards');
                                }
                                if ($('#value_ennemy_light').html() != player.base_light) {
                                    $('#value_ennemy_light').html(player.base_light);
                                    $('#value_content_ennemy_light').css('animation', 'none');
                                    $('#value_content_ennemy_light')[0].offsetHeight;
                                    $('#value_content_ennemy_light').css('animation', 'popLight 1s ease-in-out forwards');
                                }
                            }
                        });
                    }
                } else {
                    window.location.replace("./");
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur:', status, error);
            }
        });
    }
    //QUAND ON CLICK SUR UN DE NOS SORT
    $('.skill_1_carte_player, .skill_2_carte_player').off('click').click(function() {
        var sort = $(this).attr('id');
        var game_id = '<?php echo $_GET['game_id']; ?>';
        var player_id = '<?php echo $_SESSION['user_id']; ?>';
        $.ajax({
            url: 'use_skill.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'choice_skill',
                user_id: player_id,
                game_id: game_id,
                sort: sort
            },
            success: function(response) {
                var UseSkill = response.use_skill;
                if(UseSkill){
                    var card = response.card_id;
                    var skill = response.numero_skill;
                    var div_skill = $(`#${sort}`);
                    var card_info = response.card_info;
                    var skill_use = skill == 1 ? card_info['skill_1_active'] : card_info['skill_2_active'];
                    var skill_cooldown_use = skill == 1 ? card_info['skill_1_cooldown'] : card_info['skill_2_cooldown'];
                    if(card_info['active'] == 1 && card_info['hp_left'] > 0 && card_info['active'] == 1 && skill_use == 1 && skill_cooldown_use == 0){
                        var type_skill = response.card_info["skill_"+skill+"_target_type"];
                        var targets_skill = response.card_info["skill_"+skill+"_targets"];
                        if(targets_skill == 1 && type_skill == "ENNEMIE") {
                            if(TabTarget.length == 0){
                                TabTarget.push(card, skill, "ENNEMIE");
                                div_skill.css({
                                    'color': 'red',
                                });
                            } else {
                                $('#skill_1_carte_player_1, #skill_2_carte_player_1, #skill_1_carte_player_2, #skill_2_carte_player_2, #skill_1_carte_player_3, #skill_2_carte_player_3, #skill_1_carte_player_4, #skill_2_carte_player_4').css({
                                    'color': 'white',
                                });
                                TabTarget=[];
                                TabTarget.push(card, skill, "ENNEMIE");
                                div_skill.css({
                                    'color': 'red',
                                });
                            }
                            TabTarget.push($(this).attr('id'));
                            $.ajax({
                                url: 'use_skill.php',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    action: 'use_skill_ennemie',
                                    user_id: player_id,
                                    game_id: game_id,
                                    TabTarget: TabTarget
                                },
                                success: function(response) {
                                    TabTarget=[];
                                    div_skill.css({
                                        'color': 'white',
                                    });
                                },
                                error: function(xhr, status, error) {
                                    console.error('Erreur:', status, error);
                                }
                            });
                        } else if(targets_skill == 1 && type_skill == "ENNEMIE_CARDS"){
                            if(TabTarget.length == 0){
                                TabTarget.push(card, skill, "ENNEMIE_CARDS");
                                div_skill.css({
                                    'color': 'red',
                                });
                            } else {
                                if(TabTarget[0] == card && TabTarget[1] == skill){
                                    TabTarget=[];
                                    div_skill.css({
                                        'color': 'white',
                                    });
                                } else {
                                    $('#skill_1_carte_player_1, #skill_2_carte_player_1, #skill_1_carte_player_2, #skill_2_carte_player_2, #skill_1_carte_player_3, #skill_2_carte_player_3, #skill_1_carte_player_4, #skill_2_carte_player_4').css({
                                        'color': 'white',
                                    });
                                    TabTarget=[];
                                    TabTarget.push(card, skill, "ENNEMIE_CARDS");
                                    div_skill.css({
                                        'color': 'red',
                                    });
                                }
                            }
                        } else if(targets_skill == 1 && type_skill == "ALLIE") {
                            if(TabTarget.length == 0){
                                TabTarget.push(card, skill, "ALLIE");
                                div_skill.css({
                                    'color': 'green',
                                });
                            } else {
                                $('#skill_1_carte_player_1, #skill_2_carte_player_1, #skill_1_carte_player_2, #skill_2_carte_player_2, #skill_1_carte_player_3, #skill_2_carte_player_3, #skill_1_carte_player_4, #skill_2_carte_player_4').css({
                                        'color': 'white',
                                });
                                TabTarget=[];
                                TabTarget.push(card, skill, "ALLIE");
                                div_skill.css({
                                    'color': 'green',
                                });
                            }
                            TabTarget.push($(this).attr('id'));
                            $.ajax({
                                url: 'use_skill.php',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    action: 'use_skill_allie',
                                    user_id: player_id,
                                    game_id: game_id,
                                    TabTarget: TabTarget
                                },
                                success: function(response) {
                                    TabTarget=[];
                                    div_skill.css({
                                        'color': 'white',
                                    });
                                },
                                error: function(xhr, status, error) {
                                    console.error('Erreur:', status, error);
                                }
                            });
                        } else if(targets_skill == 1 && type_skill == "ALLIE_CARDS"){
                            if(TabTarget.length == 0){
                                TabTarget.push(card, skill, "ALLIE_CARDS");
                                div_skill.css({
                                    'color': 'green',
                                });
                            } else {
                                if(TabTarget[0] == card && TabTarget[1] == skill){
                                    TabTarget=[];
                                    div_skill.css({
                                        'color': 'white',
                                    });
                                } else {
                                    $('#cooldown_skill_1_carte_player_1, #cooldown_skill_2_carte_player_1, #cooldown_skill_1_carte_player_2, #cooldown_skill_2_carte_player_2, #cooldown_skill_1_carte_player_3, #cooldown_skill_2_carte_player_3, #cooldown_skill_1_carte_player_4, #cooldown_skill_2_carte_player_4').css({
                                        'color': 'white',
                                    });
                                    TabTarget=[];
                                    TabTarget.push(card, skill, "ALLIE_CARDS");
                                    div_skill.css({
                                        'color': 'green',
                                    });
                                }
                            }
                        }
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur:', status, error);
            }
        });
    });

    
    $('#carte_ennemy_1, #carte_ennemy_2, #carte_ennemy_3, #carte_ennemy_4').off('click').click(function() {
        if (TabTarget.includes("ENNEMIE_CARDS")) {
            var game_id = '<?php echo $_GET['game_id']; ?>';
            var player_id = '<?php echo $_SESSION['user_id']; ?>';
            TabTarget.push($(this).attr('id'));
            $.ajax({
                url: 'use_skill.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'use_skill_ennemie_cards',
                    user_id: player_id,
                    game_id: game_id,
                    TabTarget: TabTarget
                },
                success: function(response) {
                    TabTarget=[];
                    $('#skill_1_carte_player_1, #skill_2_carte_player_1, #skill_1_carte_player_2, #skill_2_carte_player_2, #skill_1_carte_player_3, #skill_2_carte_player_3, #skill_1_carte_player_4, #skill_2_carte_player_4').css({
                        'color': 'white',
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Erreur:', status, error);
                }
            });
        }
    });

    $('#carte_player_1, #carte_player_2, #carte_player_3, #carte_player_4').off('click').click(function() {
        if (TabTarget.includes("ALLIE_CARDS")) {
            var game_id = '<?php echo $_GET['game_id']; ?>';
            var player_id = '<?php echo $_SESSION['user_id']; ?>';
            TabTarget.push($(this).attr('id'));
            $.ajax({
                url: 'use_skill.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'use_skill_allie_cards',
                    user_id: player_id,
                    game_id: game_id,
                    TabTarget: TabTarget
                },
                success: function(response) {
                    TabTarget=[];
                    $('#skill_1_carte_player_1, #skill_2_carte_player_1, #skill_1_carte_player_2, #skill_2_carte_player_2, #skill_1_carte_player_3, #skill_2_carte_player_3, #skill_1_carte_player_4, #skill_2_carte_player_4').css({
                        'color': 'white',
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Erreur:', status, error);
                }
            });
        }
    });

    RefreshGame();
    setInterval(function() {
        RefreshGame(); // Recharger les parties toutes les 2 secondes (2000 ms)
    }, 500); // Répéter toutes les 2 secondes (2000 ms)
});
</script>