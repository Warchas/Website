<?php
if (!defined('IN_PHPBB')) {
    die("You do not have permission to access this file.");
}

$skill_array = array('attack', 'strength', 'defense', 'hits', 'ranged', 'prayer', 'magic', 'cooking', 'woodcut', 'fletching', 'fishing', 'firemaking', 'crafting', 'smithing', 'mining', 'herblaw', 'agility', 'thieving');

function buildSQLArray($array)
{
    $SQLarray = '';
    $size = sizeof($array) - 1;
    $i = 0;
    while ($i <= $size) {
        $SQLarray .= ($array[$i] == 'total_lvl') ? '' : (($array[$i] == 'hitpoints') ? 'exp_hits,' : 'exp_' . $array[$i] . '' . (($i == $size) ? '' : ',') . '');
        $i++;
    }
    return $SQLarray;
}

$connector = new Dbc();

$subpage = 'shar';
$skills = buildSQLArray($skill_array);

$character_result = $connector->gamequery("SELECT " . $skills . ", openrsc_players.* FROM openrsc_experience LEFT JOIN openrsc_players ON openrsc_experience.playerID = openrsc_players.id WHERE (openrsc_players.id = '$subpage' OR openrsc_players.username = '$subpage')");
$character = $connector->fetchArray($character_result);

$totalTime = $connector->gamequery("SELECT SUM(`value`) FROM openrsc_player_cache AS B LEFT JOIN openrsc_players AS A ON B.playerID = A.id WHERE (A.id = '$subpage' OR A.username = '$subpage') AND B.key = 'total_played'");

$player_logins = $connector->gamequery("SELECT * FROM openrsc_logins AS B LEFT JOIN openrsc_players AS A ON B.playerID = A.id WHERE (A.id = '$subpage' OR A.username = '$subpage') ORDER BY 'B.time' DESC LIMIT 30");

$player_chatlogs = $connector->gamequery("SELECT * FROM openrsc_chat_logs AS B LEFT JOIN openrsc_players AS A ON B.sender = A.username WHERE (A.id = '$subpage' OR A.username = '$subpage') ORDER BY 'B.time' DESC LIMIT 30");

$player_pmlogs = $connector->gamequery("SELECT * FROM openrsc_private_message_logs AS B LEFT JOIN openrsc_players AS A ON B.sender = A.username OR B.reciever = A.username WHERE (A.id = '$subpage' OR A.username = '$subpage') ORDER BY 'B.time' DESC LIMIT 30");

$player_tradelogs = $connector->gamequery("SELECT B.player1, B.player2, B.player1_items, B.player2_items, B.time FROM openrsc_trade_logs AS B LEFT JOIN openrsc_players AS A ON 'B.player1' = 'A.username' OR 'B.player2' = 'A.username' WHERE (A.id = '$subpage' OR A.username = '$subpage') LIMIT 30");

$player_bank = $connector->gamequery("SELECT A.username, B.id, format(B.amount, 0) number, B.slot FROM `openrsc_bank` AS B LEFT JOIN openrsc_players AS A ON B.playerID = A.id WHERE (A.id = '$subpage' OR A.username = '$subpage') ORDER BY slot ASC");

$player_invitems = $connector->gamequery("SELECT A.username, B.id, format(B.amount, 0) number, B.slot FROM `openrsc_invitems` AS B LEFT JOIN openrsc_players AS A ON B.playerID = A.id WHERE (A.id = '$subpage' OR A.username = '$subpage') ORDER BY slot ASC");

$player_feed = $connector->gamequery("SELECT * FROM openrsc_live_feeds AS B LEFT JOIN openrsc_players AS A ON B.username = A.username WHERE (A.id = '$subpage' OR A.username = '$subpage') ORDER BY 'B.time' DESC LIMIT 8");

//$phpbb_user_result = $connector->gamequery("SELECT B.user_id, B.username AS player_name, A.username, A.group_id FROM openrsc_forum.phpbb_users as B LEFT JOIN openrsc_game.openrsc_players as A on B.user_id = A.owner WHERE (A.id = '$subpage' OR A.username = '$subpage')");
//$phpbb_user = $connector->fetchArray($phpbb_user_result);

function bd_nice_number($n)
{
    if ($n > 1000000000000) return round(($n / 1000000000000), 1) . ' trillion';
    else if ($n > 1000000000) return round(($n / 1000000000), 1) . ' billion';
    else if ($n > 1000000) return round(($n / 1000000), 1) . ' million';
    else if ($n > 1000) return round(($n / 1000), 1) . ' thousand';

    return number_format($n);
}

?>

<main class="main">
    <article>
        <div class="panel">
            <div>
                <h3>
                    <?php echo $character['username']; ?>'s Donation Bank
                </h3>
            </div>
            <div class="stats flex-row">
                <div id="character">
                    <?php
                    $file = 'https://game.openrsc.com/avatars/' . $character['id'] . '.png';
                    echo "<img src=\"$file\"/>";
                    ?>
                </div>

                <div>
                    <div id="sm-stats">
                        <span class="sm-stats">Status:
                            <?php if ($character['online'] == 1) {
                                echo '<span class="green"><strong>Online</strong></span>';
                            } else {
                                echo '<span class="red"><strong>Offline</strong></span>';
                            } ?></span>
                        <span class="sm-stats">Last Online: <?php date_default_timezone_set('America/New_York');
                            echo strftime("%d %b / %H:%M %Z", $character["login_date"]) ?></span>
                        Shar redistributes wealth because he is concerned about the economy.
                    </div>
                </div>
            </div>

            <br/>

            <div style="margin-left: 10px;">
                <table style="background: rgba(255,255,255,0.3); border-collapse: collapse;">
                    <?php $bank = $connector->num_rows($player_bank); ?>
                    <tr>
                        <?php
                        if ($bank == 0) {
                            echo "No bank items found.";
                        } else {
                            for ($i = 1; $list = $connector->fetchArray($player_bank); $i++) {
                                ?>
                                <td style="border: 1px solid black;">
                                    <div style="-webkit-text-fill-color: limegreen; -webkit-text-stroke-width: 0.8px; -webkit-text-stroke-color: black; margin-top: -3px; position: absolute; color: white; font-size: 13px; font-weight: 900;">
                                        <?php echo $list["number"]; ?>
                                    </div>
                                    <img src="/css/images/items/<?php echo $list["id"]; ?>.png"/>
                                </td>
                                <?php
                                if (($i % 14 == 0) && ($i < $bank)) {
                                    echo '</tr><tr>';
                                }
                            }
                        } ?>
                    </tr>
                </table>
                <br/><p align="center">Shar accepts player item donations for drop parties. Please contact staff to donate.</p>
            </div>

        </div>
    </article>
</main>
