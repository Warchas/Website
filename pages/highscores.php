<?php
if (!defined('IN_SITE')) {
	die("You do not have permission to access this file.");
}

$skill_array = array('skill_total', 'attack', 'strength', 'defense', 'hits', 'ranged', 'prayer', 'magic', 'cooking', 'woodcut', 'fletching', 'fishing', 'firemaking', 'crafting', 'smithing', 'mining', 'herblaw', 'agility', 'thieving');

function totalXP($skills)
{
	$skill_total = 0;
	foreach ($skills as $key => $value) {
		if (substr($key, 0, 4) == "exp_") {
			$skill_total += $value;
		}
	}
	return $skill_total;
}

$connector = new Dbc();
$subpage = preg_replace("/[^A-Za-z0-9 ]/", "_", $subpage);

if ($subpage == $skill_array[0]) {
	$query = array('openrsc_players.' . $subpage . ', openrsc_experience.*', 'openrsc_players.' . $subpage);
} else {
	$query = array('openrsc_experience.exp_' . $subpage, 'exp_' . $subpage);
}
$args = $query[0];
$order = $query[1];
$stat_result = $connector->gamequery("SELECT openrsc_players.id, openrsc_players.username, openrsc_players.login_date, openrsc_players.highscoreopt, $args FROM openrsc_experience LEFT JOIN openrsc_players ON openrsc_experience.playerID = openrsc_players.id WHERE openrsc_players.banned != '1' AND openrsc_players.group_id = '10' AND openrsc_players.login_date >= unix_timestamp( current_date - interval 3 month ) AND openrsc_players.login_date >= '1539645175' ORDER BY $order DESC");
?>

<div class="text-info table-dark" style="height: 100vh; width: 100vw;">
	<div class="container border-left border-info border-right">
		<div class="h2 text-center pt-5 pb-5 text-capitalize display-3"
			 style="font-size: 38px;"><?php print preg_replace("/[^A-Za-z0-9 ]/", " ", $subpage); ?>
		</div>
		<div align="center">
			Note: Only players that have logged in within the last 3 months are shown.
		</div>
		<div class="pl-3 pr-3">
			<div class="row">
				<div class="col-2">
					<div class="skill">
						<?php foreach ($skill_array as $skill) { ?>
							<li><a href="/highscores/<?php print $skill; ?>">
									<img src="/img/skill_icons/<?php print $skill; ?>.svg"
										 alt="<?php print $skill; ?>" class="skill-icon"/>
									<?php print ucwords(preg_replace("/[^A-Za-z0-9 ]/", " ", $skill)); ?>
								</a></li>
						<?php } ?>
					</div>
				</div>
				<div class="col-10">
					<input type="text" class="pl-2 mb-2" id="inputBox" onkeyup="search()"
						   placeholder="Search for a player">
					<div class="tableFixHead">
						<table id="itemList" class="container table-striped table-hover table-dark text-primary"
							   align="center">
							<thead>
							<tr>
								<th class="username">Username</th>
								<th class="rank">Rank</th>
								<th class="experience">Level</th>
								<th class="experience">Experience</th>
								<th class="experience">Last Login</th>
							</tr>
							</thead>
							<tbody>
							<?php
							$i = 1;
							while ($row = $connector->fetchArray($stat_result)) {
								$idLink = preg_replace('~[\x00\x0A\x0D\x1A\x22\x25\x27\x5C\x5F]~u', " ", $idLinks);
								$idLink = preg_replace("/[^A-Za-z0-9]/", "-", $row['id']);
								?>
								<tr id="table">
									<td class="text-capitalize username">
										<div class="clickable-row" data-href="/player/<?php
										if ($row['highscoreopt'] == 1): echo "null";
										else:
											echo $idLink;
										endif;
										?>"><?php
											if ($row['highscoreopt'] == 1): echo "<i>(Hidden)</i>";
											else:
												echo $row['username'];
											endif;
											?></div>
									</td>
									<td class="rank"><?php echo $i; ?></td>
									<td class="experience">
										<?php echo ($subpage == $skill_array[0]) ? $row['skill_total'] : experienceToLevel($row['exp_' . $subpage] / 4.0); ?>
									</td>
									<td class="experience">
										<?php echo ($subpage == $skill_array[0]) ? intval(totalXP($row) / 4.0) : intval($row['exp_' . $subpage] / 4.0); ?>
									</td>
									<td class="experience">
										<?php echo $row['login_date']; ?>
									</td>
								</tr>
								<?php $i++;
							} ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
