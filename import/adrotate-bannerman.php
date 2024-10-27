<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

function adrotateswitch_import_bannerman() {
	global $wpdb, $current_user, $userdata, $adrotate_config;
	
	if(!adrotateswitch_adrotate_is_active()) {
		wp_redirect('admin.php?page=adrotate-switch');
		die();
	} 

	$now = adrotate_now();
	$in84days = $now + 7257600;

	if(wp_verify_nonce($_POST['adrotateswitch_nonce'], 'adrotateswitch_import_bannerman')) {
		$include_schedules = (isset($_POST['adrotateswitch_import_schedules'])) ? 1 : 0;

		$data = maybe_unserialize(get_option('bannerman'));

		if(is_array($data)) {
			// Convert interfal
			if($data['refresh'] == 30) $data['refresh'] = 35;
			if($data['refresh'] == 40) $data['refresh'] = 45;
			if($data['refresh'] == 50) $data['refresh'] = 45;
	
			// Determine loation
			if($data['display'] == 'none') $data['display'] = 0;
			if($data['display'] == 'top') $data['display'] = 1;
			if($data['display'] == 'bottom') $data['display'] = 2;
	
			// List all pages
			$pages = get_pages(array('sort_column' => 'ID', 'sort_order' => 'asc'));
			$page_list = '';	
			if(!empty($pages)) {
				foreach($pages as $page) {
					$page_list .= $page_list.','.$page->ID;
				}
			}
	
			$modus = ($data['refresh'] > 0) ? 1 : 0;
			$adspeed = ($data['refresh'] > 0) ? $data['refresh']*1000 : 6000;
			$groupdata = adrotateswitch_format_group('BannerMan', '', $modus, 0, '', '', '', $page_list, $data['display'], '', 0, 0, '<center>', '</center>', 1, 2, 2, 0, 0, 0, 0, 125, 125, $adspeed);
	
			$wpdb->insert($wpdb->prefix."adrotate_groups", $groupdata);
			$group_id = $wpdb->insert_id;
	
			// $source, $title, $bannercode, $imagetype, $image, $tracker, $desktop, $mobile, $tablet, $responsive, $type, $weight, $budget, $crate, $irate
			foreach($data['banners'] as $key => $value) {
				// Format advert
				$new_id = $key + 1;
				$advertdata =  adrotateswitch_format_advert('Bannerman', 'banner '.$new_id, $value, '', '', 'N', 'Y', 'Y', 'Y', 'N', 'active', 6, 0, 0, 0);
	
				$wpdb->insert($wpdb->prefix."adrotate", $advertdata);
			    $ad_id = $wpdb->insert_id;
				$ads2schedule[] = $ad_id;
	
				$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => $group_id, 'user' => 0, 'schedule' => 0));
				
				unset($advertdata, $ad_id);
			}
	
			if($include_schedules == 1) {
				$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'BannerMan schedule', 'starttime' => $now, 'stoptime' => $in84days, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'daystarttime' => '0000', 'daystoptime' => '0000', 'day_mon' => 'Y', 'day_tue' => 'Y', 'day_wed' => 'Y', 'day_thu' => 'Y', 'day_fri' => 'Y', 'day_sat' => 'Y', 'day_sun' => 'Y', 'autodelete' => 'N'));
				$schedule_id = $wpdb->insert_id;
	
				foreach($ads2schedule as $key => $ad_id) {
					$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => 0, 'user' => 0, 'schedule' => $schedule_id));
				}
			}
			unset($ads2schedule, $ad_id, $groupdata, $group_id, $schedule_id, $modus, $adspeed);
		} else {
			wp_redirect('admin.php?page=adrotate-switch&s=3');
		}
	
		wp_redirect('admin.php?page=adrotate-switch&s=1');
	} else {
		adrotate_nonce_error();
	}
}
?>