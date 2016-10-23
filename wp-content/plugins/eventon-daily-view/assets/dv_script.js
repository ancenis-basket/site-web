/**
 * Javascript: Eventon Daily View
 * @version 0.26
 */
jQuery(document).ready(function($){

	init();

	var current_date;
	var current_day;
	var current_events;


	function init(){
		set_daily_strip_sizes('');
		$('body').find('div.evoDV').each(function(){

			IN = $(this).find('.eventon_daily_in');
			LEFT = parseInt(IN.attr('data-left'));
			//console.log(LEFT);
			IN.css('margin-left',LEFT);
			//$(this).attr({'data-runajax':0});			
		});

		update_num_events();
	}

	// Hover on event dots tooltip
		$('.eventon_daily_list').on('mouseover','em',function(){
			OBJ = $(this);
			
			PAR = OBJ.closest('.eventon_daily_list');

			p = OBJ.offset();
			t = PAR.offset();
			w = PAR.width();
			xleft = p.left - t.left;
			xtop = p.top - t.top;

			TITLE = OBJ.data('title');

			// adjust side of the tooltip
			if((w/2) > xleft){
				HTML = "<em class='evodv_tooltip' style='top:"+(xtop-13)+"px;left:"+(xleft+23)+"px;'>"+TITLE+"</em>";
			}else{

				xright = w - xleft;
				HTML = "<em class='evodv_tooltip left' style='top:"+(xtop-13)+"px;right:"+(xright+13)+"px;'>"+TITLE+"</em>";
			}
			
			PAR.append(HTML);

		}).mouseout(function(){
			OBJ = $(this);
			OBJ.closest('.eventon_daily_list').find('.evodv_tooltip').remove();

		});

	// switch month within day strip
		$('body').on('click','.evodv_action',function(){

		});
	// update number of events for current day
		function update_num_events(){
			$('.evoDV').each(function(){
				var numevents = $(this).find('.eventon_daily_in').find('p.on_focus').data('events');

				if(numevents!=='' && numevents!==false){
					$(this).find('.evodv_current_day .evodv_events span').html(numevents).parent().show();
				}

				// update day and date name
				var onfocus = $(this).find('p.evo_day.on_focus');
				changin_dates(onfocus.data('date'), $(this).attr('id'), onfocus);
			});
		}
	// click on a day
		$('.eventon_daily_list').on( 'click','.evo_day',function(){
			var new_day = $(this).find('.evo_day_num').html();
					
			var cal_id = $(this).closest('.ajde_evcal_calendar').attr('id');
			var day_obj = $(this);
			
			var daysinmonth = $('#'+cal_id).find('.eventon_daily_in .evo_day').length;
			var thisday = parseInt($(this).find('.evo_day_num').html());
			var arrows =  $('#'+cal_id).find('.evodv_daynum');
			arrows.find('span').removeClass('disable'); // remove disable class

			// add disable class 
			if(thisday==1 ) arrows.find('.prev').addClass('disable');
			if(thisday==daysinmonth) arrows.find('.next').addClass('disable');

			changin_dates(new_day, cal_id, day_obj, true);
		});

	// User DV box arrows to switch days
		$('.evodv_current_day').on('click', '.evodv_daynum span', function(){

			if(!$(this).hasClass('disable')){
				var dir = $(this).attr('data-dir');
				var cal = $(this).closest('.ajde_evcal_calendar');
				var cal_id = cal.attr('id');

				var daysinmonth = cal.find('.eventon_daily_in .evo_day').length;
				var thisday = parseInt($(this).parent().find('b').html());

				// remove disable class
				$(this).parent().find('span').removeClass('disable');

				if(dir == 'next'){
					var day_obj = cal.find('p.evo_day.on_focus');
					var new_day = cal.find('p.evo_day.on_focus').next().data('date');

					// add disable calss
					if(thisday == (daysinmonth-1)) {$(this).addClass('disable');}					
				}else{
					var day_obj = cal.find('p.evo_day.on_focus');
					var new_day = cal.find('p.evo_day.on_focus').prev().data('date');
					if(thisday==2){ $(this).addClass('disable'); }
				}
				
				changin_dates(new_day, cal_id, day_obj, true);
			}

		});
	
	// change the dates on current date section
		function changin_dates(new_day, cal_id, day_obj, ajax){
			var new_day_obj = day_obj.parent().find('.evo_day[data-date='+ new_day+']');
			
			day_obj.parent().find('.evo_day').removeClass('on_focus');
			new_day_obj.addClass('on_focus');

			// update global values
			current_date = new_day;
			current_events = new_day_obj.data('events');
			current_day = new_day_obj.data('dnm');
			update_current_date_section(day_obj.closest('.ajde_evcal_calendar'));
			
			if(ajax)
				ajax_update_month_events(cal_id, new_day);
		}

	// update the current date section with new information
		function update_current_date_section(obj){
			obj.find('.evodv_current_day .evodv_events span').html(current_events).parent().show();
			obj.find('.evodv_current_day .evodv_daynum b').html(current_date);
			obj.find('.evodv_current_day .evodv_dayname').html(current_day);
		}
	
	// AJAX:  when changing date
		function ajax_update_month_events(cal_id, new_day){
			var ev_cal = $('#'+cal_id); 
			var cal_head = ev_cal.find('.calendar_header');
			var evodata = ev_cal.find('.evo-data');

			var evcal_sort = cal_head.siblings('div.evcal_sort');
					
			var sort_by=evcal_sort.attr('sort_by');
			var cat=evcal_sort.attr('cat');
			
			var ev_type = evodata.attr('data-ev_type'); 
			var ev_type_2 = evodata.attr('data-ev_type_2');
			
			// wether to switch to 1st of month
				var new_date_el = ev_cal.find('.eventon_other_vals[name=dv_focus_day]');
				var new_day_ =1;
				
				new_day_ = (new_date_el.attr('data-mo1st')=='1')? 1: new_day;

			// change values to new in ATTRs
			evodata.attr({'data-cday':new_day});

			var data_arg = {
				action: 		'the_ajax_hook',
				current_month: 	evodata.attr('data-cmonth'),	
				current_year: 	evodata.attr('data-cyear'),	
				sort_by: 		sort_by, 			
				event_count: 	evodata.attr('data-ev_cnt'),
				dv_focus_day: 	new_day,
				direction: 		'none',
				filters: 		ev_cal.evoGetFilters(),
				shortcode: 		ev_cal.evo_shortcodes(),
				evodata: 		ev_cal.evo_getevodata()			
			};
			
			
			$.ajax({
				beforeSend: function(){
					ev_cal.find('.eventon_events_list').slideUp('fast');
					ev_cal.find('#eventon_loadbar').slideDown().css({width:'0%'}).animate({width:'100%'});
				},
				type: 'POST',
				url:the_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					// /alert(data);
					ev_cal.find('.eventon_events_list').html(data.content);
					ev_cal.find('.eventon_other_vals').val(new_day_);
				},complete:function(){
					ev_cal.find('#eventon_loadbar').css({width:'100%'}).slideUp();
					ev_cal.find('.eventon_events_list').delay(300).slideDown();
					ev_cal.evoGenmaps({'delay':400});
				}
			});
			
		}
	
	// filter the events	
		$('.eventon_filter_dropdown').on( 'click','p',function(){
			filter_section = $(this).closest('.eventon_filter_line');
			if(filter_section.hasClass('selecttype')) return;

			var cal_head = $(this).closest('.eventon_sorting_section').siblings('.calendar_header');
			eventon_dv_get_new_days(cal_head,'','');
		});

		$('body').on('click','.evo_filter_submit',function(){
			var cal_head = $(this).closest('.eventon_sorting_section').siblings('.calendar_header');
			eventon_dv_get_new_days(cal_head,'','');
		});

	// go to today
		$('body').on('evo_goto_today', function(index, calid, evo_data){
			if($('#'+calid).hasClass('evoDV'))		
				eventon_dv_get_new_days($('#'+calid).find('.calendar_header'),'','');
		});

	// MONTH JUMPER
		$('.evo_j_dates').on('click','a',function(){
			var container = $(this).closest('.evo_j_container');
			if(container.attr('data-m')!==undefined && container.attr('data-y')!==undefined){
				
				var cal_head = $(this).closest('.calendar_header');
				var evo_dv = cal_head.find('.eventon_other_vals').length;

				if(evo_dv>0)
					eventon_dv_get_new_days(cal_head,'','');
			}
		});

	// MONTH SWITCHING	
		$('.evcal_btn_prev').click(function(){
			var top = $(this).closest('.ajde_evcal_calendar');
			if(top.hasClass('evoDV')){
				var cal_head = $(this).parents('.calendar_header');
				var evo_dv = cal_head.find('.eventon_other_vals').length;		
				if(evo_dv>0){
					eventon_dv_get_new_days(cal_head,'prev','');
				}
			}			
		});
		
		$('.evcal_btn_next').click(function(){	
			var top = $(this).closest('.ajde_evcal_calendar');
			if(top.hasClass('evoDV')){
				var cal_head = $(this).parents('.calendar_header');
				var evo_dv = cal_head.find('.eventon_other_vals').length;		
				if(evo_dv>0){
					eventon_dv_get_new_days(cal_head,'next','');
				}
			}
		});

		// switching months with day strip
			$('.eventon_daily_list').on('click','.evodv_action',function(event){
				classN = ($(this).hasClass('next'))? 'evcal_btn_next':'evcal_btn_prev';
				calendar = $(this).closest('.ajde_evcal_calendar');

				calendar.find('.'+classN).trigger('click');
			});
	
	// AJAX: update the days list for new month
		function eventon_dv_get_new_days(cal_header, change, cday){
			
			var cal_id = cal_header.parent().attr('id');

			// stop this from running for other calendars
			if(!cal_header.parent().hasClass('evoDV'))
				return;

			var evodata = cal_header.siblings('.evo-data');

			var cur_m = parseInt(evodata.attr('data-cmonth'));
			var cur_y = parseInt(evodata.attr('data-cyear'));
			
			var ev_cal = cal_header.parent();
			
			// new dates
			var new_d = (cday=='')? cal_header.find('.eventon_other_vals').val(): cday;

			// set first to be the date
				//cal_header.find('.eventon_other_vals').attr({'value':1});
			
			if(change=='next'){
				var new_m = (cur_m==12)?1: cur_m+ 1 ;
				var new_y = (cur_m==12)? cur_y+1 : cur_y;
			}else if(change=='prev'){
				var new_m = (cur_m==1)?12:cur_m-1;
				var new_y = (cur_m==1)?cur_y-1:cur_y;
			}else{
				var new_m =cur_m;
				var new_y = cur_y;
			}
			
			var data_arg = {
				action:'the_ajax_daily_view',
				next_m:new_m,	
				next_y:new_y,
				next_d:new_d,
				cal_id: 	cal_id,
				send_unix: 	evodata.data('send_unix'),
				filters: 		ev_cal.evoGetFilters(),
				shortcode: 		ev_cal.evo_shortcodes(),
			};
			
			var this_section = cal_header.parent().find('.eventon_daily_in');
			var this_section_days = cal_header.parent().find('.eventon_daily_list');
			owl = cal_header.parent().find('.evodv_carousel');
			
			$.ajax({
				beforeSend: function(){
					this_section_days.slideUp('fast');		
				},
				type: 'POST',
				url:the_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					//console.log(data);
					this_section.html(data.days_list);
					revert_to_beginning(cal_id, data.last_date_of_month, new_d);

					// update current date section 
					update_num_events();

				},complete:function(){
					this_section_days.slideDown('slow');				
					set_daily_strip_sizes();
				}
			});
			//ajax_update_month_events(cal_id, new_d);
		}
		
	//	Return filters array if exist for the active calendar
		function get_filters_array(cal_id){
			var ev_cal = $('#'+cal_id); 
			var evodata = ev_cal.find('.evo-data');
			
			var filters_on = ( evodata.attr('data-filters_on')=='true')?'true':'false';
			
			// creat the filtering data array if exist
			if(filters_on =='true'){
				var filter_section = ev_cal.find('.eventon_filter_line');
				var filter_array = [];
				
				filter_section.find('.eventon_filter').each(function(index){
					var filter_val = $(this).attr('data-filter_val');
					
					if(filter_val !='all'){
						var filter_ar = {};
						filter_ar['filter_type'] = $(this).attr('data-filter_type');
						filter_ar['filter_name'] = $(this).attr('data-filter_field');
						filter_ar['filter_val'] = filter_val;
						filter_array.push(filter_ar);
					}
				});			
			}else{
				var filter_array ='';
			}
			
			return filter_array;
		}

	// SUPPORT: turn off runAJAX on calendar
		function turnoff_runajax(cal_id){	}
	
	// mouse wheel
		$('.eventon_daily_in').mousewheel(function(e, delta) {
			//$(this).scrollLeft -= (delta * 40);
			OBJ = $(this);
			
			var cur_mleft = parseInt(OBJ.css('marginLeft')),
				width = parseInt(OBJ.css('width') ),
				Pwid = OBJ.parent().width();
			maxMLEFT = (width-Pwid)*(-1);
			
			if( cur_mleft<=0){
				
				var new_marl = (cur_mleft+ (delta * 140));
				
				if(new_marl>0){ new_marl=0;}
				
				// moving to left
				if(delta == -1 && ( (new_marl*(-1))< (width -200)) ){
					new_marl = ( new_marl <maxMLEFT)? maxMLEFT: new_marl;
					OBJ.stop().animate({'margin-left': new_marl });
				
				}else if(delta == 1){
					OBJ.stop().animate({'margin-left': new_marl });
				}
			}
			e.preventDefault();
		});
		// touch function
			$('.eventon_daily_in').on('swipeleft',function(event){				
				swiping('swipeleft', $(this));
				event.preventDefault();
			});
			$('.eventon_daily_in').on('swiperight',function(event){				
				swiping('swiperight', $(this));
				event.preventDefault();
			});

			function swiping(direction, OBJ){
				var leftNow = parseInt(OBJ.css('marginLeft'));
				var Pwid = OBJ.parent().width();
				var width = parseInt(OBJ.css('width') );
				maxMLEFT = (width-Pwid)*(-1);
				swipeMove = 300;
				
				if(direction =='swipeleft'){
					var newLEFT = ( leftNow - swipeMove );	
					// /console.log(newLEFT);

					if( newLEFT*(-1) < (width) ){
						newLEFT = ( newLEFT <maxMLEFT)? maxMLEFT: newLEFT;
						OBJ.stop().animate({'margin-left': newLEFT });
					}
				}else{
					var newLEFT = ( leftNow + swipeMove );	
					// /console.log(newLEFT);

					newLEFT = ( newLEFT >0 )? 0: newLEFT;
					OBJ.stop().animate({'margin-left': newLEFT });
				}
				
			}
		// adjust margin left when window resized
			$(window).on('resize', function(){
				$('.eventon_daily_in').each(function(){
					OBJ = $(this);
					var leftNow = parseInt(OBJ.css('marginLeft'));
					var Pwid = OBJ.parent().width();
					var width = parseInt(OBJ.css('width') );

					maxMLEFT = (width-Pwid)*(-1);

					if(leftNow < maxMLEFT)
						OBJ.stop().css({'margin-left': maxMLEFT });

				});
			});
	
	// remove days back to beginning of month
		function revert_to_beginning(cal_id, new_d, current_day){
			var day_holder = $('#'+cal_id).find('.eventon_daily_in');
			var date_w = parseInt(day_holder.find('.evo_day:gt(20)').outerWidth());

			// fix width 
			date_w = (date_w<=0 )? 30: date_w;
			
			
			var w_fb = ((date_w*current_day) - (date_w*8));
			var adjust_w = (w_fb>0)? (w_fb): 0;

			//var dpw = parseInt( day_holder.parent().width());
			//var dw = parseInt(day_holder.width());
			//var ml = day_holder.css('margin-left');

			//var new_ml = dw-dpw;
			day_holder.animate({'margin-left':'-'+(adjust_w)+'px'});

			//console.log(adjust_w+' '+(date_w*5)+' '+new_d+' '+date_w);
		}
	
	// daily list sliders	
		function set_daily_strip_sizes(cal_id){
			if(cal_id!=''){
				var holder = $('#'+cal_id).find('.eventon_daily_list');
				adjust_days_width(holder);
			}
			$('.eventon_daily_list').each(function(){
				adjust_days_width( $(this));
				
			});
		}
			function adjust_days_width(holder){
				var day_holder = holder.find('.eventon_daily_in');
				var days = day_holder.children('.evo_day');	
				var day_width = parseInt(day_holder.find('.evo_day:gt(20)').outerWidth());

				var d_holder_width = (parseInt(days.length)+2 )* (day_width);
						
				day_holder.css({'width':d_holder_width});

				//console.log(day_width+' '+d_holder_width+' '+days.length);
			}
	
});