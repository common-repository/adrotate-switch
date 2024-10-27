<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2016 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

function adrotateswitch_import_bannerize() {
	global $wpdb, $current_user, $userdata, $adrotate_config;
	
	if(!adrotateswitch_adrotate_is_active()) {
		wp_redirect('admin.php?page=adrotate-switch');
		die();
	} 

	$now = adrotate_now();

	if(wp_verify_nonce($_POST['adrotateswitch_nonce'], 'adrotateswitch_import_bannerize')) {
		$include_schedules = (isset($_POST['adrotateswitch_import_schedules'])) ? 1 : 0;
		$include_stats = (isset($_POST['adrotateswitch_import_stats'])) ? 1 : 0;
		
		$adverts = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}posts` WHERE `post_type` = 'wp_bannerize' ORDER BY `ID` ASC;");
		if(is_array($adverts)) {
			foreach($adverts as $advert) {

				$meta = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}postmeta` WHERE `post_id` = {$advert->ID} ORDER BY `meta_id` ASC;");
				if($meta->wp_bannerize_banner_type == 'local') {
					// Statistics
					$tracking = ($meta->wp_bannerize_banner_impressions_enabled == 1 OR $meta->wp_bannerize_banner_clicks_enabled == 1) ? 'Y' : 'N';

					// target attr
					$new_window = ($meta->wp_bannerize_banner_target != '') ? ' target="'.esc_attr($advert->target).'"' : '';
						
					// rel attr
					$link_rel = ($meta->wp_bannerize_banner_no_follow != '') ? ' rel="nofollow"' : '';

					// AdCode
					$adcode = '<a href="'.esc_attr($meta->wp_bannerize_banner_link).'"'.$new_window.$link_rel.'><img src="'.$meta->wp_bannerize_banner_url.'" width="'.rtrim($meta->wp_bannerize_banner_width, 'px').'" height="'.rtrim($meta->wp_bannerize_banner_height, 'px').'" /></a>';
					unset($new_window, $link_rel);
				} else if($meta->wp_bannerize_banner_type == 'remote') {
					// Statistics
					$tracking = ($meta->wp_bannerize_banner_impressions_enabled == 1 OR $meta->wp_bannerize_banner_clicks_enabled == 1) ? 'Y' : 'N';

					// target attr
					$new_window = ($meta->wp_bannerize_banner_target != '') ? ' target="'.esc_attr($advert->target).'"' : '';
						
					// rel attr
					$link_rel = ($meta->wp_bannerize_banner_no_follow != '') ? ' rel="nofollow"' : '';

					// AdCode
					$adcode = '<a href="'.esc_attr($meta->wp_bannerize_banner_link).'"'.$new_window.$link_rel.'><img src="'.$meta->wp_bannerize_banner_external_url.'" width="'.rtrim($meta->wp_bannerize_banner_width, 'px').'" height="'.rtrim($meta->wp_bannerize_banner_height, 'px').'" /></a>';
					unset($new_window, $link_rel);
				} else {
					// Statistics
					$tracking = (!preg_match_all('/<(a|script|embed|iframe)[^>](.*?)>/i', $advert->free_html, $things)) ? 'N' : 'Y';

					// AdCode
					$adcode = $advert->post_content;
					unset($things);
				}
	
				// Enabled or Disabled
				$status = ($advert->post_status == 'publish') ? 'active' : 'disabled';
	
				// Description
				$title = ($advert->post_title == '') ? 'Advert '.$advert->id : $advert->post_title;
	
				// Format advert
				// $source, $title, $bannercode, $imagetype, $image, $tracker, $desktop, $mobile, $tablet, $responsive, $type, $weight, $budget, $crate, $irate
				$advertdata = adrotateswitch_format_advert('WP Bannerize', $title, $adcode, '', '', $tracking, 'Y', 'Y', 'Y', 'N', $status, 6, 0, 0, 0);
	
				$wpdb->insert($wpdb->prefix."adrotate", $advertdata);
			    $ad_id = $wpdb->insert_id;

				if($include_schedules == 1) {
					$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Imported schedule for advert '.$ad_id, 'starttime' => $meta->wp_bannerize_banner_date_from, 'stoptime' => $meta->wp_bannerize_banner_date_expiry, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'daystarttime' => '0000', 'daystoptime' => '0000', 'day_mon' => 'Y', 'day_tue' => 'Y', 'day_wed' => 'Y', 'day_thu' => 'Y', 'day_fri' => 'Y', 'day_sat' => 'Y', 'day_sun' => 'Y', 'autodelete' => 'N'));
					$schedule_id = $wpdb->insert_id;
					$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => 0, 'user' => 0, 'schedule' => $schedule_id));
				}
				unset($advertdata, $description, $adcode, $status, $ad_id, $schedule_id);
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