<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

function adrotateswitch_import_sam() {
	global $wpdb, $current_user, $userdata, $adrotate_config;
	
	if(!adrotateswitch_adrotate_is_active()) {
		wp_redirect('admin.php?page=adrotate-switch');
		die();
	} 

	$now = adrotate_now();

	if(wp_verify_nonce($_POST['adrotateswitch_nonce'], 'adrotateswitch_import_sam')) {
		$include_groups = (isset($_POST['adrotateswitch_import_groups'])) ? 1 : 0;
		
		if($include_groups == 1) {
			$blocks = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."sam_blocks` ORDER BY `id` ASC;");
			if(is_array($blocks)) {
				foreach($blocks as $block) {
					$margin = explode(" ", $block->b_margin);
	
					$blockdata = adrotateswitch_format_group('Simple Ads Manager', $block->name, 2, 0, '', '', '', '', '', '', 0, 0, '', '', 1, $block->b_lines, $block->b_cols, rtrim($margin[0], 'px'), rtrim($margin[2], 'px'), rtrim($margin[3], 'px'), rtrim($margin[1], 'px'), 'auto', 'auto', 6000);
	
					$wpdb->insert($wpdb->prefix."adrotate_groups", $blockdata);
				}
			} else {
				wp_redirect('admin.php?page=adrotate-switch&s=3');
			}

			$groups = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."sam_places` ORDER BY `id` ASC;");
			if(is_array($groups)) {
				foreach($groups as $group) {
					$width = ($group->place_custom_width > 0) ? $group->place_custom_width : 'auto';
					$height = ($group->place_custom_height > 0) ? $group->place_custom_height : 'auto';
	
					$groupdata = adrotateswitch_format_group('Simple Ads Manager', $group->name, 0, 0, '', '', '', '', '', '', 0, 0, '', '', 1, 2, 2, 0, 0, 0, 0, $width, $height, 6000);
	
					$wpdb->insert($wpdb->prefix."adrotate_groups", $groupdata);
					$group2advert[esc_attr($group->ID)] = $wpdb->insert_id;
	
					unset($width, $height);
				}
			} else {
				wp_redirect('admin.php?page=adrotate-switch&s=3');
			}
		}
		
		$adverts = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."sam_ads` ORDER BY `id` ASC;");
		if(is_array($adverts)) {
			foreach($adverts as $advert) {
				if($advert->code_mode == 1) {
					$adcode = esc_attr($advert->ad_code);
					$imagetype = '';
					$image = '';
				} else {				
					$adcode = '<a href="'.esc_attr($advert->ad_target).'" target="_blank"><img src="%image%" /></a>';
					$imagetype = 'field';
					$image = esc_attr($advert->ad_img);
				}
	
				// Enabled or Disabled
				if($advert->ad_weight > 0) {
					$status = 'active';
				} else {
					$status = 'disabled';
				}
	
				// Convert Weight
				$weight = 6;
				if($advert->ad_weight == 1) $weight = 2;
				if($advert->ad_weight == 3) $weight = 4;
				if($advert->ad_weight == 5) $weight = 6;
				if($advert->ad_weight == 7) $weight = 8;
				if($advert->ad_weight == 9) $weight = 10;
	
				// Format advert
				// $source, $title, $bannercode, $imagetype, $image, $tracker, $desktop, $mobile, $tablet, $responsive, $type, $weight, $budget, $crate, $irate
				$advertdata =  adrotateswitch_format_advert('Max Banner Ads', $advert->name, $adcode, $imagetype, $image, 'Y', 'Y', 'Y', 'Y', 'N', $status, $weight, $advert->per_month, $advert->cpc, $advert->cpm);
		
				$wpdb->insert($wpdb->prefix."adrotate", $advertdata);
			    $ad_id = $wpdb->insert_id;
		
				$start_date = adrotate_now();
				$end_date = $start_date + 7257600;
				list($syear, $smonth, $sday) = explode('-', esc_attr($advert->ad_start_date));
				if($syear > 0 AND $smonth > 0 AND $sday > 0) $start_date = gmmktime(0, 0, 0, $smonth, $sday, $syear);
				list($eyear, $emonth, $eday) = explode('-', esc_attr($advert->ad_end_date));
				if($eyear > 0 AND $emonth > 0 AND $eday > 0) $end_date = gmmktime(0, 0, 0, $emonth, $eday, $eyear);
	
				$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Imported schedule for advert '.$ad_id, 'starttime' => $start_date, 'stoptime' => $end_date, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'daystarttime' => '0000', 'daystoptime' => '0000', 'day_mon' => 'Y', 'day_tue' => 'Y', 'day_wed' => 'Y', 'day_thu' => 'Y', 'day_fri' => 'Y', 'day_sat' => 'Y', 'day_sun' => 'Y', 'autodelete' => 'N'));
				$schedule_id = $wpdb->insert_id;
				$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => 0, 'user' => 0, 'schedule' => $schedule_id));
	
				if($include_groups == 1) {
					$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => $group2advert[esc_attr($advert->pid)], 'user' => 0, 'schedule' => 0));
				}
		
				unset($advertdata, $adcode, $status, $weight, $ad_id, $syear, $smonth, $sday, $eyear, $emonth, $eday, $start_date, $end_date, $schedule_id);
			}
		} else {
			wp_redirect('admin.php?page=adrotate-switch&s=2');
		}
	
		wp_redirect('admin.php?page=adrotate-switch&s=1');
	} else {
		adrotate_nonce_error();
	}
}
?>