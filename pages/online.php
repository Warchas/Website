<?php
if (!defined('IN_SITE')) {
	die("You do not have permission to access this file.");
}
?>

<div class="text-info table-dark">
	<div class="container border-left border-info border-right">
		<div class="h2 text-center pt-5 pb-5 text-capitalize display-3" style="font-size: 38px;">Players Online</div>
		<div class="row justify-content-center" style="height: 100vh;">
			<div class="col-2 text-primary" align="center">
				<b><?php echo onlinePlayers(); ?></b>
			</div>
		</div>
	</div>
</div>