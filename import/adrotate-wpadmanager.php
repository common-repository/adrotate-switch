<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

function adrotateswitch_import_wpadmanager() {
	global $wpdb, $current_user, $userdata, $adrotate_config;
	
	if(!adrotateswitch_adrotate_is_active()) {
		wp_redirect('admin.php?page=adrotate-switch');
		die();
	} 

	$now = adrotate_now();
	$in84days = $now + 7257600;
	$adverts = $groups = array();

	if(wp_verify_nonce($_POST['adrotateswitch_nonce'], 'adrotateswitch_import_wpadmanager')) {
		$include_schedules = (isset($_POST['adrotateswitch_import_schedules'])) ? 1 : 0;
		$include_groups = (isset($_POST['adrotateswitch_import_groups'])) ? 1 : 0;

		$data_id = get_option('administer_post_id');

		if($include_groups == 1) {
			$groups = get_post_meta($data_id, 'administer_positions');
			if(is_array($groups[0])) {
				foreach($groups[0] as $name => $group) {
					$type = '';
					if($group['type'] == 'template') $type = ' (Template)';
					if($group['type'] == 'widget') $type = ' (Widget)';
	
					$groupdata = adrotateswitch_format_group('WP Ad Manager', $name.$type, 0, 0, '', '', '', '', '', '', 0, 0, $group['before'], $group['after'], 1, 2, 2, 0, 0, 0, 0, 125, 125, 6000);
	
					$wpdb->insert($wpdb->prefix."adrotate_groups", $groupdata);
					$group2ad[esc_attr($group['position'])] = $wpdb->insert_id;
				}
			} else {
				wp_redirect('admin.php?page=adrotate-switch&s=3');
			}
		}

		$adverts = get_post_meta($data_id, 'administer_content');
		if(is_array($adverts[0])) {
			foreach($adverts[0] as $advert) {
				// Fix the code and make ampersands validate
				$adcode = str_replace('%tracker%', '', stripslashes($advert['code']));		
				$adcode = preg_replace('/&([^#])(?![a-zA-Z1-4]{1,8};)/', '&amp;$1', $adcode);	
	
				// Fix the dates
				$parts = explode(':', trim($advert['scheduele'])); // Yes, scheduele... :-/
				$start = (isset($parts[0])) ? strtotime($parts[0] . ' 00:00:00') : $now;
				$stop = (isset($parts[1])) ? strtotime($parts[1] . ' 23:59:59') : $in84days;
				
				// Status
				$status = ($advert['show'] == 'on') ? 'active' : 'disabled';
	
				// Format advert
				// $source, $title, $bannercode, $imagetype, $image, $tracker, $desktop, $mobile, $tablet, $responsive, $type, $weight, $budget, $crate, $irate
				$advertdata =  adrotateswitch_format_advert('WP Ad Manager', $advert['title'], $adcode, '', '', 'N', 'Y', 'Y', 'Y', 'N', $status, 6, 0, 0, 0);
	
				$wpdb->insert($wpdb->prefix."adrotate", $advertdata);
			    $ad_id = $wpdb->insert_id;
	
				if($include_schedules == 1) {
					$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Schedule for advert '.$ad_id, 'starttime' => $start, 'stoptime' => $stop, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'daystarttime' => '0000', 'daystoptime' => '0000', 'day_mon' => 'Y', 'day_tue' => 'Y', 'day_wed' => 'Y', 'day_thu' => 'Y', 'day_fri' => 'Y', 'day_sat' => 'Y', 'day_sun' => 'Y'));
					$schedule_id = $wpdb->insert_id;
		
					$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => 0, 'user' => 0, 'schedule' => $schedule_id));
					
					unset($schedule_id, $start, $stop);
				}
		
				if($include_groups == 1) {
					$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => $group2ad[$advert['position']], 'user' => 0, 'schedule' => 0));
				}
	
				unset($advertdata, $group2ad, $ad_id, $status, $adcode, $parts, $start, $stop);
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