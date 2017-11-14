<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;?>

<table class="form-table">

	<?php do_action( 'geotr/metaboxes/before_options', $opts );?>

	<tr valign="top">
		<th><label for="geotr_trigger"><?php _e( 'Destination URL', 'geotr' ); ?></label></th>
		<td>
			<input type="text" class="widefat" name="geotr[url]" min="0" value="<?php echo esc_attr($opts['url']); ?>"  />
            <p class="help"><?php _e( 'Enter redirection url. You can create dynamic urls by using placeholders like :', 'geotr' ); ?></p>
			<ul>
				<li>{{country_code}} <?php _e('Two letter iso code','geotr');?></li>
				<li>{{state_code}} <?php _e('Two letter state code','geotr');?></li>
				<li>{{zip}} <?php _e('Zip code','geotr');?></li>
				<li>{{requested_uri}} <?php _e('Original requested url. Eg: http://geotargetingwp.com/geo-redirects','geotr');?></li>
				<li>{{requested_path}} <?php _e('Original requested path. Eg: geo-redirects','geotr');?></li>
			</ul>
		</td>
	</tr>

	<tr valign="top">
		<th><label for="geotr_trigger"><?php _e( 'One time redirect ?', 'geotr' ); ?></label></th>
		<td>
            <select id="one_time_redirect" name="geotr[one_time_redirect]" class="widefat">
                <option value="0" <?php selected($opts['one_time_redirect'], '0'); ?> > <?php _e( 'No', 'geotr' ); ?></option>
                <option value="1" <?php selected($opts['one_time_redirect'], '1'); ?> > <?php _e( 'Yes', 'geotr' ); ?></option>
				<option value="2" <?php selected($opts['one_time_redirect'], '2'); ?> > <?php _e( 'Yes, one per user session', 'geotr' ); ?></option>
			</select>
            <p class="help"><?php _e( 'Select if user will be redirected every time, once per browser session or only once in total', 'geotr' ); ?></p>
		</td>
	</tr>

	<tr valign="top">
		<th><label for="geotr_trigger"><?php _e( 'Exclude Search Engines ?', 'geotr' ); ?></label></th>
		<td>
            <select id="exclude_se" name="geotr[exclude_se]" class="widefat">
                <option value="0" <?php selected($opts['exclude_se'], '0'); ?> > <?php _e( 'No', 'geotr' ); ?></option>
                <option value="1" <?php selected($opts['exclude_se'], '1'); ?> > <?php _e( 'Yes', 'geotr' ); ?></option>
			</select>
            <p class="help"><?php _e( 'Exclude bots and crawlers from being redirected', 'geotr' ); ?></p>
		</td>
	</tr>

    <tr valign="top">
		<th><label for="geotr_trigger"><?php _e( 'Redirection code?', 'geotr' ); ?></label></th>
		<td>
			<input type="text"  name="geotr[status]" value="<?php echo esc_attr($opts['status']); ?>" placeholder="302"/>
            <p class="help"><?php _e( 'Add redirection code. Default to 302', 'geotr' ); ?></p>
		</td>
	</tr>
    <tr valign="top">
		<th><label for="geotr_trigger"><?php _e( 'IP Whitelist', 'geotr' ); ?></label></th>
		<td>
			<textarea class="widefat" name="geotr[whitelist]"><?php echo esc_attr($opts['whitelist']); ?></textarea>
            <p class="help"><?php _e( 'Exclude the following IPs from being redirected. Enter one per line', 'geotr' ); ?></p>
		</td>
	</tr>
	<?php do_action( 'geotr/metaboxes/after_options', $opts );?>
</table>
<?php wp_nonce_field( 'geotr_options', 'geotr_options_nonce' ); ?>
