<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

function adrotateswitch_import_adking() {
	global $wpdb, $adrotate_config;
	
	if(!adrotateswitch_adrotate_is_active()) {
		wp_redirect('admin.php?page=adrotate-switch');
		die();
	} 

	$now = adrotate_now();

	if(wp_verify_nonce($_POST['adrotateswitch_nonce'], 'adrotateswitch_import_adking')) {
		$include_schedules = (isset($_POST['adrotateswitch_import_schedules'])) ? 1 : 0;
		
		$adverts = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}posts` WHERE `post_type` = 'adverts_posts' ORDER BY `id` ASC;");

		if(is_array($adverts)) {
			foreach($adverts as $advert) {
				$meta_advert = get_post_meta($advert->ID);
	
				if($meta_advert['akp_media_type'][0] == 'image' OR $meta_advert['akp_media_type'][0] == 'text' OR $meta_advert['akp_media_type'][0] == 'adsense' OR $meta_advert['akp_media_type'][0] == 'html5') {
					// Open in new window?
					if($meta_advert['akp_target'][0] == 'blank') {
						$new_window = ' target="_blank"';
					} else {
						$new_window = '';
					}
		
					// Image Url and AdCode
					$image = $imagetype = $adcode = '';
					if($meta_advert['akp_media_type'][0] == 'image') {
						$image_id = $wpdb->get_var("SELECT `ID` FROM `".$wpdb->prefix."posts` WHERE `post_type` = 'attachment' AND `post_parent` = ".$advert->ID." AND `post_status` = 'inherit' ORDER BY `id` ASC;");
						$upload_dir = wp_upload_dir();
		
						$adcode = '<a href="'.esc_attr($advert->post_title).'"'.$new_window.'><img src="%image%" /></a>';
						$image = $upload_dir['baseurl'].get_post_meta($image_id, '_wp_attached_file', 1);
						$imagetype = 'field';
					} else if($meta_advert['akp_media_type'][0] == 'text') {
						$adcode = '<a href="'.esc_attr($advert->post_title).'"'.$new_window.'>'.get_post_meta($advert->ID, 'akp_text', 1).'</a>';
						$image = '';
						$imagetype = '';
					} else if($meta_advert['akp_media_type'][0] == 'adsense') {
						$adcode = get_post_meta($advert->ID, 'akp_adsense_code', 1);
						$image = '';
						$imagetype = '';
					} else if($meta_advert['akp_media_type'][0] == 'html5') {
						$adcode = '<iframe src="%image%" height="'.get_post_meta($advert->ID, 'akp_html5_height', 1).'" width="'.get_post_meta($advert->ID, 'akp_html5_width', 1).'" frameborder="0" style="border:none;"></iframe>';
						$image = get_post_meta($advert->ID, 'akp_html5_url', 1);
						$imagetype = 'field';
					}
		
					// Enabled or Disabled
					if($advert->post_status == 'publish') {
						$status = 'active';
					} else {
						$status = 'disabled';
					}
		
					// Format advert
					// $source, $title, $bannercode, $imagetype, $image, $tracker, $desktop, $mobile, $tablet, $responsive, $type, $weight, $budget, $crate, $irate
					$advertdata =  adrotateswitch_format_advert('Ad King Pro', 'banner '.$advert->ID, $adcode, $imagetype, $image, 'Y', 'Y', 'Y', 'Y', 'N', $status, 6, 0, 0, 0);
			
					$wpdb->insert($wpdb->prefix."adrotate", $advertdata);
				    $ad_id = $wpdb->insert_id;
			
					if($include_schedules == 1) {
						$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Imported schedule for advert '.$ad_id, 'starttime' => $now, 'stoptime' => $meta_advert['akp_expiry_date'][0], 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'daystarttime' => '0000', 'daystoptime' => '0000', 'day_mon' => 'Y', 'day_tue' => 'Y', 'day_wed' => 'Y', 'day_thu' => 'Y', 'day_fri' => 'Y', 'day_sat' => 'Y', 'day_sun' => 'Y', 'autodelete' => 'N'));
						$schedule_id = $wpdb->insert_id;
						$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => 0, 'user' => 0, 'schedule' => $schedule_id));
					}
					
					unset($advertdata, $adcode, $new_window, $track_link, $target_url, $status, $ad_id, $schedule_id);
				}
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