;(function ( $ ) {

	"use strict";

	$(document).ready(function(){

		function hide_or_show_fields( _this ){
			var _name = $(_this).attr('name');
			var _val = $(_this).val();

			$( '[data-show-if-'+ _name +']' ).each(function(){
				if( $(this).data('show-if-'+ _name +'') == _val ){
					$(this).show();
				}
				else{
					$(this).hide();
				}
			});
		}

		$('select:not(.ignore-changes), input:not(.ignore-changes)').on( 'change', function(){
			hide_or_show_fields( this );
		});

		$('select:not(.ignore-changes), input:not(.ignore-changes)').each( function(){
			hide_or_show_fields( this );
		});
		

		/*
		-------------------------------------------------------------------------------
		Seteaza perioada asigurata in dependenta de tara inmatricularii
		-------------------------------------------------------------------------------
		*/
		function perioada_asigurata( value ){
			if( value == 'moldova' ){
				$( 'select[name="perioada_asigurata"]' ).attr('disabled', 'disabled').val('12l').change();
				$( 'select[name="perioada_asigurata"]' ).before('<input type="hidden" name="perioada_asigurata" value="12l">');
			}
			else{
				$( 'select[name="perioada_asigurata"]' ).removeAttr('disabled');
			}
		}

		if( $( '[name="inmatriculat_tara"]' ).val() == 'moldova' ){
			perioada_asigurata( 'moldova' );
		}

		$( '[name="inmatriculat_tara"]' ).on('change', function(){
			perioada_asigurata( $(this).val() );
		});


		/*
		-------------------------------------------------------------------------------
		Afiseaza "Persoane admise la volan" in dependenta de statut juridic
		-------------------------------------------------------------------------------
		*/
		function persoane_admin_la_volan( value ){
			if( value == 'fizica' ){
				$( '#persoane_admin_la_volan' ).show();
			}
			else{
				$( '#persoane_admin_la_volan' ).hide();
			}
		}

		if( $( '[name="statut_juridic"]' ).val() == 'fizica' ){
			persoane_admin_la_volan( 'fizica' );
		}

		$( '[name="statut_juridic"]' ).on('change', function(){
			persoane_admin_la_volan( $(this).val() );
		});


		/*
		-------------------------------------------------------------------------------
		Admin
		-------------------------------------------------------------------------------
		*/
		// Confirmare inainte de a continua un link de stergere a datelor
		$('a[data-confirm-delete]').on( 'click', function(){
			var msg = $(this).data('confirm-delete');
			if( ! confirm( msg ) ){
				return false;
			}
		});

		// Tooltips
		$('a').tooltip();

		//Popovers
		$('[data-toggle="popover"]').popover({
			html: true,
			trigger: 'hover',
			placement: 'top',
			content: function () {
				var tip_doc = $('[name="tip_document"]').val();
				return '<img src="img/'+$(this).data('img-tip') + '_'+ tip_doc +'.jpg" class="img-responsive" />';
			}
		});

		// Nu urmari linkurile care sunt compuse numai din "#".
		$('a[href="#"]').on( 'click', function(event){
			event.preventDefault();
		});

		
		/*
		-------------------------------------------------------------------------------
		Form nice selector
		-------------------------------------------------------------------------------
		*/
		$('.form-nice-selector').each(function(){
			var _t = $(this);
			var spans = _t.children('span');
			var input = _t.children('input');

			/* Action on option select
			-------------------------------*/
			spans.on('click', function(){
				var value = $(this).data('value');
				spans.removeClass('active');
				$(this).addClass('active');
				input.val( value ).change();
			});

			/* Prepare spans
			---------------------------*/
			$.each(spans, function(){
				if($(this).hasClass('active')){
					input.val( $(this).data('value') ).change();
				}
				
				// If has color option, show it
				var color = $(this).data('color');
				if( color ){
					color = (color.toString().indexOf('#') > -1) ? color : '#'+color;
					$(this).prepend('<div class="color-box" style="background-color: '+ color +'"></div>');
				}

				//If has image option, show it
				var img = $(this).data('img');
				if( img ){
					$(this).prepend('<div class="img-box"><img src="'+ img +'" alt="" /></div>');
				}
			});
		});


	});

}( jQuery ));