<?php
/**
* Grab geotr settings
* @return mixed|void
*/
function geotr_settings(){
	return apply_filters('geotr/opts', get_option( 'geotr_settings' ) );
}