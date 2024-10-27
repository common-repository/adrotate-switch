<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

function adrotateswitch_import_advertising_manager() {
	global $wpdb, $current_user, $userdata, $adrotate_config;
	
	if(!adrotateswitch_adrotate_is_active()) {
		wp_redirect('admin.php?page=adrotate-switch');
		die();
	} 

	$now = adrotate_now();
	$in84days = $now + 7257600;

	if(wp_verify_nonce($_POST['adrotateswitch_nonce'], 'adrotateswitch_import_am')) {
		$include_schedules = (isset($_POST['adrotateswitch_import_schedules'])) ? 1 : 0;

		$data = maybe_unserialize(get_option('plugin_advman_ads'));

		if(is_array($data)) {	
			// $source, $title, $bannercode, $imagetype, $image, $tracker, $desktop, $mobile, $tablet, $responsive, $type, $weight, $budget, $crate, $irate
			foreach($data as $id => $value) {
				$before = (array_key_exists('html-before', $value)) ? $value['html-before'] : '';
				$after = (array_key_exists('html-after', $value)) ? $value['html-after'] : '';
				$adcode = $before.$value['code'].$after;
				$active = ($value['active'] == 1) ? 'active' : 'disabled';
				
				// Format advert
				$advertdata =  adrotateswitch_format_advert('Advertising Manager', 'banner '.$value['id'].' '.$value['name'], $adcode, '', '', 'Y', 'Y', 'Y', 'Y', 'N', $active, 6, 0, 0, 0);
	
				$wpdb->insert($wpdb->prefix."adrotate", $advertdata);
			    $ad_id = $wpdb->insert_id;
				$ads2schedule[] = $ad_id;
				
				unset($advertdata, $ad_id);
			}
	
			if($include_schedules == 1) {
				$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Ad Injection schedule', 'starttime' => $now, 'stoptime' => $in84days, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'daystarttime' => '0000', 'daystoptime' => '0000', 'day_mon' => 'Y', 'day_tue' => 'Y', 'day_wed' => 'Y', 'day_thu' => 'Y', 'day_fri' => 'Y', 'day_sat' => 'Y', 'day_sun' => 'Y', 'autodelete' => 'N'));
				$schedule_id = $wpdb->insert_id;
	
				foreach($ads2schedule as $key => $ad_id) {
					$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => 0, 'user' => 0, 'schedule' => $schedule_id));
				}
			}
			unset($ads2schedule, $ad_id, $schedule_id, $active, $adcode, $before, $after);
		} else {
			wp_redirect('admin.php?page=adrotate-switch&s=3');
		}
	
		wp_redirect('admin.php?page=adrotate-switch&s=1');
	} else {
		adrotate_nonce_error();
	}
}
?>