<div class="wrap geot-settings">
	<form name="geot-settings" method="post" enctype="multipart/form-data">
		<table class="form-table">
			<tr valign="top" class="">
				<th colspan="2"><h3><?php _e( 'Geo Redirects settings:', 'geot' ); ?></h3></th>
				<td colspan="2">
				</td>
			</tr>
			<tr valign="top" class="">
				<th><label for="ajax_mode"><?php _e( 'Ajax Mode', 'geot'); ?></label></th>
				<td colspan="3">
					<label><input type="checkbox" id="ajax_mode" name="geotr_settings[ajax_mode]" value="1" <?php checked($opts['ajax_mode'],'1');?>/>
						<p class="help"><?php _e( 'In Ajax mode, after page load an extra request is made and the user it\'s redirected if needed.', 'geot'); ?></p>
				</td>
			</tr>

			<tr><td><input type="submit" class="button-primary" value="<?php _e( 'Save settings', 'geot' );?>"/></td>
				<?php wp_nonce_field('geotr_save_settings','geot_nonce'); ?>
		</table>
	</form>
</div>
