<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

function adrotateswitch_import_wppas() {
	global $wpdb, $current_user, $userdata, $adrotate_config;
	
	if(!adrotateswitch_adrotate_is_active()) {
		wp_redirect('admin.php?page=adrotate-switch');
		die();
	} 

	$now = adrotate_now();
	$in84days = $now + 7257600;

	if(wp_verify_nonce($_POST['adrotateswitch_nonce'], 'adrotateswitch_import_wppas')) {
		$include_groups = (isset($_POST['adrotateswitch_import_groups'])) ? 1 : 0;
		$include_schedules = (isset($_POST['adrotateswitch_import_schedules'])) ? 1 : 0;
		
		if($include_groups == 1) {
			$groups = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}posts` WHERE `post_type` = 'adzones' AND `post_status` = 'publish' ORDER BY `id` ASC;");
			if(is_array($groups)) {
				foreach($groups as $group) {
					$meta_group = get_post_meta($group->ID);
					
					// Advert sizing
					if(strlen($meta_advert['_adzone_size'][0] > 0)) {
						list($group_width, $group_height) = explode("x", $meta_advert['_adzone_size'][0]);
					} else {
						$group_width = $group_height = 125;
					}
	
					// Modus
					if($meta_advert['_adzone_grid_horizontal'][0] > 0 AND $meta_advert['_adzone_grid_vertical'][0] > 0) {
						$modus = 2;
						$rows = $meta_advert['_adzone_grid_horizontal'][0];
						$columns = $meta_advert['_adzone_grid_vertical'][0];
					} else {
						$modus = 0;
						$rows = $columns = 2;
					}
					
					// Rotation
					if($meta_advert['_adzone_rotation_time'][0] == 1) {
						$rotation = $meta_advert['_adzone_rotation_time'][0] * 1000;
					} else {
						$rotation = 6000;
					}
	
					// Centering
					if($meta_advert['_adzone_center'][0] == 1) {
						$center = 3;
					} else {
						$center = 0;
					}
					
					$groupdata = adrotateswitch_format_group('WP Pro Ad System', $group->post_title, $modus, 0, '', '', '', '', '', '', 0, 0, '', '', $center, $rows, $columns, 0, 0, 0, 0, $group_width, $group_height, $rotation);
	
					$wpdb->insert($wpdb->prefix."adrotate_groups", $groupdata);
					$group2zone[esc_attr($group->ID)] = $wpdb->insert_id;
					
					unset($groupdata, $group_width, $group_height, $modus, $rows, $columns, $rotation, $center);
				}
			} else {
				wp_redirect('admin.php?page=adrotate-switch&s=3');
			}
		}

		$adverts = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."posts` WHERE `post_type` = 'banners' ORDER BY `id` ASC;");
		if(is_array($adverts)) {
			foreach($adverts as $advert) {
				$meta_advert = get_post_meta($advert->ID);
	
				// Open in new window?
				if($meta_advert['_banner_target'][0] == '_blank') {
					$new_window = ' target="_blank"';
				} else {
					$new_window = '';
				}
		
				// Enabled or Disabled
				if($advert->post_status == 'publish') {
					$status = 'active';
				} else {
					$status = 'disabled';
				}
		
				// Format advert (Desktop)
				$desktop_image = $desktop_imagetype = $desktop_adcode = '';
				if(strlen($meta_advert['_banner_html'][0]) > 0) {
					$desktop_adcode = esc_attr($meta_advert['_banner_html'][0]);
					$desktop_image = '';
					$desktop_imagetype = '';
				} else {
					$desktop_adcode = '<a href="'.esc_attr($meta_advert['_banner_link'][0]).'"'.$new_window.'><img src="%image%" /></a>';
					$desktop_image = $meta_advert['_banner_url'][0];
					$desktop_imagetype = 'field';
				}
		
				// $source, $title, $bannercode, $imagetype, $image, $tracker, $desktop, $mobile, $tablet, $responsive, $type, $weight, $budget, $crate, $irate
				$desktop_advertdata =  adrotateswitch_format_advert('WP Pro Ad System', $advert->post_title.' (Desktop #'.$advert->ID.')', $desktop_adcode, $desktop_imagetype, $desktop_image, 'Y', 'Y', 'N', 'N', 'N', $status, 6, 0, 0, 0);
		
				$wpdb->insert($wpdb->prefix."adrotate", $desktop_advertdata);
			    $desktop_ad_id = $wpdb->insert_id;
				$ads2schedule[] = $desktop_ad_id;
			    unset($desktop_image, $desktop_imagetype, $desktop_adcode, $desktop_advertdata);
	
	
				$tablet_ad_id = $phone_ad_id = 0;
				// Format advert (Tablet)
				if($meta_advert['_banner_html_tablet_portrait'][0] OR $meta_advert['_banner_url_tablet_portrait'][0]) {
					$tablet_image = $tablet_imagetype = $tablet_adcode = '';
					if(strlen($meta_advert['_banner_html_tablet_portrait'][0]) > 0) {
						$tablet_adcode = esc_attr($meta_advert['_banner_html_tablet_portrait'][0]);
						$tablet_image = '';
						$tablet_imagetype = '';
					} else {
						$tablet_adcode = '<a href="'.esc_attr($meta_advert['_banner_link'][0]).'"'.$new_window.'><img src="%image%" /></a>';
						$tablet_image = $meta_advert['_banner_url_tablet_portrait'][0];
						$tablet_imagetype = 'field';
					}
			
					// $source, $title, $bannercode, $imagetype, $image, $tracker, $desktop, $mobile, $tablet, $responsive, $type, $weight, $budget, $crate, $irate
					$tablet_advertdata =  adrotateswitch_format_advert('WP Advertize it', $advert->post_title.' (Tablet #'.$advert->ID.')', $tablet_adcode, $tablet_imagetype, $tablet_image, 'Y', 'N', 'N', 'Y', 'N', $status, 6, 0, 0, 0);
			
					$wpdb->insert($wpdb->prefix."adrotate", $tablet_advertdata);
				    $tablet_ad_id = $wpdb->insert_id;
					$ads2schedule[] = $tablet_ad_id;
				    unset($tablet_image, $tablet_imagetype, $tablet_adcode, $tablet_advertdata);
				}
	
				// Format advert (Smartphone)
				if($meta_advert['_banner_html_phone_portrait'][0] OR $meta_advert['_banner_url_phone_portrait'][0]) {
					$phone_image = $phone_imagetype = $phone_adcode = '';
					if(strlen($meta_advert['_banner_html_phone_portrait'][0]) > 0) {
						$phone_adcode = esc_attr($meta_advert['_banner_html_phone_portrait'][0]);
						$phone_image = '';
						$phone_imagetype = '';
					} else {
						$phone_adcode = '<a href="'.esc_attr($meta_advert['_banner_link'][0]).'"'.$new_window.'><img src="%image%" /></a>';
						$phone_image = $meta_advert['_banner_url_phone_portrait'][0];
						$phone_imagetype = 'field';
					}
			
					// $source, $title, $bannercode, $imagetype, $image, $tracker, $desktop, $mobile, $tablet, $responsive, $type, $weight, $budget, $crate, $irate
					$phone_advertdata =  adrotateswitch_format_advert('WP Pro Ad System', $advert->post_title.' (Mobile #'.$advert->ID.')', $phone_adcode, $phone_imagetype, $phone_image, 'Y', 'N', 'Y', 'N', 'N', $status, 6, 0, 0, 0);
			
					$wpdb->insert($wpdb->prefix."adrotate", $phone_advertdata);
				    $phone_ad_id = $wpdb->insert_id;
					$ads2schedule[] = $tablet_ad_id;
				    unset($phone_image, $phone_imagetype, $phone_adcode, $phone_advertdata);
				}
	
				$adzones = maybe_unserialize($meta_advert['_linked_adzones'][0]);
				if($include_groups == 1 AND is_array($adzones)) {
					foreach($adzones as $key => $adzone) {
						$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $desktop_ad_id, 'group' => $group2zone[esc_attr($adzone)], 'user' => 0, 'schedule' => 0));
						if($tablet_ad_id > 0) {
							$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $tablet_ad_id, 'group' => $group2zone[esc_attr($adzone)], 'user' => 0, 'schedule' => 0));
						}
						if($phone_ad_id > 0) {
							$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $phone_ad_id, 'group' => $group2zone[esc_attr($adzone)], 'user' => 0, 'schedule' => 0));
						}
						
						// Enable mobile support in group
						$wpdb->update($wpdb->prefix.'adrotate_groups', array('mobile' => 1), array('id' => $group2zone[esc_attr($adzone)]));
					}
				}
					
				unset($meta_advert, $new_window, $status, $adzones);
			}
	
			if($include_schedules == 1) {
				$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'WP Pro Ad System schedule', 'starttime' => $now, 'stoptime' => $in84days, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'daystarttime' => '0000', 'daystoptime' => '0000', 'day_mon' => 'Y', 'day_tue' => 'Y', 'day_wed' => 'Y', 'day_thu' => 'Y', 'day_fri' => 'Y', 'day_sat' => 'Y', 'day_sun' => 'Y', 'autodelete' => 'N'));
				$schedule_id = $wpdb->insert_id;
	
				foreach($ads2schedule as $key => $ad_id) {
					$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => 0, 'user' => 0, 'schedule' => $schedule_id));
				}
			}
			unset($ads2schedule, $ad_id, $schedule_id);
		} else {
			wp_redirect('admin.php?page=adrotate-switch&s=2');
		}
	
		wp_redirect('admin.php?page=adrotate-switch&s=1');
	} else {
		adrotate_nonce_error();
	}
}
?>