<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2019 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

/* Format advert */
function adrotateswitch_format_advert($source, $title, $bannercode, $imagetype, $image, $tracker, $desktop, $mobile, $tablet, $responsive, $type, $weight, $budget, $crate, $irate) {
	global $current_user, $userdata;

	$now = adrotate_now();

	$advertdata['title'] = '[Imported] '.$source.' '.$title;
	$advertdata['bannercode'] = esc_attr($bannercode);
	$advertdata['thetime'] = $now;
	$advertdata['updated'] = $now;
	$advertdata['author'] = $current_user->user_login;
	$advertdata['imagetype'] = $imagetype;
	$advertdata['image'] = esc_attr($image);
	$advertdata['paid'] = 'U';
	$advertdata['tracker'] = $tracker;
	$advertdata['desktop'] = $desktop;
	$advertdata['mobile'] = $mobile;
	$advertdata['tablet'] = $tablet;
	$advertdata['os_ios'] = 'Y';
	$advertdata['os_android'] = 'Y';
	$advertdata['os_other'] = 'Y';
	$advertdata['responsive'] = $responsive; // Deprecated
	$advertdata['type'] = $type;
	$advertdata['weight'] = $weight;
	$advertdata['budget'] = $budget;
	$advertdata['crate'] = $crate;
	$advertdata['irate'] = $irate;
	$advertdata['cities'] = serialize(array());
	$advertdata['countries'] = serialize(array());
	
	return $advertdata;
}

/* Format group */
function adrotateswitch_format_group($source, $title, $modus, $fallback, $cat, $cat_loc, $cat_par, $page, $page_loc, $page_par, $mobile, $geo, $wrapper_before, $wrapper_after, $align, $gridrows, $gridcolumns, $admargin, $admargin_bottom, $admargin_left, $admargin_right, $adwidth, $adheight, $adspeed) {
	$groupdata['name'] = '[Imported] '.$source.' '.$title;
	$groupdata['modus'] = $modus;
	$groupdata['fallback'] = $fallback;
	$groupdata['cat'] = $cat;
	$groupdata['cat_loc'] = $cat_loc;
	$groupdata['cat_par'] = $cat_par;
	$groupdata['page'] = $page;
	$groupdata['page_loc'] = $page_loc;
	$groupdata['page_par'] = $page_par;
	$groupdata['mobile'] = $mobile;
	$groupdata['geo'] = $geo;
	$groupdata['wrapper_before'] = $wrapper_before;
	$groupdata['wrapper_after'] = $wrapper_after;
	$groupdata['align'] = $align;
	$groupdata['gridrows'] = $gridrows;
	$groupdata['gridcolumns'] = $gridcolumns;
	$groupdata['admargin'] = $admargin;
	$groupdata['admargin_bottom'] = $admargin_bottom;
	$groupdata['admargin_left'] = $admargin_left;
	$groupdata['admargin_right'] = $admargin_right;
	$groupdata['adwidth'] = $adwidth;
	$groupdata['adheight'] = $adheight;
	$groupdata['adspeed'] = $adspeed;
	
	return $groupdata;
}

/* Check if AdRotate is active */
function adrotateswitch_adrotate_is_active() {
	if(function_exists('adrotate_dashboard')) {
		return true;
	} else {
		return false;
	}
}

function adrotateswitch_compatible_plugins() {
	return array(
		'ad-injection/ad-injection.php' => '1.2.0.19', 
		'adkingpro/adkingpro.php' => '2.0.1', 
		'advanced-advertising-system/advanced_advertising_system.php' => '1.3.1',
		'advertising-manager/advertising-manager.php' => '3.5.3',
		'wp-bannerize/main.php' => '4.0.2',
		'bannerman/bannerman.php' => '0.2.4',
		'max-banner-ads-pro/max-banner-ads-pro.php' => '2.1.3',
		'simple-ads-manager/simple-ads-manager.php' => '2.9.8.125',
		'useful-banner-manager/useful-banner-manager.php' => '1.6.1',
		'wp-pro-ad-system/wp-pro-ad-system.php' => '4.6.9',
		'wp125/wp125.php' => '1.5.4',
		'wp-ad-manager/ad-minister.php' => '0.7.5',
		'wp-advertize-it/bootstrap.php' => '1.2.1',
//		'advert/advert.php' => '1.0.5',
//		'easy-ads-manager/easy-ads-manager.php' => '1.0.1',
//		'easy-adsense-injection/easy-adsense-injection.php' => '1.0',
//		'max-adsense/adsense.php' => '1.0',
//		'random-banners/random-banners.php' => '1.0.0',
	);
}

function adrotateswitch_compatibility($plugin = '') {
	$compatible_plugins = adrotateswitch_compatible_plugins();
	if(file_exists(WP_PLUGIN_DIR.'/'.$plugin)) {	
		$installed_plugin = get_plugin_data(WP_PLUGIN_DIR.'/'.$plugin);
	} else { 
		return false;
	}

	if($installed_plugin['Version'] AND version_compare($installed_plugin['Version'], $compatible_plugins[$plugin], '==')) {
		// Compatible, safe to continue
		$status = '<strong>'._('Status:', 'adrotate-switch').'</strong> <span class="row_good">Compatible and tested!</span></em>';
	} else if($installed_plugin['Version'] AND version_compare($installed_plugin['Version'], $compatible_plugins[$plugin], '<')) {
		// Import script supports newer version, possibly incompatible, not safe to continue
		$status = '<strong>'._('Status:', 'adrotate-switch').'</strong> <span class="row_notice">Before continuing, update your plugin to v'.$compatible_plugins[$plugin].' first. Proceed at your own risk!</span></em>';
	} else if($installed_plugin['Version'] AND version_compare($installed_plugin['Version'], $compatible_plugins[$plugin], '>')) {
		// Import script outdated, not safe to continue
		$status = '<strong>'._('Status:', 'adrotate-switch').'</strong> <span class="row_caution">You have v'.$installed_plugin['Version'].', AdRotate Switch supports v'.$compatible_plugins[$plugin].'. Contact support for an updated import script or proceed at your own risk!</span>';
	} else {
		$status = '';
	}

	return $status;
}

/* Load Dashboard styles */
function adrotateswitch_dashboard_styles() {
	wp_enqueue_style('adrotateswitch-admin-stylesheet', plugins_url('dashboard.css', __FILE__));
}

function adrotateswitch_plugin_actions($links, $file) {
	if($file == 'adrotate-switch/adrotate-switch.php' AND strpos($_SERVER['SCRIPT_NAME'], '/network/') === false) {
		$link = '<a href="tools.php?page=adrotate-switch">'.__('Start Importing').'</a>';
		array_unshift($links, $link);
	}
	return $links;
}
?>