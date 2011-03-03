<?php
/*
Plugin Name: Gravity Forms Directory & Addons
Plugin URI: http://www.seodenver.com/gravity-forms-addons/
Description: Add directory functionality and improve usability for the great <a href="http://bit.ly/dvF8BI" rel="nofollow">Gravity Forms</a> plugin.
Author: Katz Web Services, Inc.
Version: 2.4.4
Author URI: http://www.katzwebservices.com

Copyright 2011 Katz Web Services, Inc.  (email: info@katzwebservices.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

// Get Gravity Forms over here!
@include_once(WP_PLUGIN_DIR . "/gravityforms/gravityforms.php");
@include_once(WP_PLUGIN_DIR . "/gravityforms/forms_model.php");
@include_once(WP_PLUGIN_DIR . "/gravityforms/common.php"); // 1.3
@include_once(WP_PLUGIN_DIR . "/gravityforms/form_display.php"); // 1.3

// If Gravity Forms is installed and exists
if(class_exists('RGForms') && class_exists('RGFormsModel')) {
	
	$Forms = new RGForms();  $RG = new RGFormsModel(); 
	if(class_exists('GFCommon')) { $Common = new GFCommon(); } else { $Common = false; }
	if(class_exists('GFFormDisplay')) { $FormDisplay = new GFFormDisplay(); } else { $FormDisplay = false; }
	
	//creates the subnav left menu
   	add_filter("gform_addon_navigation", 'kws_gf_create_menu');
	
	function kws_gf_create_menu() {
		// Adding submenu if user has access
        $permission = kws_gf_has_access("gravityforms_addons");

        if(!empty($permission)) {
            $menus[] = array("name" => "gf_addons", "label" => __("Addons", "gravity-forms-addons"), "callback" =>  "kws_gf_settings_page", "permission" => $permission);
		}
		
        return $menus;
	}
	
	function kws_gf_get_settings() {
		$settings = get_option("gf_addons_settings");
        if(!$settings) {
        	$settings = array(
        		"directory" => true,
        		"referrer" => false,
        		"widget" => true,
        		"modify_admin" => true
        	);
        }
        return $settings;
	}
	
	function kws_gf_settings_page(){

		if($_POST["gf_addons_submit"]){
            check_admin_referer("update", "gf_addons_update");
            $settings = array(
            	"directory" => isset($_POST["gf_addons_directory"]),
            	"referrer" => isset($_POST["gf_addons_referrer"]),
            	"widget" => isset($_POST["gf_addons_widget"]),
            	"modify_admin" => isset($_POST["gf_addons_modify_admin"]),
            );
            update_option("gf_addons_settings", $settings);
        }
        
        $settings = kws_gf_get_settings();
        
		?>
        <form method="post" action="">
            <?php wp_nonce_field("update", "gf_addons_update") ?>
            <h3><?php _e("Gravity Forms Addons Settings", "gravity-forms-addons") ?></h3>
            <p style="text-align: left;">
                <?php _e('', "gravity-forms-addons") ?>
            </p>

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="gf_addons_directory"><?php _e("Gravity Forms Directory", "gravity-forms-addons"); ?></label> </th>
                    <td>
                        <label for="gf_addons_directory" class="howto"><input type="checkbox" id="gf_addons_directory" name="gf_addons_directory" <?php checked($settings["directory"]); ?> /> Turn Gravity Forms into a directory plugin with this awesome functionality.</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="gf_addons_referrer"><?php _e("Add Referrer Data to Emails", "gravity-forms-addons"); ?></label> </th>
                    <td>
                        <label for="gf_addons_referrer" class="howto"><input type="checkbox" id="gf_addons_referrer" name="gf_addons_referrer" <?php checked($settings["referrer"]); ?> /> Adds referrer data to entries, including the path the user took to get to the form before submitting.</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="gf_addons_widget"><?php _e("Load Addons Widget", "gravity-forms-addons"); ?></label> </th>
                    <td>
                        <label for="gf_addons_widget" class="howto"><input type="checkbox" id="gf_addons_widget" name="gf_addons_widget" <?php checked($settings["widget"]); ?> /> Load the <a href="http://yoast.com/gravity-forms-widget-extras/" rel="nofollow">Gravity Forms Widget by Yoast</a></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="gf_addons_modify_admin"><?php _e("Modify Gravity Forms Admin", "gravity-forms-addons"); ?></label> </th>
                    <td>
                        <label for="gf_addons_modify_admin" class="howto"><input type="checkbox" id="gf_addons_modify_admin" name="gf_addons_modify_admin" <?php checked($settings["modify_admin"]); ?> /> Makes possible direct editing of entries from <a href="<?php echo admin_url('admin.php?page=gf_entries');?>">Entries list view</a>, option to expand all Edit Form fields. </label>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" ><input type="submit" name="gf_addons_submit" class="button-primary" value="<?php _e("Save Settings", "gravity-forms-addons") ?>" /></td>
                </tr>
            </table>
        </form>
        <?php
    }
	
	function kws_gf_has_access($required_permission = ''){
        $has_members_plugin = function_exists('members_get_capabilities');
        $has_access = $has_members_plugin ? current_user_can($required_permission) : current_user_can("level_7");
        if($has_access)
            return $has_members_plugin ? $required_permission : "level_7";
        else
            return false;
    }
	

	add_action('plugins_loaded', 'kws_gf_load_functionality');
	
	function kws_gf_load_functionality() {
		$settings = kws_gf_get_settings();
		extract($settings);
		
		
		// Add menu in GF Settings
		if(is_admin() && kws_gf_has_access("gravityforms_addons")){
			global $Forms;
			$Forms->add_settings_page("Addons", "kws_gf_settings_page");
    	}
		
		if($directory) {
			// Load up the directory functionality
			@include_once('directory.php');
			
			register_activation_hook( __FILE__, 'kws_gf_flush_rules' );
			register_deactivation_hook( __FILE__, 'kws_gf_flush_rules' );
			
			function kws_gf_flush_rules() {
				global $wp_rewrite;
				$wp_rewrite->flush_rules();
				return;
			}
			
			add_action('init', 'kws_gf_add_rewrite');
			function kws_gf_add_rewrite() {
				global $wp_rewrite;
				if(!$wp_rewrite->using_permalinks()) { return; }
				$wp_rewrite->add_permastruct('entry', '(.+)/entry/%entry%', true);
				$wp_rewrite->add_endpoint('entry',EP_PERMALINK);
				$wp_rewrite->flush_rules();
			}
		}
		
		if($widget) {
			// Load Joost's widget
			@include_once('gravity-forms-widget.php');	
		}
		
		if($referrer) {
			// Load Joost's referrer tracker
			@include_once('gravity-forms-referrer.php');	
		}
		
		if($modify_admin) {
			add_action('admin_head', 'kws_gf_head',1);
		}
	}

	
	function kws_gf_css() {
		if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'gf_edit_forms' && isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
			$style = '<style type="text/css">
				.gforms_edit_form_expanded ul.menu li.add_field_button_container ul,
				.gforms_edit_form_expanded ul.menu li.add_field_button_container ul ol { display:block!important; }
			</style>';
			$style= apply_filters('kws_gf_display_all_fields', $style);
			echo $style;
		}
	}
		
	function kws_gf_js() {
		global $Common;
		if(is_admin()) {
			echo '<script src="'.$Common->get_base_url().'/js/jquery.simplemodal-1.3.min.js"></script>'; // Added for the new IDs popup
		}
		if(isset($_REQUEST['page']) && ($_REQUEST['page'] == 'gf_edit_forms' || $_REQUEST['page'] == 'gf_entries')) {
			echo '<script type="text/javascript">
			jQuery(document).ready(function($) {';
				kws_gf_add_edit_js(isset($_REQUEST['id']));
			echo '});
			</script>';
		}
	}
		
	function kws_gf_show_field_ids($form) {
		if(isset($_REQUEST['show_field_ids'])) {
		echo <<<EOD
		<style type="text/css">
			#input_ids th, #input_ids td { border-bottom:1px solid #999; padding:.25em 15px; }
			#input_ids th { border-bottom-color: #333; font-size:.9em; background-color: #464646; color:white; padding:.5em 15px; font-weight:bold;  } 
			#input_ids { background:#ccc; margin:0 auto; font-size:1.2em; line-height:1.4; width:100%; border-collapse:collapse;  }
			#input_ids strong { font-weight:bold; }
			#preview_hdr { display:none;}
			#input_ids caption { color:white!important;}
		</style>
EOD;
		
		if(!empty($form)) { echo '<table id="input_ids"><caption id="input_id_caption">Fields for <strong>Form ID '.$form['id'].'</strong></caption><thead><tr><th>Field Name</th><th>Field ID</th></thead><tbody>'; }
		foreach($form['fields'] as $field) {
			// If there are multiple inputs for a field; ie: address has street, city, zip, country, etc.
			if(is_array($field['inputs'])) {
				foreach($field['inputs'] as $input) {
					echo "<tr><td width='50%'><strong>{$input['label']}</strong></td><td>{$input['id']}</td></tr>";
				}
			}
			// Otherwise, it's just the one input.
			else {
				echo "<tr><td width='50%'><strong>{$field['label']}</strong></td><td>{$field['id']}</td></tr>";
			}
		}
		if(!empty($form)) { echo '</tbody></table><div style="clear:both;"></div></body></html>'; exit(); }
		} else {
			return $form;
		}
	}
	add_filter('gform_pre_render','kws_gf_show_field_ids');
	
	function kws_gf_add_edit_js($edit_forms = false) {
			if($edit_forms) {
				?>
				$('div.gforms_edit_form #add_fields #floatMenu').prepend('<div class="alignright" style="margin:1.17em 0!important;"><label for="expandAllMenus"><input type="checkbox" id="expandAllMenus" value="1" /> Expand All Menus</label></div>');
				$('input#expandAllMenus').live('change click', function() {
					if($(this).is(':checked')) {
						$('div.gforms_edit_form').addClass('gforms_edit_form_expanded');
						$('ul.menu li .button-title-link').unbind().die(); // .unbind() is for the initial .click()... .die() is for the live() below
					} else {
						$('div.gforms_edit_form').removeClass('gforms_edit_form_expanded');
						// from .../gravityforms/js/menu.js
						jQuery('ul.menu li .button-title-link').live('click', 
						        function() {
						            var checkElement = jQuery(this).next();
						            var parent = this.parentNode.parentNode.id;
						
						            if(jQuery('#' + parent).hasClass('noaccordion')) {
						                jQuery(this).next().slideToggle('normal');
						                return false;
						            }
						            if((checkElement.is('ul')) && (checkElement.is(':visible'))) {
						                if(jQuery('#' + parent).hasClass('collapsible')) {
						                    jQuery('#' + parent + ' ul:visible').slideUp('normal');
						                }
						                return false;
						            }
						            if((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
						                jQuery('#' + parent + ' ul:visible').slideUp('normal');
						                checkElement.slideDown('normal');
						                return false;
						            }
						        }
						    );
					}
				});
				
				<?php
				return;
			}
			
			if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'gf_entries') {
				?>
				$(".row-actions span.edit:contains('Delete')").each(function() { 
					var editLink = $(this).parents('tr').find('.column-title a').attr('href');
					editLink = editLink + '&screen_mode=edit';
					//alert();
					$(this).after('<span class="edit">| <a title="<?php _e("Edit this entry", "gravityforms"); ?>" href="'+editLink+'"><?php _e("Edit", "gravityforms"); ?></a></span>');
				});
				<?php 
			}
			
			else if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'gf_edit_forms') {
				?>
				$(".row-actions span.edit:contains('Delete')").each(function() { 
					var formID = $(this).parents('tr').find('.column-id').text();;
					$(this).after('<span class="edit">| <a title="<?php _e("View form field IDs", "gravityforms"); ?>" href="<?php  echo WP_PLUGIN_URL . "/" . basename(dirname(__FILE__)) . "/field-ids.php"; ?>?id='+formID+'&amp;show_field_ids=true" class="form_ids"><?php _e("IDs", "gravityforms"); ?></a></span>');
				});
					var h = $('#gravityformspreviewidsiframe').css('height');
					
					$("a.form_ids").live('click', function(e) {
						e.preventDefault();
						var src = $(this).attr('href');
						$.modal('<iframe src="' + src + '" width="" height="" style="border:0;">', {
//							closeHTML:"<a href='#'>Close</a>",
							minHeight:400,
							minWidth: 600,
							containerCss:{
								borderColor: 'transparent',
								borderWidth: 0,
								padding:10,
								escClose: true,
								minWidth:500,
								maxWidth:800,
								minHeight:500,
							},
							overlayClose:true,
							onShow: function(dlg) {
								var iframeHeight = $('iframe', $(dlg.container)).height();
								var containerHeight = $(dlg.container).height();
								var iframeWidth = $('iframe', $(dlg.container)).width();
								var containerWidth = $(dlg.container).width();
								
								if(containerHeight < iframeHeight) { $(dlg.container).height(iframeHeight); }
								else { $('iframe', $(dlg.container)).height(containerHeight); }
								
								if(containerWidth < iframeWidth) { $(dlg.container).width(iframeWidth); }
								else { $('iframe', $(dlg.container)).width(containerWidth); }
							}
						});
			         });

				<?php 
			}	
	}
	// Allows for edit links to work with a link instead of a form (GET instead of POST)
	if(isset($_GET["screen_mode"])) { $_POST["screen_mode"] = $_GET["screen_mode"]; }

	function kws_gf_head() {
		kws_gf_css();
		kws_gf_js();
	}
	
	function gf_field_value($leadid, $fieldid) {
		echo get_gf_field_value($leadid, $fieldid);
	}
	
	
	// To retrieve textarea inputs from a lead 
	// Example: get_gf_field_value_long(22, '14');
	function get_gf_field_value_long($leadid, $fieldid) {
		global $Forms, $RG, $Common;
		return $RG->get_field_value_long($leadid, $fieldid);
	}
	
	// To retrieve textarea inputs from a lead 
	// Example: get_gf_field_value_long(22, '14');
	function get_gf_field_value($leadid, $fieldid) {
		global $Forms, $RG, $Common;
		$lead = $RG->get_lead($leadid);
#		echo '<pre>'.print_r($lead,true).'</pre>'.floatval($fieldid);
		$fieldid = floatval($fieldid);
		if(is_numeric($fieldid)) {
			$result = $lead["$fieldid"];
		}
		
		$max_length = GFORMS_MAX_FIELD_LENGTH;
		
		if(strlen($result) >= ($max_length - 50)) {
			$result = get_gf_field_value_long($lead["id"], $fieldid);
        }
        $result = trim($result);
        
        if(!empty($result)) { return $result; }
		return false;
	}
	
	function gf_field_value_long($leadid, $fieldid) {
		echo get_gf_field_value_long($leadid, $fieldid);
	}
	
	
	// Gives you the label for a form input (such as First Name). Enter in the form and the field ID to access the label.
	// Example: echo get_gf_field_label(1,1.3);
	// Gives you the label for a form input (such as First Name). Enter in the form and the field ID to access the label.
	// Example: echo get_gf_field_label(1,1.3);
	function get_gf_field_label($form_id, $field_id) {
		global $Forms,$RG,$Common;
		$form = $RG->get_form_meta($form_id);
		foreach($form["fields"] as $field){
			if($field['id'] == $field_id) {
				# $output = $Forms->escape_text($field['label']); // No longer used
				$output = esc_html($field['label']); // Using esc_html(), a WP function
			}elseif(is_array($field['inputs'])) {
				foreach($field["inputs"] as $input){
					if($input['id'] == $field_id) {
						if($Common) {
							$output = esc_html($Common->get_label($field,$field_id));
						} else {
							#$output = $Forms->escape_text($Forms->get_label($field,$field_id));  // No longer used
							$output = esc_html($Forms->get_label($field,$field_id));  // No longer used
						}
					}
				}
			}
		}
		return $output;
	}
	function gf_field_label($form_id, $field_id) {
		echo get_gf_field_label($form_id, $field_id);
	}	
	
	// Returns a form using php instead of shortcode
	function get_gf_form($id, $display_title=true, $display_description=true, $force_display=false, $field_values=null){
		global $Forms,$RG,$Common,$FormDisplay;
		if($FormDisplay) {	
			return $FormDisplay->get_form($id, $display_title=true, $display_description=true, $force_display=false, $field_values=null);
		} else {
			return $RG->get_form($id, $display_title, $display_description);
		}
	}
	function gf_form($id, $display_title=true, $display_description=true, $force_display=false, $field_values=null){
		echo get_gf_form($id, $display_title, $display_description, $force_display, $field_values);
	}
	
	// Returns array of leads for a specific form
	function get_gf_leads($form_id, $sort_field_number=0, $sort_direction='DESC', $search='', $offset=0, $page_size=3000, $star=null, $read=null, $is_numeric_sort = false, $start_date=null, $end_date=null) {
		global $Forms, $RG,$Common;
		
		return $RG->get_leads($form_id,$sort_field_number, $sort_direction, $search, $offset, $page_size, $star, $read, $is_numeric_sort, $start_date, $end_date);
	}
	
	function gf_leads($form_id, $sort_field_number=0, $sort_direction='DESC', $search='', $offset=0, $page_size=3000, $star=null, $read=null, $is_numeric_sort = false, $start_date=null, $end_date=null) {
		echo get_gf_leads($form_id,$sort_field_number, $sort_direction, $search, $offset, $page_size, $star, $read, $is_numeric_sort, $start_date, $end_date);
	}
	

} else {
	// If the classes don't exist, the plugin won't do anything useful.
	function kws_gf_warning() {
			echo "<div id='kws-gf-warning' class='updated fade'><p><strong>".sprintf(__('Gravity Forms Addons has detected a problem: required Gravity Forms files cannot be found'), "http://bit.ly/dvF8BI")."</strong></p><p>".sprintf(__('The <a href="%1$s">Gravity Forms plugin</a> must be installed and activated for the Gravity Forms Addons plugin to work.</p><p>If you haven\'t installed the plugin, you can <a href="%1$s">purchase the plugin here</a>. If you have, and you believe this notice is in error, <a href="%2$s">start a topic on the plugin support forum</a>.'), "http://bit.ly/dvF8BI", "http://wordpress.org/tags/gravity-forms-addons?forum_id=10#postform")."</p></div>";
	}
	
	add_action('admin_notices', 'kws_gf_warning');
}
?>