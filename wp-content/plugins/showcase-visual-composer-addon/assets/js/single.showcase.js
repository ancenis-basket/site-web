jQuery(document).ready(function($) {
	$('#showcase-closebtn,#box_vc_floats').bind('click', function() {
		if ($('#box_vc_floats').length) {					
			$(this).fadeOut(200, function() { 
				$('#box_vc_floats').remove();
			});
		};

		$(this).fadeOut(200, function() { 
			$('#single-showcase').hide();
		});
		
		$("body").removeClass('vc-floats-fixed');
		$('#showcase-carousel a').fadeTo('slow', 1 );
		$('#showcase-carousel a.current').removeClass('current');
		$('#showcase-carousel').trigger('owl.play',3000);
	});

	$(document).keyup(function(e) {
		if (e.keyCode == 27) {
			$( "#box_vc_floats" ).remove();
			$("body").removeClass('vc-floats-fixed');
			$('#showcase-carousel a').fadeTo('slow', 1 );
			$('#showcase-carousel a.current').removeClass('current');
			$('#showcase-carousel').trigger('owl.play',3000);
		}
		
	});

});