/**
 * Javascript: Eventon Daily View
 * @version 1.0.4
 */
jQuery(document).ready(function($){

	init();

	var current_date;
	var current_day;
	var current_events;


	function init(){
		set_daily_strip_sizes('');
		/*$('body').find('div.evoDV').each(function(){

			IN = $(this).find('.eventon_daily_in');
			LEFT = parseInt(IN.attr('data-left'));
			//console.log(LEFT);
			IN.css('margin-left',LEFT);
			//$(this).attr({'data-runajax':0});			
		});
		*/

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
		$('.evodv_current_day').on('click', '.evodv_daynum_switch', function(){

			OBJ = $(this);
			var dir = OBJ.attr('data-dir');
			var cal = OBJ.closest('.ajde_evcal_calendar');
			var cal_id = cal.attr('id');

			var daysinmonth = parseInt(cal.find('.eventon_daily_list').attr('data-mdays'));
			var thisday = parseInt(OBJ.parent().find('b').html());

			// remove disable class
			OBJ.parent().find('span').removeClass('disable');

			//console.log(dir);

			if(dir == 'next'){

				if(thisday == daysinmonth){
					calendar = OBJ.closest('.ajde_evcal_calendar');
					calendar.find('.evcal_btn_next').trigger('click');
				}else{
					var day_obj = cal.find('p.evo_day.on_focus');
					var new_day = cal.find('p.evo_day.on_focus').next().data('date');
					changin_dates(new_day, cal_id, day_obj, true);
				}
				
			}else{
				if(thisday == 1){
					calendar = OBJ.closest('.ajde_evcal_calendar');
					calendar.find('.evcal_btn_prev').trigger('click');
				}else{
					var day_obj = cal.find('p.evo_day.on_focus');
					var new_day = cal.find('p.evo_day.on_focus').prev().data('date');
					changin_dates(new_day, cal_id, day_obj, true);
				}
			}			

		});

	
	// change the dates on current date section
		function changin_dates(new_day, cal_id, day_obj, ajax){
			var new_day_obj = day_obj.parent().find('.evo_day[data-date='+ new_day+']');
			
			day_obj.parent().find('.evo_day').removeClass('on_focus');
			new_day_obj.addClass('on_focus');

			day_obj.closest('.eventon_daily_list').attr('data-fday', new_day);

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
			
			var ev_type = evodata.attr('data-ev_type'),
				ev_type_2 = evodata.attr('data-ev_type_2');
			
			// wether to switch to 1st of month
				other_valsOBJ = ev_cal.find('.eventon_other_vals[name="dv_focus_day"]');
				other_valsDOBJ = ev_cal.find('.eventon_other_vals[name="dv_def_focus_day"]');
				var new_date_el = other_valsOBJ;
				var new_day_ =1;
				
			// move to first month when switching months
				new_day_ = (new_date_el.attr('data-mo1st')=='yes')? 1: new_day;

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
					ev_cal.find('#eventon_loadbar').slideDown();
				},
				type: 'POST',
				url:the_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					// /alert(data);
					ev_cal.find('.eventon_events_list').html(data.content);

					//console.log(new_day+'rrr');
					if(other_valsOBJ.data('mo1st')=='yes'){
						other_valsOBJ.attr('data-day',new_day);						
					}else{
						other_valsOBJ.attr('data-day',new_day).val(new_day);
					}
					other_valsDOBJ.val(new_day);
				},complete:function(){
					ev_cal.find('#eventon_loadbar').slideUp();
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
			eventon_dv_get_new_days(cal_head,'','','filtering');
		});

		$('body').on('click','.evo_filter_submit',function(){
			var cal_head = $(this).closest('.eventon_sorting_section').siblings('.calendar_header');
			eventon_dv_get_new_days(cal_head,'','','filtering');
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
				var evo_dv = cal_head.find('.eventon_other_vals[name="dv_focus_day"]').length;

				if(evo_dv>0)
					eventon_dv_get_new_days(cal_head,'','');
			}
		});

	// MONTH SWITCHING	
		$('.evcal_btn_prev').click(function(){
			var top = $(this).closest('.ajde_evcal_calendar');
			if(top.hasClass('evoDV')){
				var cal_head = $(this).parents('.calendar_header');
				var evo_dv = cal_head.find('.eventon_other_vals[name="dv_focus_day"]').length;		
				if(evo_dv>0){
					eventon_dv_get_new_days(cal_head,'prev','');
				}
			}			
		});
		
		$('.evcal_btn_next').click(function(){	
			var top = $(this).closest('.ajde_evcal_calendar');
			if(top.hasClass('evoDV')){
				var cal_head = $(this).parents('.calendar_header');
				var evo_dv = cal_head.find('.eventon_other_vals[name="dv_focus_day"]').length;		
				if(evo_dv>0){
					eventon_dv_get_new_days(cal_head,'next','');
				}
			}
		});

		// switching months with day strip
			$('.eventon_daily_list').on('click','.evodv_action',function(event){
				if( $(this).hasClass('scroll') ){
					eventon_daily_in = $(this).siblings('.eventon_daily_in');
					if($(this).hasClass('prev')){
						swiping('swiperight', eventon_daily_in);
					}else{// next
						swiping('swipeleft', eventon_daily_in);
					}
				// switch months
				}else{
					classN = ($(this).hasClass('next'))? 'evcal_btn_next':'evcal_btn_prev';
					calendar = $(this).closest('.ajde_evcal_calendar');
					
					calendar.find('.'+classN).trigger('click');
				}				
			});
	
	// AJAX: update the days list for new month
		function eventon_dv_get_new_days(cal_header, change, cday, type){
			
			var cal_id = cal_header.parent().attr('id');

			// stop this from running for other calendars
			if(!cal_header.parent().hasClass('evoDV'))
				return;

			var evodata = cal_header.siblings('.evo-data'),
				cur_m = parseInt(evodata.attr('data-cmonth')),
				cur_y = parseInt(evodata.attr('data-cyear')),
				other_vals = cal_header.find('.eventon_other_vals[name="dv_focus_day"]');
			
			var ev_cal = cal_header.parent();
			
			// new dates
			var new_d = (cday=='')? 
				other_vals.val(): cday;
				if(type=='filtering' && other_vals.data('mo1st') == 'yes'){
					new_d = other_vals.attr('data-day');
				}
			
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
				direction: change,
				continuous_scroll: cal_header.find('.eventon_other_vals[name="dv_continuous_scroll"]').val(),
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
					this_section_days.attr('data-mdays',data.last_date_of_month);
					//update_focus_date(this_section_days, next_d);

					// update current date section 
					update_num_events();

				},complete:function(){
					this_section_days.slideDown('slow');				
					set_daily_strip_sizes();
				}
			});
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
					OBJ.stop().animate({'margin-left': new_marl },function(){
						scroll_o_switch( OBJ.parent().parent() );
					});
				
				}else if(delta == 1){
					OBJ.stop().animate({'margin-left': new_marl },function(){
						scroll_o_switch( OBJ.parent().parent() );
					});
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
				one_day_width = parseInt(OBJ.find('.evo_day:gt(20)').outerWidth());
				maxMLEFT = (width-Pwid)*(-1);

				STRIP = OBJ.closest('.eventon_dv_outter');
				STRIP_Width = STRIP.width();
				ARROW_Width = STRIP.find('span.prev').width() + STRIP.find('span.next').width();

				swipeMove = (one_day_width*5);

				if( (swipeMove + ARROW_Width) > STRIP_Width ){
					swipeMove = (one_day_width*2);
				}
				
				if(direction =='swipeleft'){
					var newLEFT = ( leftNow - swipeMove );	
					// /console.log(newLEFT);

					if( newLEFT*(-1) < (width) ){
						newLEFT = ( newLEFT <maxMLEFT)? maxMLEFT: newLEFT;
						OBJ.stop().animate({'margin-left': newLEFT },function(){
							scroll_o_switch( OBJ.parent() );
						});
					}
				}else{
					var newLEFT = ( leftNow + swipeMove );	
					// /console.log(newLEFT);

					newLEFT = ( newLEFT >0 )? 0: newLEFT;
					OBJ.stop().animate({'margin-left': newLEFT },function(){
						scroll_o_switch( OBJ.parent() );
					});
				}
			}
		// adjust margin left when window resized
			$(window).on('resize', function(){
				$('.eventon_daily_list').each(function(){
					adjust_days_width( $(this));				
				});
			});
		
	// daily list sliders	
		function set_daily_strip_sizes(cal_id){
			if(cal_id!=''){
				var holder = $('#'+cal_id).find('.eventon_daily_list');
				adjust_days_width(holder);
				scroll_o_switch(holder);
			}
			$('.eventon_daily_list').each(function(){
				adjust_days_width( $(this));				
				scroll_o_switch( $(this));				
			});
		}
			function adjust_days_width(holder){
				var day_holder = holder.find('.eventon_daily_in');
				var days = day_holder.children('.evo_day');	
				var day_width = parseInt(day_holder.find('.evo_day:gt(20)').outerWidth());
				OUTTERWidth = parseInt(day_holder.parent().width());

				wALLDAYS = (parseInt(days.length)+2 )* (day_width);
													
				FOCUSday = parseInt(day_holder.closest('.eventon_daily_list').attr('data-fday'));
				LEFTwidth = (FOCUSday-1) * day_width;
				RIGHTwidth = wALLDAYS - LEFTwidth;

				LEFTmargin = ( RIGHTwidth > OUTTERWidth ) ? LEFTwidth: wALLDAYS-OUTTERWidth;
				LEFTmargin = -1* LEFTmargin;

				day_holder.css({'width':wALLDAYS, 'margin-left':LEFTmargin});

				// /console.log(OUTTERWidth+' '+wALLDAYS+' '+LEFTmargin+' '+LEFTwidth+' '+RIGHTwidth);

				//console.log(LEFTmargin);
			}
		// scroll or switch months
			function scroll_o_switch(list){
				holder = list.find('.eventon_daily_in');
				current_marginleft = parseInt(holder.css('marginLeft'));
				max_marginleft = parseInt(holder.parent().width()) - parseInt(holder.width());
				
				//console.log(current_marginleft+' '+max_marginleft);
				if( current_marginleft <= max_marginleft){
					holder.siblings('.next').removeClass('scroll').addClass('switch');
					holder.siblings('.prev').removeClass('switch').addClass('scroll');
				}else if(current_marginleft >= 0){
					holder.siblings('.next').removeClass('switch').addClass('scroll');
					holder.siblings('.prev').removeClass('scroll').addClass('switch');
				}else{
					holder.siblings('.next').attr({'class':'evodv_action next scroll'});
					holder.siblings('.prev').attr({'class':'evodv_action prev scroll'});
				}
			}
		// update current focus date in calendar
			function update_focus_date(OBJ, DATE){
				OBJ.attr('data-fday', DATE);
			}
});