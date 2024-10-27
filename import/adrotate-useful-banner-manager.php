<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

function adrotateswitch_import_ubm() {
	global $wpdb, $current_user, $userdata, $adrotate_config;
	
	if(!adrotateswitch_adrotate_is_active()) {
		wp_redirect('admin.php?page=adrotate-switch');
		die();
	} 

	$now = adrotate_now();

	if(wp_verify_nonce($_POST['adrotateswitch_nonce'], 'adrotateswitch_import_ubm')) {
		$include_schedules = (isset($_POST['adrotateswitch_import_schedules'])) ? 1 : 0;
		
		$adverts = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."useful_banner_manager_banners` ORDER BY `id` ASC;");
		if(is_array($adverts)) {
			foreach($adverts as $advert) {
				// target attr
				$new_window = ' target="'.esc_attr($advert->link_target).'"';
					
				// rel attr
				$link_rel = ' rel="'.esc_attr($advert->link_rel).'"';
					
				// alt attr
				$alt_attr = ' alt="'.esc_attr($advert->banner_alt).'"';
					
				$adcode = '<a href="'.esc_attr($advert->banner_link).'"'.$new_window.' '.$link_rel.'><img'.$alt_attr.' src="%image%" /></a>';
	
				// Enabled or Disabled
				if($advert->is_visible == 'yes') {
					$status = 'active';
				} else {
					$status = 'disabled';
				}
	
				// Format advert
				$upload_dir = wp_upload_dir();
				$image = $upload_dir['baseurl'].'/useful_banner_manager_banners/'.esc_attr($advert->id).'-'.esc_attr($advert->banner_name).'.'.esc_attr($advert->banner_type);
				// $source, $title, $bannercode, $imagetype, $image, $tracker, $desktop, $mobile, $tablet, $responsive, $type, $weight, $budget, $crate, $irate
				$advertdata =  adrotateswitch_format_advert('Useful Banner Manager', $advert->banner_title, $adcode, 'field', $image, 'Y', 'Y', 'Y', 'Y', 'N', $status, 6, 0, 0, 0);
	
				$wpdb->insert($wpdb->prefix."adrotate", $advertdata);
			    $ad_id = $wpdb->insert_id;
		
				if($include_schedules == 1) {
					list($eyear, $emonth, $eday) = explode('-', esc_attr($advert->active_until));
					$end_date = gmmktime(0, 0, 0, $emonth, $eday, $eyear);
	
					$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Imported schedule for advert '.$ad_id, 'starttime' => $now, 'stoptime' => $end_date, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'daystarttime' => '0000', 'daystoptime' => '0000', 'day_mon' => 'Y', 'day_tue' => 'Y', 'day_wed' => 'Y', 'day_thu' => 'Y', 'day_fri' => 'Y', 'day_sat' => 'Y', 'day_sun' => 'Y', 'autodelete' => 'N'));
					$schedule_id = $wpdb->insert_id;
					$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => 0, 'user' => 0, 'schedule' => $schedule_id));
				}
	
				unset($advertdata, $adcode, $new_window, $link_rel, $track_link, $target_url, $status, $ad_id, $schedule_id);
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