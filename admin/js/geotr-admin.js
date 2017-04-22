(function( $ ) {
	'use strict';

	$('document').ready( function() {
		geotr.rules.init();
	//	$(".geot-chosen-select").chosen({width:"90%",no_results_text: "Oops, nothing found!"});

		$(".add-region").click( function(e){
			e.preventDefault();
			var region 		= $(this).prev('.region-group');
			var new_region 	= region.clone();
			var new_id		= parseInt( region.data('id') ) + 1;

			new_region.find('input[type="text"]').attr('name', 'geot_settings[region]['+new_id+'][name]').val('');
			new_region.find('select').attr('name', 'geot_settings[region]['+new_id+'][countries][]').find("option:selected").removeAttr("selected");
			new_region.find('.chosen-container').remove();
			new_region.insertAfter(region);
			$(".geot-chosen-select").chosen({width:"90%",no_results_text: "Oops, nothing found!"});
		});

		$(".geot-settings").on('click','.remove-region', function(e){
			e.preventDefault();
			var region 		= $(this).parent('.region-group');
			region.remove();
		});

		$(".add-city-region").click( function(e){
			e.preventDefault();
			var region 		= $(this).prev('.city-region-group');
			var new_region 	= region.clone();
			var cities = new_region.find(".cities_container");
			var chosen = new_region.find(".country_ajax");

			var new_id		= parseInt( region.data('id') ) + 1;
			new_region.find('input[type="text"]').attr('name', 'geot_settings[city_region]['+new_id+'][name]').val('');
			chosen.attr('name', 'geot_settings[city_region]['+new_id+'][countries][]').find("option:selected").removeAttr("selected");
			cities.attr('name', 'geot_settings[city_region]['+new_id+'][cities][]').find("option:selected").removeAttr("selected");
			new_region.find('.chosen-container').remove();
			new_region.insertAfter(region);
			chosen.attr('data-counter', new_id);
			cities.attr('id', 'cities'+new_id);
			cities.chosen({width:"90%",no_results_text: "Oops, nothing found!"});
			chosen.chosen({width:"90%",no_results_text: "Oops, nothing found!"}).on('change', function(){
				load_cities(chosen);
			});
		});

		$(".geot-settings").on('click','.remove-city-region', function(e){
			e.preventDefault();
			var region 		= $(this).parent('.city-region-group');
			region.remove();
		});


		$(".country_ajax").on('change', function(){
			load_cities($(this));
		});

		function load_cities( o ) {
			var counter 		= o.data('counter');
			var cities_select 	= $("#cities"+counter);
			var cities_choosen  = cities_select.next('.chosen-container');
			$.post(
				geot.ajax_url,
				{ action: 'geot_cities_by_country', country : o.val() },
				function(response) {
					//cities_choosen.remove();
					cities_select.html(response);
					cities_select.trigger("chosen:updated");
				}
			);
		}

	});

var geotr = { rules: null}

	/*
	*  Rules
	*
	*  Js for needed for rules
	*
	*  @since: 1.0.0
	*  Thanks to advanced custom fields plugin for part of this code
	*/

	geotr.rules = {
		$el : null,
		init : function(){

			// vars
			var _this = this;


			// $el
			_this.$el = $('#geotr-rules');


			// add rule
			_this.$el.on('click', '.rules-add-rule', function(){

				_this.add_rule( $(this).closest('tr') );

				return false;

			});


			// remove rule
			_this.$el.on('click', '.rules-remove-rule', function(){

				_this.remove_rule( $(this).closest('tr') );

				return false;

			});


			// add rule
			_this.$el.on('click', '.rules-add-group', function(){

				_this.add_group();

				return false;

			});


			// change rule
			_this.$el.on('change', '.param select', function(){

				// vars
				var $tr = $(this).closest('tr'),
					rule_id = $tr.attr('data-id'),
					$group = $tr.closest('.rules-group'),
					group_id = $group.attr('data-id'),
					val_td   = $tr.find('td.value'),
					ajax_data = {
						'action' 	: "geotr/field_group/render_rules",
						'nonce' 	: geotr_js.nonce,
						'rule_id' 	: rule_id,
						'group_id' 	: group_id,
						'value' 	: '',
						'param' 	: $(this).val()
					};


				// add loading gif
				var div = $('<div class="geotr-loading"><img src="'+geotr_js.admin_url+'/images/wpspin_light.gif"/> </div>');
				val_td.html( div );


				// load rules html
				$.ajax({
					url: ajaxurl,
					data: ajax_data,
					type: 'post',
					dataType: 'html',
					success: function(html){

						val_td.html(html);

					}
				});

				// Operators Rules
				var operator_td =  $tr.find('td.operator'),
					ajax_data = {
						'action' 	: "geotr/field_group/render_operator",
						'nonce' 	: geotr_js.nonce,
						'rule_id' 	: rule_id,
						'group_id' 	: group_id,
						'value' 	: '',
						'param' 	: $(this).val()
					};

				operator_td.html( div );
				$.ajax({
					url: ajaxurl,
					data: ajax_data,
					type: 'post',
					dataType: 'html',
					success: function(html){

						operator_td.html(html);

					}
				});

			});

		},
		add_rule : function( $tr ){

			// vars
			var $tr2 = $tr.clone(),
				old_id = $tr2.attr('data-id'),
				new_id = 'rule_' + ( parseInt( old_id.replace('rule_', ''), 10 ) + 1);


			// update names
			$tr2.find('[name]').each(function(){

				$(this).attr('name', $(this).attr('name').replace( old_id, new_id ));
				$(this).attr('id', $(this).attr('id').replace( old_id, new_id ));

			});


			// update data-i
			$tr2.attr( 'data-id', new_id );


			// add tr
			$tr.after( $tr2 );


			return false;

		},
		remove_rule : function( $tr ){

			// vars
			var siblings = $tr.siblings('tr').length;


			if( siblings == 0 )
			{
				// remove group
				this.remove_group( $tr.closest('.rules-group') );
			}
			else
			{
				// remove tr
				$tr.remove();
			}

		},
		add_group : function(){

			// vars
			var $group = this.$el.find('.rules-group:last'),
				$group2 = $group.clone(),
				old_id = $group2.attr('data-id'),
				new_id = 'group_' + ( parseInt( old_id.replace('group_', ''), 10 ) + 1);


			// update names
			$group2.find('[name]').each(function(){

				$(this).attr('name', $(this).attr('name').replace( old_id, new_id ));
				$(this).attr('id', $(this).attr('id').replace( old_id, new_id ));

			});


			// update data-i
			$group2.attr( 'data-id', new_id );


			// update h4
			$group2.find('h4').html( geotr_js.l10n.or ).addClass('rules-or');


			// remove all tr's except the first one
			$group2.find('tr:not(:first)').remove();


			// add tr
			$group.after( $group2 );



		},
		remove_group : function( $group ){

			$group.remove();

		}
	};
})( jQuery );
