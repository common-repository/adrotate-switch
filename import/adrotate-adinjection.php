<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

function adrotateswitch_import_adinjection() {
	global $wpdb, $adrotate_config;
	
	if(!adrotateswitch_adrotate_is_active()) {
		wp_redirect('admin.php?page=adrotate-switch');
		die();
	} 

	$now = adrotate_now();
	$in84days = $now + 7257600;

	if(wp_verify_nonce($_POST['adrotateswitch_nonce'], 'adrotateswitch_import_adinjection')) {
		$include_schedules = (isset($_POST['adrotateswitch_import_schedules'])) ? 1 : 0;

		$data = get_option('adinj_options');

		if(is_array($data)) {
			for($n=1;$n<11;$n++) {
				if(strlen($data['ad_code_top_'.$n]) > 0) {
					$adverts[] = array('type' => 'top', 'code' => $data['ad_code_top_'.$n], 'id' => $n);
				}
				if(strlen($data['ad_code_bottom_'.$n]) > 0) {
					$adverts[] = array('type' => 'bottom', 'code' => $data['ad_code_bottom_'.$n], 'id' => $n);
				}
				if(strlen($data['ad_code_footer_'.$n]) > 0) {
					$adverts[] = array('type' => 'footer', 'code' => $data['ad_code_footer_'.$n], 'id' => $n);
				}
				if(strlen($data['ad_code_random_'.$n]) > 0) {
					$adverts[] = array('type' => 'random', 'code' => $data['ad_code_random_'.$n], 'id' => $n);
				}
			}
			sort($adverts);

			foreach($adverts as $key => $value) {
				// Format advert
				$title = '('.$value['type'].' #'.$value['id'].')';
				// $source, $title, $bannercode, $imagetype, $image, $tracker, $desktop, $mobile, $tablet, $responsive, $type, $weight, $budget, $crate, $irate
				$advertdata =  adrotateswitch_format_advert('Ad Injection', $title, $value['code'], '', '', 'N', 'Y', 'Y', 'Y', 'N', 'active', 6, 0, 0, 0);
		
				$wpdb->insert($wpdb->prefix."adrotate", $advertdata);
				$ads2schedule[] = $wpdb->insert_id;
					
				unset($advertdata, $title);
			}
	
			if($include_schedules == 1) {
				$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Ad Injection schedule', 'starttime' => $now, 'stoptime' => $in84days, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'daystarttime' => '0000', 'daystoptime' => '0000', 'day_mon' => 'Y', 'day_tue' => 'Y', 'day_wed' => 'Y', 'day_thu' => 'Y', 'day_fri' => 'Y', 'day_sat' => 'Y', 'day_sun' => 'Y', 'autodelete' => 'N'));
				$schedule_id = $wpdb->insert_id;
	
				foreach($ads2schedule as $key => $ad_id) {
					$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $ad_id, 'group' => 0, 'user' => 0, 'schedule' => $schedule_id));
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