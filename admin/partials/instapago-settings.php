<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://angelcruz.dev
 * @since      8.0.0
 *
 * @package    Instapago
 * @subpackage Instapago/admin/partials
 * @author     Angel Cruz <hello@tepuilabs.dev>
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->


<div class="wrap">
	<h2><?php echo get_admin_page_title() ?></h2>
	<br>
	<hr>
	<form method="post" action="options.php">
		<?php
		settings_fields('instapago_settings');
		do_settings_sections('instapago-settings');
		submit_button();
		?>
	</form>
</div>
