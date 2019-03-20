(function( $ ) {
	'use strict';

    const urlParams = new URLSearchParams(window.location.search);
    const geot_debug = urlParams.get('geot_debug'),
     geot_debug_iso  = urlParams.get('geot_debug_iso'),
     geot_state  = urlParams.get('geot_state'),
     geot_state_code  = urlParams.get('geot_state_code'),
     geot_city  = urlParams.get('geot_city'),
     geot_zip  = urlParams.get('geot_zip');

	if( $('.geotr-ajax').length ) {
        var data = {
                action : 'geo_redirects',
                pid : geotr.pid,
                referrer : document.referrer,
                url : window.location.href,
                query_string : document.location.search,
                is_category : geotr.is_category,
                is_archive : geotr.is_archive,
                is_front_page : geotr.is_front_page,
                is_search : geotr.is_search,
                geot_debug : geot_debug,
                geot_debug_iso  : geot_debug_iso,
                geot_state  : geot_state,
                geot_state_code  : geot_state_code,
                geot_city  : geot_city,
                geot_zip  : geot_zip
            }
            ,success_cb = function(response) {
                if( response && response.url ){
                    $('.geotr-ajax').show();
                    setTimeout(function(){
                        location.replace(response.url)
                    },2000);
                }
            },
            error_cb 	= function (data, error, errorThrown){
                console.log('Geo Redirects error: ' + error + ' - ' + errorThrown);
            }
        request(data, success_cb, error_cb);
	}
    /**
     * Ajax requests
     * @param data
     * @param url
     * @param success_cb
     * @param error_cb
     * @param dataType
     */
    function request(data, success_cb, error_cb ){
        // Prepare variables.
        var ajax       = {
                url:      geotr.ajax_url,
                data:     data,
                cache:    false,
                type:     'POST',
                dataType: 'json',
                timeout:  30000
            },
            dataType   = dataType || false,
            success_cb = success_cb || false,
            error_cb   = error_cb   || false;


        // Set success callback if supplied.
        if ( success_cb ) {
            ajax.success = success_cb;
        }

        // Set error callback if supplied.
        if ( error_cb ) {
            ajax.error = error_cb;
        }

        // Make the ajax request.
        $.ajax(ajax);

    }
})( jQuery );
