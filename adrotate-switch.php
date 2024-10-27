<?php
/*
Plugin Name: AdRotate Switch
Plugin URI: https://ajdg.solutions/product/adrotate-switch/
Author: Arnan de Gans
Author URI: https://www.arnan.me/
Description: Easily migrate your data from compatible advertising plugins to AdRotate Banner Manager or AdRotate Professional.
Text Domain: adrotate-switch
Version: 1.12
License: GPLv3
*/

/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2019 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

if(is_admin()) {
	/* Compatibility info */
	define("ADROTATE_PRO_FROM", '5.0');
	define("ADROTATE_PRO_TO", '5.7.3');
	define("ADROTATE_FREE_FROM", '5.0');
	define("ADROTATE_FREE_TO", '5.7.1');

	include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/adrotate-switch-functions.php');

	add_action('admin_menu', 'adrotateswitch_dashboard');
	add_action('admin_print_styles', 'adrotateswitch_dashboard_styles');

	add_filter('plugin_action_links', 'adrotateswitch_plugin_actions', 10, 2);

	/*--- Internal redirects ------------------------------------*/
	if(isset($_POST['adrotateswitch_import_mba'])) {
		include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/import/adrotate-max-bannerads.php');	
		add_action('init', 'adrotateswitch_import_mba');
	}
	
	if(isset($_POST['adrotateswitch_import_ubm'])) {
		include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/import/adrotate-useful-banner-manager.php');
		add_action('init', 'adrotateswitch_import_ubm');
	}
	
	if(isset($_POST['adrotateswitch_import_sam'])) {
		include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/import/adrotate-simple-ads-manager.php');
		add_action('init', 'adrotateswitch_import_sam');
	}
	
	if(isset($_POST['adrotateswitch_import_bannerize'])) {
		include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/import/adrotate-bannerize.php');
		add_action('init', 'adrotateswitch_import_bannerize');
	}
	
	if(isset($_POST['adrotateswitch_import_wp125'])) {
		include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/import/adrotate-wp125.php');
		add_action('init', 'adrotateswitch_import_wp125');
	}

	if(isset($_POST['adrotateswitch_import_wppas'])) {
		include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/import/adrotate-wp-pro-ad-system.php');
		add_action('init', 'adrotateswitch_import_wppas');
	}
	
	if(isset($_POST['adrotateswitch_import_adking'])) {
		include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/import/adrotate-adking.php');
		add_action('init', 'adrotateswitch_import_adking');
	}
	
	if(isset($_POST['adrotateswitch_import_aas'])) {
		include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/import/adrotate-advanced-advertising-system.php');
		add_action('init', 'adrotateswitch_import_aas');
	}
	
	if(isset($_POST['adrotateswitch_import_am'])) {
		include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/import/adrotate-advertising-manager.php');
		add_action('init', 'adrotateswitch_import_advertising_manager');
	}
	
	if(isset($_POST['adrotateswitch_import_bannerman'])) {
		include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/import/adrotate-bannerman.php');
		add_action('init', 'adrotateswitch_import_bannerman');
	}
	
	if(isset($_POST['adrotateswitch_import_wpadmanager'])) {
		include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/import/adrotate-wpadmanager.php');
		add_action('init', 'adrotateswitch_import_wpadmanager');
	}
	
	if(isset($_POST['adrotateswitch_import_adinjection'])) {
		include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/import/adrotate-adinjection.php');
		add_action('init', 'adrotateswitch_import_adinjection');
	}
	
	if(isset($_POST['adrotateswitch_import_wpadvertizeit'])) {
		include_once(WP_CONTENT_DIR.'/plugins/adrotate-switch/import/adrotate-wpadvertizeit.php');
		add_action('init', 'adrotateswitch_import_wpadvertizeit');
	}
}

/* Add dashboard */
function adrotateswitch_dashboard() {
	add_menu_page(__('AdRotate Switch', 'adrotate-switch'), __('AdRotate Switch', 'adrotate-switch'), 'manage_options', 'adrotate-switch', 'adrotateswitch_main', plugins_url('/images/icon-menu.png', __FILE__), '25.9');
}

/* Show dashboard */
function adrotateswitch_main() {
	$status = 0;
	if(isset($_GET['s'])) $status = esc_attr($_GET['s']);
	?>
	<div class="wrap">
		<h1><?php _e('AdRotate Switch', 'adrotate-switch'); ?></h1>

		<?php if($status == 1) { ?>
			<div class="updated" style="padding:12px;"><strong><?php _e('HOORAY! Your plugin data has been imported into AdRotate. You can manage your adverts from the', 'adrotate-switch'); ?> <a href="<?php echo admin_url('/admin.php?page=adrotate-ads'); ?>">AdRotate <?php _e('dashboard', 'adrotate-switch'); ?></a> <?php _e('now.', 'adrotate-switch'); ?></strong><br /><br /><strong><?php _e('Next steps:', 'adrotate-switch'); ?></strong><br />- <?php _e('If you have imported all your ads through AdRotate Switch you can uninstall or disable this plugin.', 'adrotate-switch'); ?><br />- <?php _e('Once you have verified all your ads and compatible data have been imported into AdRotate correctly you can remove or disable your previous advertising plugin.', 'adrotate-switch'); ?><br />- <?php _e('Do not forget that your previous plugin may have had shortcodes and placement details that may need to be cleaned up.', 'adrotate-switch'); ?></div>
		<?php } ?>

		<?php if($status == 2) { ?>
			<div class="error" style="padding:12px;"><?php _e('Something went wrong importing your adverts. No adverts have been created! - Try again or check your servers error_log file for details.', 'adrotate-switch'); ?><br /><?php _e('Contact AdRotate Support following the support link near the top of this page if the issue persists.', 'adrotate-switch'); ?></div>
		<?php } ?>

		<?php if($status == 3) { ?>
			<div class="error" style="padding:12px;"><?php _e('Something went wrong importing your groups/placements. No groups have been generated! - Try again or check your servers error_log file for details.', 'adrotate-switch'); ?><br /><?php _e('Contact AdRotate Support following the link at the bottom of this page if the issue persists.', 'adrotate-switch'); ?></div>
		<?php } ?>

		<?php if(!adrotateswitch_adrotate_is_active()) { ?>
			<div class="error" style="padding:12px;"><?php _e('AdRotate (Pro) is not active or installed! AdRotate (Pro) must be active for AdRotate Switch to work!', 'adrotate-switch'); ?></div>
			<?php define('ADROTATE_DISPLAY', 'Version unknown'); ?>
		<?php } ?>

		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">
		
				<div id="postbox-container-1" class="postbox-container" style="width:50%;">
					<div class="meta-box-sortables">

						<div class="ajdg-postbox">
							<h2 class="ajdg-postbox-title"><?php _e('Compatibility Information', 'adrotate-switch'); ?></h2>
							<div id="support" class="ajdg-postbox-content">
								<p><?php _e('You have', 'adrotate-switch'); ?> <strong>AdRotate <?php echo ADROTATE_DISPLAY; ?></strong> <?php _e('installed', 'adrotate-switch'); ?>.</p>
								<strong><?php _e('AdRotate Switch is tested with:', 'adrotate-switch'); ?></strong>
								<p>AdRotate Professional <?php _e('versions', 'adrotate-switch'); ?> <strong><?php echo ADROTATE_PRO_FROM; ?></strong> <?php _e('to', 'adrotate-switch'); ?>  <strong><?php echo ADROTATE_PRO_TO; ?></strong>.<br />
								AdRotate Banner Manager <?php _e('versions', 'adrotate-switch'); ?> <strong><?php echo ADROTATE_FREE_FROM; ?></strong> <?php _e('to', 'adrotate-switch'); ?>  <strong><?php echo ADROTATE_FREE_TO; ?></strong>.</p>
								<p><?php _e('Module outdated or import not working?', 'adrotate-switch'); ?> <a href="https://ajdg.solutions/forums/forum/wordpress-plugins/?pk_campaign=adrotateswitch&pk_keyword=dashboard" target="_blank"><?php _e('Let me know', 'adrotate-switch'); ?></a>!</p>
							</div>
						</div>

						<div class="ajdg-postbox">
							<h2 class="ajdg-postbox-title"><?php _e('AdRotate Switch', 'adrotate-switch'); ?></h2>
							<div id="services" class="ajdg-postbox-content">
								<p><strong><?php _e('Help with development', 'adrotate-switch'); ?></strong></p>
								<p><?php _e('Consider writing a review or make a donation if you like the plugin or if you find the plugin useful. Thanks for your support!', 'adrotate-switch'); ?></p>
								<center><a class="button-primary" href="https://ajdg.solutions/go/donate" target="_blank">Donate via Paypal</a> <a class="button" target="_blank" href="https://wordpress.org/support/plugin/adrotate-switch/reviews/?rate=5#new-post">Write review on WordPress.org</a></center></p>
			
								<p><strong><?php _e('Plugins and Services', 'adrotate-switch'); ?></strong></p>
								<table width="100%">
									<tr>
										<td width="33%">
											<div class="ajdg-sales-widget" style="display: inline-block; margin-right:2%;">
												<a href="https://ajdg.solutions/product/adrotate-html5-setup-service/?pk_campaign=adrotateswitch&pk_keyword=dashboard" target="_blank"><div class="header"><img src="<?php echo plugins_url("/images/offers/html5-service.jpg", __FILE__); ?>" alt="HTML5 Advert setup" width="228" height="120"></div></a>
												<a href="https://ajdg.solutions/product/adrotate-html5-setup-service/?pk_campaign=adrotateswitch&pk_keyword=dashboard" target="_blank"><div class="title"><?php _e('HTML5 Advert setup', 'adrotate-switch'); ?></div></a>
												<div class="sub_title"><?php _e('Professional service', 'adrotate-switch'); ?></div>
												<div class="cta"><a role="button" class="cta_button" href="https://ajdg.solutions/product/adrotate-html5-setup-service/?pk_campaign=adrotateswitch&pk_keyword=dashboard" target="_blank">Only &euro; 22,50 p/ad</a></div>
												<hr>
												<div class="description"><?php _e('Did you get a HTML5 advert and can’t get it to work in AdRotate Professional? I’ll install and configure it for you.', 'adrotate-switch'); ?></div>
											</div>							
										</td>
										<td width="33%">
											<div class="ajdg-sales-widget" style="display: inline-block; margin-right:2%;">
												<a href="https://ajdg.solutions/product/wordpress-maintenance-and-updates/?pk_campaign=adrotateswitch&pk_keyword=dashboard" target="_blank"><div class="header"><img src="<?php echo plugins_url("/images/offers/wordpress-maintenance.jpg", __FILE__); ?>" alt="WordPress Maintenance" width="228" height="120"></div></a>
												<a href="https://ajdg.solutions/product/wordpress-maintenance-and-updates/?pk_campaign=adrotateswitch&pk_keyword=dashboard" target="_blank"><div class="title"><?php _e('WP Maintenance', 'adrotate-switch'); ?></div></a>
												<div class="sub_title"><?php _e('Professional service', 'adrotate-switch'); ?></div>
												<div class="cta"><a role="button" class="cta_button" href="https://ajdg.solutions/product/wordpress-maintenance-and-updates/?pk_campaign=adrotateswitch&pk_keyword=dashboard" target="_blank">Starting at &euro; 22,50</a></div>
												<hr>								
												<div class="description"><?php _e('Get all the latest updates for WordPress and plugins. Maintenance, delete spam and clean up files.', 'adrotate-switch'); ?></div>
											</div>
										</td>
										<td>
											<div class="ajdg-sales-widget" style="display: inline-block;">
												<a href="https://ajdg.solutions/product/woocommerce-single-page-checkout/?pk_campaign=adrotateswitch&pk_keyword=dashboard" target="_blank"><div class="header"><img src="<?php echo plugins_url("/images/offers/single-page-checkout.jpg", __FILE__); ?>" alt="WooCommerce Single Page Checkout" width="228" height="120"></div></a>
												<a href="https://ajdg.solutions/product/woocommerce-single-page-checkout/?pk_campaign=adrotateswitch&pk_keyword=dashboard" target="_blank"><div class="title"><?php _e('Single Page Checkout', 'adrotate-switch'); ?></div></a>
												<div class="sub_title"><?php _e('WooCommerce Plugin', 'adrotate-switch'); ?></div>
												<div class="cta"><a role="button" class="cta_button" href="https://ajdg.solutions/product/woocommerce-single-page-checkout/?pk_campaign=adrotateswitch&pk_keyword=dashboard" target="_blank">Only &euro; 10,-</a></div>
												<hr>
												<div class="description"><?php _e('Merge your cart and checkout pages into one single page in seconds with no setup required at all.', 'adrotate-switch'); ?></div>
											</div>
										</td>
									</tr>
								</table>
							</div>
						</div>

					</div>
				</div>
		
				<div id="postbox-container-2" class="postbox-container" style="width:50%;">
					<div class="meta-box-sortables">
		
						<div class="ajdg-postbox">
							<h2 class="ajdg-postbox-title"><?php _e('News & Updates', 'adrotate-switch'); ?></h2>
							<div id="services" class="ajdg-postbox-content">
								<?php wp_widget_rss_output(array(
									'url' => 'http://ajdg.solutions/feed/', 
									'items' => 5, 
									'show_summary' => 1, 
									'show_author' => 0, 
									'show_date' => 1)
								); ?>
							</div>
						</div>

					</div>
				</div>

			</div>
			<div class="clear"></div>

			<?php 
			$plugin = adrotateswitch_compatibility('max-banner-ads-pro/max-banner-ads-pro.php');
			if($plugin) {
			?>
			<h2>&nbsp;&nbsp;Max Banner Ads PRO</h2>
			<div class="postbox-adrotate">
				<div class="inside">
					<?php echo $plugin; ?>
					<form method="post" action="admin.php?page=adrotate-switch">
						<?php wp_nonce_field('adrotateswitch_import_mba','adrotateswitch_nonce'); ?>
						<p><label for="adrotateswitch_import_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adverts" checked="1" disabled="1" /> <?php _e('Import banners into adverts (required)', 'adrotate-switch'); ?></label><br />
						<label for="adrotateswitch_import_groups">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_groups" value="1" /> <?php _e('Import zones into groups', 'adrotate-switch'); ?></label><br />
						<label for="adrotateswitch_import_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_schedules" value="1" /> <?php _e('Import expiry dates into schedules (Recommended)', 'adrotate-switch'); ?></label><br />
						<label for="adrotateswitch_import_stats">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_stats" value="1" /> <?php _e('Import clicks and impressions into a stats record', 'adrotate-switch'); ?></label></p>
						<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_mba" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>				
					</form>

					<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong>
					<br />- <span class="row_good"><?php _e('Zones are migrated to groups with default settings.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_good"><?php _e('AdCode may be generated on the fly depending on your settings.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_notice"><?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></span>
					</p>
				</div>
			</div>
			<?php 
				unset($plugin);
			}
			?>

			<?php 
			$plugin = adrotateswitch_compatibility('adkingpro/adkingpro.php');
			if($plugin) {
			?>
			<h2>&nbsp;&nbsp;Ad King PRO</h2>
			<div class="postbox-adrotate">
				<div class="inside">
					<?php echo $plugin; ?>
					<form method="post" action="admin.php?page=adrotate-switch">
						<?php wp_nonce_field('adrotateswitch_import_adking','adrotateswitch_nonce'); ?>
						<p><label for="adrotateswitch_import_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adverts" checked="1" disabled="1" /> <?php _e('Import Image, Text or AdSense banners into adverts (required)', 'adrotate-switch'); ?></label><br />
						<label for="adrotateswitch_import_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_schedules" value="1" /> <?php _e('Import expiry dates into schedules (Recommended)', 'adrotate-switch'); ?></label></p>
						<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_adking" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>				
					</form>

					
					<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong> 
					<br />- <span class="row_good"><?php _e('AdCode may be generated on the fly depending on your settings.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_notice"><?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_notice"><?php _e('HTML5 code is generated and may need tweaks.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_caution"><?php _e('Flash banners are not imported.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_caution"><?php _e('Banner Zones are not compatible.', 'adrotate-switch'); ?></span>
					</p>
				</div>
			</div>
			<?php 
				unset($plugin);
			}
			?>

			<?php 
			$plugin = adrotateswitch_compatibility('advanced-advertising-system/advanced_advertising_system.php');
			if($plugin) {
			?>
			<h2>&nbsp;&nbsp;Advanced Advertising System</h2>
			<div class="postbox-adrotate">
				<div class="inside">
					<?php echo $plugin; ?>
					<form method="post" action="admin.php?page=adrotate-switch">
						<?php wp_nonce_field('adrotateswitch_import_aas','adrotateswitch_nonce'); ?>
						<p><label for="adrotateswitch_import_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adverts" checked="1" disabled="1" /> <?php _e('Import Image, Text or HTML banners into adverts (required)', 'adrotate-switch'); ?></label><br />
						<label for="adrotateswitch_import_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_schedules" value="1" /> <?php _e('Create schedules (Recommended)', 'adrotate-switch'); ?></label></p>
						<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_aas" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>				
					</form>

					
					<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong> 
					<br />- <span class="row_good"><?php _e('AdCode may be generated on the fly depending on your settings.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_notice"><?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_caution"><?php _e('Banner Zones are not compatible.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_caution"><?php _e('Advertisers are not compatible.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_caution"><?php _e('Campaigns are not compatible.', 'adrotate-switch'); ?></span>
					</p>
				</div>
			</div>
			<?php 
				unset($plugin);
			}
			?>

			<?php 
			$plugin = adrotateswitch_compatibility('advertising-manager/advertising-manager.php');
			if($plugin) {
			?>
			<h2>&nbsp;&nbsp;Advertising Manager</h2>
			<div class="postbox-adrotate">
				<div class="inside">
					<?php echo $plugin; ?>
					<form method="post" action="admin.php?page=adrotate-switch">
						<?php wp_nonce_field('adrotateswitch_import_am','adrotateswitch_nonce'); ?>
						<p><label for="adrotateswitch_import_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adverts" checked="1" disabled="1" /> <?php _e('Import banners into adverts (required)', 'adrotate-switch'); ?></label><br />
						<label for="adrotateswitch_import_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_schedules" value="1" /> <?php _e('Assign a default schedule (Recommended)', 'adrotate-switch'); ?></label></p>
						<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_am" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>
					</form>

					<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong>
					<br />- <span class="row_good"><?php _e('AdCode will be imported as-is.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_good"><?php _e('HTML Before and After code will be wrapped around AdCode.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_notice"><?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_caution"><?php _e('Placement data not compatible. Manual setup required.', 'adrotate-switch'); ?></span>
				</div>
			</div>
			<?php 
				unset($plugin);
			}
			?>

			<?php 
			$plugin = adrotateswitch_compatibility('wp-pro-ad-system/wp-pro-ad-system.php');
			if($plugin) {
			?>
			<h2>&nbsp;&nbsp;WP Pro Ad System</h2>
			<div class="postbox-adrotate">
				<div class="inside">
					<?php echo $plugin; ?>
					<form method="post" action="admin.php?page=adrotate-switch">
						<?php wp_nonce_field('adrotateswitch_import_wppas','adrotateswitch_nonce'); ?>					
						<p><label for="adrotateswitch_import_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adverts" checked="1" disabled="1" /> <?php _e('Import banners into adverts (required)', 'adrotate-switch'); ?></label><br />
						<label for="adrotateswitch_import_groups">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_groups" value="1" /> <?php _e('Import zones into groups', 'adrotate-switch'); ?></label><br />
						<label for="adrotateswitch_import_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_schedules" value="1" /> <?php _e('Assign a default schedule (Recommended)', 'adrotate-switch'); ?></label></p>
						<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_wppas" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>				
					</form>

					<p><strong><?php _e('Caution:', 'adrotate-switch'); ?></strong>
					<br />- <span class="row_notice"><?php _e('AdRotate makes a best effort but some adverts/groups may not work without some tweaks!', 'adrotate-switch'); ?></span></p>
					
					<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong>
					<br />- <span class="row_good"><?php _e('Adzones imported into groups.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_good"><?php _e('AdCode may be generated on the fly depending on your settings.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_good"><?php _e('Tablet/Phone advert variations are imported into seperate adverts.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_good"><?php _e('Tablet/Phone adverts will be set up for mobile devices.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_notice"><?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></span>
					</p>
				</div>
			</div>
			<?php 
				unset($plugin);
			}
			?>

			<?php 
			$plugin = adrotateswitch_compatibility('wp-advertize-it/bootstrap.php');
			if($plugin) {
			?>
			<h2>&nbsp;&nbsp;WP Advertize It</h2>
			<div class="postbox-adrotate">
				<div class="inside">
					<?php echo $plugin; ?>
					<form method="post" action="admin.php?page=adrotate-switch">
						<?php wp_nonce_field('adrotateswitch_import_wpadvertizeit','adrotateswitch_nonce'); ?>					
						<p><label for="adrotateswitch_import_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adverts" checked="1" disabled="1" /> <?php _e('Import banners into adverts (required)', 'adrotate-switch'); ?></label><br />
						<label for="adrotateswitch_import_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_schedules" value="1" /> <?php _e('Assign a default schedule (Recommended)', 'adrotate-switch'); ?></label></p>
						<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_wpadvertizeit" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>
					</form>

					<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong>
					<br />- <span class="row_good"><?php _e('Adverts will be imported as-is.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_notice"><?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_caution"><?php _e('Placement settings not compatible for groups.', 'adrotate-switch'); ?></span>
					</p>
				</div>
			</div>
			<?php 
				unset($plugin);
			}
			?>

			<?php 
			$plugin = adrotateswitch_compatibility('simple-ads-manager/simple-ads-manager.php');
			if($plugin) {
			?>
			<h2>&nbsp;&nbsp;Simple Ads Manager</h2>
			<div class="postbox-adrotate">
				<div class="inside">
					<?php echo $plugin; ?>
					<form method="post" action="admin.php?page=adrotate-switch">
						<?php wp_nonce_field('adrotateswitch_import_sam','adrotateswitch_nonce'); ?>
						<p><label for="adrotateswitch_import_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adverts" checked="1" disabled="1" /> <?php _e('Import banners into adverts (required)', 'adrotate-switch'); ?></label><br />
						<label for="adrotateswitch_import_groups">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_groups" value="1" /> <?php _e('Import Places and Blocks into groups', 'adrotate-switch'); ?></label></p>
						<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_sam" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>
					</form>

					<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong>
					<br />- <span class="row_good"><?php _e('AdCode may be generated on the fly depending on your settings.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_good"><?php _e('Each advert is assigned a default schedule.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_notice"><?php _e('Groups are converted where possible and compatible settings migrated.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_notice"><?php _e('Ads Blocks are converted to groups in Block mode, no ads linked.', 'adrotate-switch'); ?></span>
					</p>
				</div>
			</div>
			<?php 
				unset($plugin);
			}
			?>

			<?php 
			$plugin = adrotateswitch_compatibility('wp-bannerize/main.php');
			if($plugin) {
			?>
			<h2>&nbsp;&nbsp;WP Bannerize</h2>
			<div class="postbox-adrotate">
				<div class="inside">
					<?php echo $plugin; ?>
					<form method="post" action="admin.php?page=adrotate-switch">
						<?php wp_nonce_field('adrotateswitch_import_bannerize','adrotateswitch_nonce'); ?>
						<p><label for="adrotateswitch_import_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adverts" checked="1" disabled="1" /> <?php _e('Import banners into adverts (required)', 'adrotate-switch'); ?></label><br />
						<label for="adrotateswitch_import_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_schedules" value="1" /> <?php _e('Import dates into schedules (Recommended)', 'adrotate-switch'); ?></label><br />
						<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_bannerize" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>
					</form>

					<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong>
					<br />- <span class="row_good"><?php _e('AdCode may be generated on the fly depending on your settings.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_notice"><?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_caution"><?php _e('Placement data not compatible. Manual setup required.', 'adrotate-switch'); ?></span>
					</p>
				</div>
			</div>
			<?php 
				unset($plugin);
			}
			?>

			<?php 
			$plugin = adrotateswitch_compatibility('ad-injection/ad-injection.php');
			if($plugin) {
			?>
			<h2>&nbsp;&nbsp;Ad Injection</h2>
			<div class="postbox-adrotate">
				<div class="inside">
					<?php echo $plugin; ?>
					<form method="post" action="admin.php?page=adrotate-switch">
						<?php wp_nonce_field('adrotateswitch_import_adinjection','adrotateswitch_nonce'); ?>
						<p><label for="adrotateswitch_import_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adverts" checked="1" disabled="1" /> <?php _e('Import banners into adverts (required)', 'adrotate-switch'); ?></label><br />
						<label for="adrotateswitch_import_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_schedules" value="1" /> <?php _e('Assign a default schedule (Recommended)', 'adrotate-switch'); ?></label></p>
						<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_adinjection" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>
					</form>

					<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong>
					<br />- <span class="row_good"><?php _e('All configured ad codes migrated.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_good"><?php _e('AdCode may be generated on the fly depending on your settings.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_notice"><?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_caution"><?php _e('Placement data not compatible. Manual setup required.', 'adrotate-switch'); ?></span>
					</p>
				</div>
			</div>
			<?php 
				unset($plugin);
			}
			?>

			<?php 
			$plugin = adrotateswitch_compatibility('wp125/wp125.php');
			if($plugin) {
			?>
			<h2>&nbsp;&nbsp;wp125</h2>
			<div class="postbox-adrotate">
				<div class="inside">
					<?php echo $plugin; ?>
					<form method="post" action="admin.php?page=adrotate-switch">
						<?php wp_nonce_field('adrotateswitch_import_wp125','adrotateswitch_nonce'); ?>
						<p><label for="adrotateswitch_import_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adverts" checked="1" disabled="1" /> <?php _e('Import banners into adverts (required)', 'adrotate-switch'); ?></label><br />
						<label for="adrotateswitch_import_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_schedules" value="1" /> <?php _e('Import expiry dates into schedules (Recommended)', 'adrotate-switch'); ?></label><br />
						<label for="adrotateswitch_import_stats">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_stats" value="1" /> <?php _e('Import clicks into a stats record', 'adrotate-switch'); ?></label></p>
						<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_wp125" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>
					</form>

					<p><strong><?php _e('Caution:', 'adrotate-switch'); ?></strong>
					<br />- <span class="row_caution"><?php _e('This plugin is tested to have errors on modern WordPress 4.2+ which may affect your import.', 'adrotate-switch'); ?></span></p>

					<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong>
					<br />- <span class="row_good"><?php _e('AdCode may be generated on the fly depending on your settings.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_notice"><?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_caution"><?php _e('Slots not migrated. Manual setup required.', 'adrotate-switch'); ?></span>
					</p>
				</div>
			</div>
			<?php 
				unset($plugin);
			}
			?>

			<?php 
			$plugin = adrotateswitch_compatibility('bannerman/bannerman.php');
			if($plugin) {
			?>
			<h2>&nbsp;&nbsp;BannerMan</h2>
			<div class="postbox-adrotate">
				<div class="inside">
					<?php echo $plugin; ?>
					<form method="post" action="admin.php?page=adrotate-switch">
						<?php wp_nonce_field('adrotateswitch_import_bannerman','adrotateswitch_nonce'); ?>
						<p><label for="adrotateswitch_import_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adverts" checked="1" disabled="1" /> <?php _e('Import banners into adverts (required)', 'adrotate-switch'); ?></label><br />
						<label for="adrotateswitch_import_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_schedules" value="1" /> <?php _e('Assign a default schedule (Recommended)', 'adrotate-switch'); ?></label></p>
						<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_bannerman" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>
					</form>

					<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong>
					<br />- <span class="row_good"><?php _e('A group with relevant settings will be generated to accomodate all ads.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_good"><?php _e('AdCode will be imported as-is.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_notice"><?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></span>
					</p>
				</div>
			</div>
			<?php 
				unset($plugin);
			}
			?>

			<?php 
			$plugin = adrotateswitch_compatibility('wp-ad-manager/ad-minister.php');
			if($plugin) {
			?>
			<h2>&nbsp;&nbsp;Ad-Minister / WP-Ad-Manager</h2>
			<div class="postbox-adrotate">
				<div class="inside">
					<?php echo $plugin; ?>
					<form method="post" action="admin.php?page=adrotate-switch">
						<?php wp_nonce_field('adrotateswitch_import_wpadmanager','adrotateswitch_nonce'); ?>
						<p><label for="adrotateswitch_import_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adverts" checked="1" disabled="1" /> <?php _e('Import contents into adverts (required)', 'adrotate-switch'); ?></label><br />
						<label for="adrotateswitch_import_groups">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_groups" value="1" /> <?php _e('Import positions into groups', 'adrotate-switch'); ?></label><br />
						<label for="adrotateswitch_import_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_schedules" value="1" /> <?php _e('Generate schedules (Recommended)', 'adrotate-switch'); ?></label></p>
						<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_wpadmanager" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>
					</form>

					<p><strong><?php _e('Caution:', 'adrotate-switch'); ?></strong>
					<br />- <span class="row_caution"><?php _e('This plugin is tested to have errors on modern WordPress 3.5+ which may affect your import.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_notice"><?php _e('AdRotate makes a best effort but some adverts/groups may not work without some tweaks!', 'adrotate-switch'); ?></span></p>

					<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong>
					<br />- <span class="row_good"><?php _e('Positions converted to basic groups.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_good"><?php _e('AdCode will be stripped of %tracker% tag.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_notice"><?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></span>
					</p>
				</div>
			</div>
			<?php 
				unset($plugin);
			}
			?>

			<?php 
			$plugin = adrotateswitch_compatibility('useful-banner-manager/useful-banner-manager.php');
			if($plugin) {
			?>
			<h2>&nbsp;&nbsp;Useful Banner Manager</h2>
			<div class="postbox-adrotate">
				<div class="inside">
					<?php echo $plugin; ?>
					<form method="post" action="admin.php?page=adrotate-switch">
						<?php wp_nonce_field('adrotateswitch_import_ubm','adrotateswitch_nonce'); ?>
						<p><label for="adrotateswitch_import_adverts">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_adverts" checked="1" disabled="1" /> <?php _e('Import banners into adverts (required)', 'adrotate-switch'); ?></label><br />
						<label for="adrotateswitch_import_schedules">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="adrotateswitch_import_schedules" value="1" /> <?php _e('Import expiry dates into schedules (Recommended)', 'adrotate-switch'); ?></label></p>
						<p><input type="submit" id="post-role-submit" name="adrotateswitch_import_ubm" value="<?php _e('Import', 'adrotate-switch'); ?>" class="button-primary" />&nbsp;&nbsp;&nbsp;<em><?php _e('Click only once!', 'adrotate-switch'); ?></em></p>
					</form>

					<p><strong><?php _e('Notes:', 'adrotate-switch'); ?></strong> 
					<br />- <span class="row_good"><?php _e('AdCode may be generated on the fly depending on your settings.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_good"><?php _e('Most settings are converted into HTML for use in AdCode.', 'adrotate-switch'); ?></span>
					<br />- <span class="row_notice"><?php _e('If schedules are skipped, imported adverts may appear expired/faulty until you give them a schedule.', 'adrotate-switch'); ?></span>
					</p>
				</div>
			</div>
			<?php 
				unset($plugin);
			}
			?>
		
			<div class="clear"></div>

			<table class="widefat" style="margin-top: .5em">
			
			<thead>
			<tr valign="top">
				<th colspan="2"><strong><?php _e("Help AdRotate Grow", "adrotate-switch"); ?></strong></th>
				<th width="45%"><strong><?php _e("Do you have AdRotate Professional yet?", "adrotate-switch"); ?></strong></th>
			</tr>
			</thead>
			
			<tbody>
			<tr>
			<td><center><a href="https://ajdg.solutions/product-category/adrotate-pro/?pk_campaign=adrotateswitch&pk_keyword=dashboard" title="AdRotate plugin for WordPress"><img src="<?php echo plugins_url('/images/logo-60x60.png', __FILE__); ?>" alt="AdRotate Logo" width="60" height="60" /></a></center></td>
			<td><?php _e("Many users only think to review a plugin when something goes wrong while thousands of people happily use it.", "adrotate-switch"); ?> <strong><?php _e("If you find AdRotate Switch useful please leave your", "adrotate-switch"); ?> <a href="https://wordpress.org/support/view/plugin-reviews/adrotate-switch?rate=5#postform" target="_blank"><?php _e("rating", "adrotate-switch"); ?></a> <?php _e('and', 'adrotate-switch'); ?> <a href="https://wordpress.org/support/view/plugin-reviews/adrotate-switch" target="_blank"><?php _e('review','adrotate-switch'); ?></a> <?php _e("on WordPress.org to help AdRotate grow in a positive way", "adrotate-switch"); ?>!</strong></td>
			<td><a href="https://ajdg.solutions/cart/?add-to-cart=1124&pk_campaign=adrotateswitch&pk_keyword=dashboard" title="Get AdRotate Professional for WordPress"><img src="<?php echo plugins_url('/images/adrotate-product.png', __FILE__); ?>" alt="AdRotate Professional for WordPress" width="70" height="70" align="left" /></a><?php _e("If you haven't upgraded to AdRotate Professional yet. Do so now. You'll get more advanced features such as Geo Targeting, scheduling, Mobile Adverts and much more...", "adrotate-switch"); ?> <strong>Take a look on the <a href="https://ajdg.solutions/product/adrotate-pro-single/?pk_campaign=adrotateswitch&pk_keyword=dashboard" title="Get AdRotate Professional for WordPress">AdRotate Professional page &raquo;</a></strong></td>
			
			</tr>
			</tbody>
			
			</table>

			<center><small><?php _e('AdRotate<sup>&reg;</sup> is a registered trademark.', 'adrotate-switch'); ?></small></center>
		</div>

	</div>
	<?php
}
?>