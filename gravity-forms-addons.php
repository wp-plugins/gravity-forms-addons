<?php
/**
 * @package Gravity-forms-addons
 * @author Zack Katz
 * @version 1.0
 */
/*
Plugin Name: Gravity Forms Addons
Plugin URI: http://www.seodenver.com/gravity-forms-addons/
Description: Add functionality to the great Gravity Forms plugin. Features: Expand all of the 'Add Fields' boxes at once
Author: Katz Web Services, Inc.
Version: 1.0
Author URI: http://www.katzwebservices.com
*/

function kws_gv_expand_boxes_css() {
?>
	<style type="text/css"> .gforms_edit_form ul.menu li ul { display:block!important; } </style>
	<script type="text/javascript">
		jQuery(document).ready(function($) { 
			$("ul.menu li ul").each(function() {
				// Prevent the slideUp/slideDown functions from working
				$(this).css('min-height', $(this).height()).css('height', $(this).height()) ;
			});
		});
	</script>
<?php 
}

add_action('admin_head', 'kws_gv_expand_boxes_css',1);

?>
