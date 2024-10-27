<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

function adrotateswitch_import_aas() {
	global $wpdb, $adrotate_config;
	
	if(!adrotateswitch_adrotate_is_active()) {
		wp_redirect('admin.php?page=adrotate-switch');
		die();
	} 

	$now = adrotate_now();
	$in84days = $now + 7257600;

	if(wp_verify_nonce($_POST['adrotateswitch_nonce'], 'adrotateswitch_import_aas')) {
		$include_schedules = (isset($_POST['adrotateswitch_import_schedules'])) ? 1 : 0;
		
		$adverts = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}posts` WHERE `post_type` = 'ads_banner' ORDER BY `id` ASC;");

		if(is_array($adverts)) {
			foreach($adverts as $advert) {
				$meta_advert = get_post_meta($advert->ID);

				// Open in new window?
				if($meta_advert['banner_target'][0] == '_blank') {
					$new_window = ' target="_blank"';
				} else {
					$new_window = '';
				}
	
				// Image Url and AdCode
				$image = $imagetype = $adcode = '';
				if(strlen(unserialize($meta_advert['custom_html'][0])['html']) > 0) {
					$adcode = str_replace('%link%', $meta_advert['banner_link'][0], unserialize($meta_advert['custom_html'][0])['html']);
					$image = '';
					$imagetype = '';
				} else {
					$image_id = $meta_advert['_thumbnail_id'][0];
					$upload_dir = wp_upload_dir();
					$image_or_text = ($image_id > 0) ? '<img src="%image%" />' : esc_attr($advert->post_title);

					$adcode = '<a href="'.$meta_advert['banner_link'][0].'"'.$new_window.'>'.$image_or_text.'</a>';
					$image = ($image_id > 0) ? $upload_dir['baseurl'].get_post_meta($image_id, '_wp_attached_file', 1) : '';
					$imagetype = ($image_id > 0) ? 'field' : '';
				}
	
				// Enabled or Disabled
				if($advert->post_status == 'publish') {
					$status = 'active';
				} else {
					$status = 'disabled';
				}
	
				// Format advert
				// $source, $title, $bannercode, $imagetype, $image, $tracker, $desktop, $mobile, $tablet, $responsive, $type, $weight, $budget, $crate, $irate
				$advertdata = adrotateswitch_format_advert('Advanced Advertising System', 'banner '.$advert->ID, $adcode, $imagetype, $image, 'Y', 'Y', 'Y', 'Y', 'N', $status, 6, 0, 0, 0);
		
				$wpdb->insert($wpdb->prefix."adrotate", $advertdata);
			    $ad_id = $wpdb->insert_id;
		
				if($include_schedules == 1) {
					$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Generated schedule for advert '.$ad_id, 'starttime' => $now, 'stoptime' => $in84days, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'daystarttime' => '0000', 'daystoptime' => '0000', 'day_mon' => 'Y', 'day_tue' => 'Y', 'day_wed' => 'Y', 'day_thu' => 'Y', 'day_fri' => 'Y', 'day_sat' => 'Y', 'day_sun' => 'Y', 'autodelete' => 'N'));
					$schedule_id = $wpdb->insert_id;
					$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => 0, 'user' => 0, 'schedule' => $schedule_id));
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