/**
 * Javascript: Slider for eventon
 * @version  0.1
 */
jQuery(document).ready(function($){
	
	// click on an event slide
		$('.evosliderbox').on('click', '.desc_trig',function(){
			console.log('t');
		});	
	
	$('body').find('.evosliderbox').each(function(){

		slider = $(this);
		sliderList = slider.find('ul.evosl_slider');
		slideData = slider.find('.evo_slider_width');
		sliderTYPE = slideData.data('slider_type');

		sliderList.find('.eventon_list_event').each(function(){
			img = $(this).find('.ev_ftImg');
			imgsrc = img.data('img');
			thumbsrc = img.data('thumb');

			// featured image adjustment
				if(sliderTYPE != 'minicar'){
					if(imgsrc == undefined || imgsrc === 'undefined'){
						style = 'background-color:'+ $(this).attr('data-colr');
					}else{
						style = 'background: url('+imgsrc+') center center no-repeat;';
					}
				}else{
					style = 'background-color:'+ $(this).attr('data-colr');
				}

			$(this).attr({
				'data-thumb':thumbsrc, 
				'style':style
			}).addClass('evo_slide_item');
			//$(this).replaceWith($('<li>' + this.innerHTML + '</li>'));
		});

		

		// show multiple slides
			if( sliderTYPE =='multi'||  sliderTYPE =='multimini'){
				sliderList.evoSlider({
			      	gallery:false,
			      	slideMargin: 0,
			      	speed: 				parseInt(slideData.data('slider_speed')),
			      	pause: 				parseInt(slideData.data('slider_pause')),
			      	auto:				( slideData.data('slide_auto')=='yes'? true: false),
			      	loop:				( slideData.data('slide_loop')=='yes'? true: false),
			      	pauseOnHover: 		( slideData.data('slide_pause_hover')=='yes'? true: false), 
			      	controls: 			( slideData.data('slide_hide_control')=='yes'? false: true), 
			      	prevHtml: 			'<i class="fa fa-angle-left"></i>',
			      	nextHtml: 			'<i class="fa fa-angle-right"></i>',
			      	onSliderLoad: function() {
			          	sliderList.removeClass('evo-hidden');
			      	},
			      	item: 		4,
			      	slideMove: 	2,
			      	responsive : [
						{
							breakpoint:800,
							settings: {
								item:3,slideMove:1,slideMargin:0,
							  }
						},{
							breakpoint:480,
							settings: {
								item:2,slideMove:1,slideMargin:0,
							  }
						},{
							breakpoint:320,
							settings: {
								item:1,slideMove:1,slideMargin:0,
							  }
						}
					]
			  	});
				
				sliderList.removeClass('evo-hidden');

			}else if( sliderTYPE =='minicar'){

				sliderList.evoSlider({
			      	gallery:false,
			      	slideMargin: 0,
			      	speed: 				parseInt(slideData.data('slider_speed')),
			      	pause: 				parseInt(slideData.data('slider_pause')),
			      	auto:				( slideData.data('slide_auto')=='yes'? true: false),
			      	loop:				( slideData.data('slide_loop')=='yes'? true: false),
			      	pauseOnHover: 		( slideData.data('slide_pause_hover')=='yes'? true: false), 
			      	controls: 			( slideData.data('slide_hide_control')=='yes'? false: true), 
			      	pager: false,
			      	prevHtml: 			'<i class="fa fa-angle-left"></i>',
			      	nextHtml: 			'<i class="fa fa-angle-right"></i>',
			      	enableDrag: false,
			      	onSliderLoad: function() {
			          	sliderList.removeClass('evo-hidden');
			      	},
			      	//item: 3,
			      	slideMove: 	1,
			      	responsive : [
						{
							breakpoint:1000,
							settings: {
								item:3,slideMove:1,slideMargin:0,
							  }
						},{
							breakpoint:900,
							settings: {
								item:2,slideMove:1,slideMargin:0,
							  }
						},{
							breakpoint:480,
							settings: {
								item:1,slideMove:1,slideMargin:0,
							  }
						},{
							breakpoint:320,
							settings: {
								item:1,slideMove:1,slideMargin:0,
							  }
						}
					]
			  	});
				
				sliderList.removeClass('evo-hidden');

			}else if( sliderTYPE =='vertical'){
				sliderList.evoSlider({
			      	gallery:false,
			      	item:1,
			      	vertical: true,
        			verticalHeight: 400,
			      	slideMargin: 0,
			      	speed: 				parseInt(slideData.data('slider_speed')),
			      	pause: 				parseInt(slideData.data('slider_pause')),
			      	auto:				( slideData.data('slide_auto')=='yes'? true: false),
			      	loop:				( slideData.data('slide_loop')=='yes'? true: false),
			      	pauseOnHover: 		( slideData.data('slide_pause_hover')=='yes'? true: false), 
			      	controls: 			( slideData.data('slide_hide_control')=='yes'? false: true), 
			      	prevHtml: 			'<i class="fa fa-angle-left"></i>',
			      	nextHtml: 			'<i class="fa fa-angle-right"></i>',
			      	enableDrag: false,
			      	onSliderLoad: function() {
			          	sliderList.removeClass('evo-hidden');
			      	}
			  	});
			  	sliderList.removeClass('evo-hidden');
			}else{
				sliderList.evoSlider({
			      	gallery:false,
			      	item:1,
			      	slideMargin: 0,
			      	speed: 				parseInt(slideData.data('slider_speed')),
			      	pause: 				parseInt(slideData.data('slider_pause')),
			      	auto:				( slideData.data('slide_auto')=='yes'? true: false),
			      	loop:				( slideData.data('slide_loop')=='yes'? true: false),
			      	pauseOnHover: 		( slideData.data('slide_pause_hover')=='yes'? true: false), 
			      	controls: 			( slideData.data('slide_hide_control')=='yes'? false: true), 
			      	prevHtml: 			'<i class="fa fa-angle-left"></i>',
			      	nextHtml: 			'<i class="fa fa-angle-right"></i>',
			      	onSliderLoad: function() {
			          	sliderList.removeClass('evo-hidden');
			      	}
			  	});
			  	sliderList.removeClass('evo-hidden');
			}
	});	
});