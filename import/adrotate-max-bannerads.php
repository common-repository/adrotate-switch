<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

function adrotateswitch_import_mba() {
	global $wpdb, $current_user, $userdata, $adrotate_config;
	
	if(!adrotateswitch_adrotate_is_active()) {
		wp_redirect('admin.php?page=adrotate-switch');
		die();
	} 

	$now = adrotate_now();

	if(wp_verify_nonce($_POST['adrotateswitch_nonce'], 'adrotateswitch_import_mba')) {
		$include_schedules = (isset($_POST['adrotateswitch_import_schedules'])) ? 1 : 0;
		$include_groups = (isset($_POST['adrotateswitch_import_groups'])) ? 1 : 0;
		$include_stats = (isset($_POST['adrotateswitch_import_stats'])) ? 1 : 0;
		
		if($include_groups == 1) {
			$groups = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."mban_zone` ORDER BY `id` ASC;");

			if(is_array($groups)) {
				foreach($groups as $group) {
					$groupdata = adrotateswitch_format_group('BannerMan', $group->name, 0, 0, '', '', '', '', '', '', 0, 0, '', '', 1, 2, 2, 0, 0, 0, 0, 125, 125, 6000);
	
					$wpdb->insert($wpdb->prefix."adrotate_groups", $groupdata);
					$group2zone[esc_attr($group->ID)] = $wpdb->insert_id;
				}
			} else {
				wp_redirect('admin.php?page=adrotate-switch&s=3');
			}
		}
		
		$adverts = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."mban_banner` ORDER BY `id` ASC;");

		if(is_array($adverts)) {
			foreach($adverts as $advert) {
				if(strlen($advert->text_ad_code) > 0) {
					$adcode = esc_attr($advert->text_ad_code);
				} else {
					// Open in new window?
					if($advert->in_new_win == 1) {
						$new_window = ' target="_blank"';
					} else {
						$new_window = '';
					}
					
					$adcode = '<a href="'.esc_attr($advert->link).'"'.$new_window.'><img src="%image%" /></a>';
				}
	
				// Enabled or Disabled
				if($advert->status == 1) {
					$status = 'active';
				} else {
					$status = 'disabled';
				}
	
				// Format advert
				// $source, $title, $bannercode, $imagetype, $image, $tracker, $desktop, $mobile, $tablet, $responsive, $type, $weight, $budget, $crate, $irate
				$advertdata =  adrotateswitch_format_advert('Max Banner Ads', $advert->name, $adcode, 'field', $advert->url, 'Y', 'Y', 'Y', 'Y', 'N', $status, 6, 0, 0, 0);
		
				$wpdb->insert($wpdb->prefix."adrotate", $advertdata);
			    $ad_id = $wpdb->insert_id;
		
				if($include_schedules == 1) {
					list($eyear, $emonth, $eday) = explode('-', esc_attr($advert->expiry_date));
					$end_date = gmmktime(0, 0, 0, $emonth, $eday, $eyear);
	
					$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Imported schedule for advert '.$ad_id, 'starttime' => $now, 'stoptime' => $end_date, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'daystarttime' => '0000', 'daystoptime' => '0000', 'day_mon' => 'Y', 'day_tue' => 'Y', 'day_wed' => 'Y', 'day_thu' => 'Y', 'day_fri' => 'Y', 'day_sat' => 'Y', 'day_sun' => 'Y', 'autodelete' => 'N'));
					$schedule_id = $wpdb->insert_id;
					$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => 0, 'user' => 0, 'schedule' => $schedule_id));
				}
				
				if($include_groups == 1) {
					$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => $group2zone[esc_attr($advert->zoneid)], 'user' => 0, 'schedule' => 0));
				}
		
				if($include_stats == 1) {
					$wpdb->insert($wpdb->prefix.'adrotate_stats', array('ad' => $ad_id, 'group' => 0, 'thetime' => $now, 'clicks' => esc_attr($advert->clicks), 'impressions' => esc_attr($advert->impressions)));
				}
				
				unset($advertdata, $adcode, $new_window, $track_link, $target_url, $status, $ad_id, $schedule_id);
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