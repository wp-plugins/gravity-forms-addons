<?php 

function kws_print_r($content) {
	echo '<pre>'.print_r($content, true).'</pre>';
	return $content;
}
add_filter('kws_gf_directory_detail', 'kws_gf_convert_to_ul', 1, 2);
add_filter('kws_gf_directory_output', 'kws_gf_convert_to_ul', 1, 2);
#add_filter('kws_gf_directory_detail', 'kws_gf_convert_to_dl', 1, 2);
#add_filter('kws_gf_directory_output', 'kws_gf_convert_to_dl', 1, 2);

function kws_gf_pseudo_filter($content = null, $type = 'table', $single = false) {
	switch($type) {
		case 'table':
			return $content;
			break;
		case 'ul':
			$content = kws_gf_convert_to_ul($content, $single);
			break;	
		case 'table':
		case 'dl':
			$content = kws_gf_convert_to_dl($content, $single);
			break;
	}
	return $content;
}

function kws_gf_convert_to_ul($content = null, $singleUL = false) {
	#echo $content;
	$strongHeader = apply_filters('kws_gf_convert_to_ul_strong_header', 1);
	
#	$content = preg_replace("/\ \ /ism"," ", $content);

	// Directory View
	if(!$singleUL) { 
		$content = preg_replace("/<table([^>]*)>/ism","<ul$1>", $content);
		$content = preg_replace("/<\/table([^>]*)>/ism","</ul>", $content);
		if($strongHeader) {
			$content = preg_replace("/<tr([^>]*)>\s+/","\n\t\t\t\t\t\t\t\t\t\t\t\t<li$1><ul>", $content);
			$content = preg_replace("/<th([^>]*)\>(.*?)\<\/th\>/","$2</strong>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<ul>", $content);
		} else {
			$content = preg_replace("/<tr([^>]*)>\s+/","\n\t\t\t\t\t\t\t\t\t\t\t\t<li$1>", $content);
			$content = preg_replace("/<th([^>]*)\>(.*?)\<\/th\>/","$2\n\t\t\t\t\t\t\t\t\t\t\t\t\t<ul>", $content);
		}
		$content = preg_replace("/<\/tr[^>]*>/","\t\t\t\t\t</ul>\n\t\t\t\t\t\t\t\t\t\t\t\t</li>", $content);
	} 
	// Single listing view
	else {
		$content = preg_replace("/<table([^>]*)>/ism","<ul$1>", $content);
		$content = preg_replace("/<\/table([^>]*)>/ism","</ul>", $content);
		if($strongHeader) {
			$content = preg_replace("/<tr([^>]*)>\s+/","\n\t\t\t\t\t\t\t\t\t\t\t\t<li$1><strong>", $content);
			$content = preg_replace("/<th([^>]*)\>(.*?)\<\/th\>/","$2</strong>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<ul>", $content);
		} else {
			$content = preg_replace("/<tr([^>]*)>\s+/","\n\t\t\t\t\t\t\t\t\t\t\t\t<li$1>", $content);
			$content = preg_replace("/<th([^>]*)\>(.*?)\<\/th\>/","$2\n\t\t\t\t\t\t\t\t\t\t\t\t\t<ul>", $content);
		}
		$content = preg_replace("/<\/tr[^>]*>/","\t\t\t\t\t</ul>\n\t\t\t\t\t\t\t\t\t\t\t\t</li>", $content);
	}
#	$content = preg_replace("/\<\/p\>\s+\<\/li/ism","\<\/p\>\<\/li", $content);
	$content = preg_replace("/(?:\s+)?(valign\=\"(?:.*?)\"|width\=\"(?:.*?)\"|cellspacing\=\"(?:.*?)\")(?:\s+)?/ism", ' ', $content);
	$content = preg_replace("/<\/?tbody[^>]*>/","", $content);
	$content = preg_replace("/<thead[^>]*>.*<\/thead>|<tfoot[^>]*>.*<\/tfoot>/is","", $content);
	$content = preg_replace("/\<td([^>]*)\>(\&nbsp;|)\<\/td\>/","", $content);
	$content = preg_replace("/\<td([^>]*)\>/","\t\t\t\t\t<li$1>", $content);
	$content = preg_replace("/<\/td[^>]*>/","</li>", $content);
	$content = preg_replace('/\s?colspan\="([^>]*?)"\s?/ism', ' ', $content);
	return $content;
}

function kws_gf_convert_to_dl($content) {
	$back = '';
	$output = '<dl>';
	// Get the back link, if it exists
	preg_match("/\<p\sclass=\"entryback\"\>(.*?)\<\/p\>/", $content, $matches);
	if(isset($matches[0])) { $back = $matches[0]; }
	$content = preg_replace("/\<p\sclass=\"entryback\"\>(.*?)\<\/p\>/", "", $content);
	$content = preg_replace("/<\/?table[^>]*>|<\/?tbody[^>]*>/","", $content);
	$content = preg_replace("/<thead[^>]*>.*<\/thead>|<tfoot[^>]*>.*<\/tfoot>/is","", $content);
#	$content = preg_replace("/<tr([^>]*)>/","<dl$1>", $content);
#	$content = preg_replace("/<\/tr[^>]*>/","</dl>", $content);
	$content = preg_replace("/<tr([^>]*)>/","", $content);
	$content = preg_replace("/<\/tr[^>]*>/","", $content);
	$content = preg_replace("/\<td([^>]*)\>(\&nbsp;|)\<\/td\>/","", $content);
	$content = preg_replace("/\<th([^>]*)\>(.*?)<\/th\>/ism","<dt$1>$2</dt>", $content);
	$content = preg_replace('/<td(.*?)(title="(.*?)")?>(.*?)<\/td[^>]*>/ism',"<dt$1>$3</dt><dd>$4</dd>", $content);
	$output .= $back;
	$output .= $content;
	$output .= '</dl>';
	return $output;
}

// Version: 2.5
add_shortcode('directory', 'kws_gf_directory');

function kws_gf_directory_defaults() {
	$defaults = array(
			'form' => 1, // Gravity Forms form ID
			'approved' => false, // Show only entries that have been Approved (have a field in the form that is an Admin-only checkbox with a value of 'Approved' 
			'directoryview' => 'table', // Table, list or DL
			'entryview' => 'table', // Table, list or DL
			'hovertitle' => true, // Show column name as user hovers over cell
			'tableclass' => 'gf_directory widefat fixed', // Class for the <table>
			'tablestyle' => '', // inline CSS for the <table>
			'rowclass' => '', // Class for the <table>
			'rowstyle' => '', // inline CSS for all <tbody><tr>'s
			'valign' => 'baseline',
			'sort' => 'date_created', // Use the input ID ( example: 1.3 or 7 or ip )
			'dir' => 'DESC',
			'wpautop' => true, // Convert bulk paragraph text to...paragraphs
			'page_size' => 20, // Number of entries to show at once
			'startpage' => 1, // If you want to show page 8 instead of 1
			'lightbox' => true, // Do you want your image uploads to be lightboxed?
			'showcount' => true, // Do you want to show "Displaying 1-19 of 19"?
			'pagelinksshowall' => true, // Whether to show each page number, or just 7
			'pagelinkstype' => 'plain', // 'plain' is just a string with the links separated by a newline character. The other possible values are either 'array' or 'list'. 
			'showrowids' => true, // Whether or not to show the row ids, which are the entry IDs.
			'fulltext' => true, // If there's a textarea or post content field, show the full content or a summary?
			'linkemail' => true, // Convert email fields to email mailto: links
			'linkwebsite' => true, // Convert URLs to links
			'linknewwindow' => false, // Open links in new window? (uses target="_blank")
			'nofollowlinks' => false, // Add nofollow to all links, including emails
			'icon' => false, // show the GF icon as it does in admin?
			'titleshow' => true, // Show a form title? By default, the title will be the form title.
			'titleprefix' => 'Entries for ', // Default GF behavior is 'Entries : '
			'tablewidth' => '100%', // 'width' attribute for the table
			'searchtabindex' => false, // adds tabindex="" to the search field
			'search' => true, // show the search field
			'tfoot' => true, // show the <tfoot>
			'thead' => true, // show the <thead>
			'showadminonly' => false, // Admin only columns aren't shown by default, but can be (added 2.0.1)
			'datecreatedformat' => get_option('date_format').' \a\t '.get_option('time_format'), // Use standard PHP date formats (http://php.net/manual/en/function.date.php)
			'dateformat' => false, // Override the options from Gravity Forms, and use standard PHP date formats (http://php.net/manual/en/function.date.php)
			'postimage' => 'icon', // Whether to show icon, thumbnail, or large image
			'entry' => true, // If there's an Entry ID column, link to the full entry
			'entrylightbox' => false,
			'entrylink' => 'View entry details',
			'entryth' => 'More Info',
			'entryback' => '&larr; Back to directory',
			'entryonly' => true,
			'entrytitle' => 'Entry Detail',
			'entryanchor' => true,
			'truncatelink' => false,
			'appendaddress' => false,
			'hideaddresspieces' => false
		);
	return apply_filters('kws_gf_directory_defaults', $defaults);
}

// Add this filter so it can be removed or overridden by users
add_filter('kws_gf_directory_td_address', 'kws_gf_format_address');

function kws_gf_prep_address_field($field) {
	return !empty($field) ? trim($field) : '';
}
function kws_gf_format_address($address = array()) {
	$address_field_id = @kws_gf_prep_address_field($address['id']);
	$street_value = @kws_gf_prep_address_field($address[$address_field_id . ".1"]);
	$street2_value = @kws_gf_prep_address_field($address[$address_field_id . ".2"]);
	$city_value = @kws_gf_prep_address_field($address[$address_field_id . ".3"]);
	$state_value = @kws_gf_prep_address_field($address[$address_field_id . ".4"]);
	$zip_value = @kws_gf_prep_address_field($address[$address_field_id . ".5"]);
	$country_value = @kws_gf_prep_address_field($address[$address_field_id . ".6"]);

	$address = $street_value;
	$address .= !empty($address) && !empty($street2_value) ? "<br />$street2_value" : $street2_value;
	$address .= !empty($address) && (!empty($city_value) || !empty($state_value)) ? "<br />$city_value" : $city_value;
	$address .= !empty($address) && !empty($city_value) && !empty($state_value) ? ", $state_value" : $state_value;
	$address .= !empty($address) && !empty($zip_value) ? " $zip_value" : $zip_value;
	$address .= !empty($address) && !empty($country_value) ? "<br />$country_value" : $country_value;

	//adding map link
	if(!empty($address) && apply_filters('kws_gf_directory_td_address_map', 1)) {
		$address_qs = str_replace("<br />", " ", $address); //replacing <br/> with spaces
		$address_qs = urlencode($address_qs);
		$address .= "<br/><a href='http://maps.google.com/maps?q=$address_qs' target='_blank' class='map-it-link'>Map It</a>";
	}
	return $address;
}


add_filter('kws_gf_directory_anchor_text', 'kws_gf_directory_anchor_text');

function kws_gf_directory_anchor_text($value = null) {
	
	if(apply_filters('kws_gf_directory_anchor_text_striphttp', true)) {
		$value = str_replace('http://', '', $value);
		$value = str_replace('https://', '', $value);
	}
	
	if(apply_filters('kws_gf_directory_anchor_text_stripwww', true)) {
		$value = str_replace('www.', '', $value);
	}
	if(apply_filters('kws_gf_directory_anchor_text_rootonly', true)) {
		$value = preg_replace('/(.*?)\/(.+)/ism', '$1', $value);
	}
	if(apply_filters('kws_gf_directory_anchor_text_nosubdomain', true)) {
		$value = preg_replace('/((.*?)\.)+(.*?)\.(.*?)/ism', '$3.$4', $value);
	}
	if(apply_filters('kws_gf_directory_anchor_text_noquerystring', true)) {
		$ary = explode("?", $value);
		$value = $ary[0];
	}
	return $value;
}


function kws_gf_directory($atts) { 
	global $wpdb,$wp_rewrite,$post, $wpdb;
	
	//quit if version of wp is not supported
	if(!GFCommon::ensure_wp_version())
		return;

	ob_start(); // Using ob_start() allows us to use echo instead of $output .=
	
	foreach($atts as $key => $att) { 
		if(strtolower($att) == 'false') { $atts[$key] = false; }
		if(strtolower($att) == 'true') { $atts[$key] = true; }
	}
	$options = shortcode_atts( kws_gf_directory_defaults(), $atts );
	extract( $options );
		
		$form_id = $form;
		$sort_field = empty($_GET["sort"]) ? $sort : $_GET["sort"];
		$sort_direction = empty($_GET["dir"]) ? $dir : $_GET["dir"];
		$search_query = isset($_GET["gf_search"]) ? $_GET["gf_search"] : null;
		$page_index = empty($_GET["page"]) ? $startpage -1 : intval($_GET["page"]) - 1;
		$star = (isset($_GET["star"]) && is_numeric($_GET["star"])) ? intval($_GET["star"]) : null;
		$read = (isset($_GET["read"]) && is_numeric($_GET["read"])) ? intval($_GET["read"]) : null;
		$first_item_index = $page_index * $page_size;
		$form = RGFormsModel::get_form_meta($form_id);
		$link_params = array();
		if(!empty($page_index)) { $link_params['page'] = $page_index; }
		$formaction = remove_query_arg(array('gf_search','sort','dir'), add_query_arg($link_params));
		if($titleshow === true) { $title = $form["title"]; }
		$sort_field_meta = RGFormsModel::get_field($form, $sort_field);
		$is_numeric = $sort_field_meta["type"] == "number";

		$columns = RGFormsModel::get_grid_columns($form_id, true);
		$approvedcolumn = kws_gf_get_approved_column($form);
		$adminonlycolumns = kws_gf_get_admin_only($form);

		$leads = RGFormsModel::get_leads($form_id, $sort_field, $sort_direction, $search_query, $first_item_index, $page_size, $star, $read, $is_numeric);
		
		$approvedleads = 0;
		if($approved) {
			foreach($leads as $lead) {
				if(kws_gf_check_approval($lead, $approvedcolumn)) {
					$approvedleads++;
				}
			}
		} else{
			$approvedleads = sizeof($leads);
		}
		#kws_print_r($leads);
/*
		// Todo: Fix page size when approved entries are turned on
		if($approved) {
			$notapprovedleads = 0;
			foreach($leads as $lead) {
				if(!kws_gf_check_approval($lead, $approvedcolumn)) {
					$notapprovedleads++;
				}
			}
			if(!empty($leads) && !empty($notapprovedleads)) { $page_size2 = $notapprovedleads * $page_size; }
			
			$leads = RGFormsModel::get_leads($form_id, $sort_field, $sort_direction, $search_query, $first_item_index, $page_size2, $star, $read, $is_numeric);
		}
*/
	
		
		if(!$showadminonly)	 {
			$leads = kws_gf_remove_admin_only($leads, $adminonlycolumns, $approvedcolumn, true); 
			$columns = kws_gf_remove_admin_only($columns, $adminonlycolumns, $approvedcolumn, false); 
		}
		
		$lead_count = kws_gf_get_lead_count($form_id, $search_query, $star, $read, $approvedcolumn, $approved, $leads);

		if($entry === true && $detail = kws_gf_process_lead_detail(true, $entryback, $showadminonly, $adminonlycolumns, $approvedcolumn, $options)) {
			echo $detail;
			if($entryonly) {
				return;
			}
		};
		
		// Allow lightbox to determine whether showadminonly is valid without passing a query string in URL
		if($entry === true && !empty($entrylightbox)) {
			if(get_transient('gf_form_'.$form['id'].'_post_'.$post->ID.'_showadminonly') != $showadminonly) {
				set_transient('gf_form_'.$form['id'].'_post_'.$post->ID.'_showadminonly', $showadminonly, 60*60);	
			}
		} else {
			delete_transient('gf_form_'.$form['id'].'_post_'.$post->ID.'_showadminonly');
		}
		
		
		// Get a list of query args for the pagination links
		if(!empty($search_query)) { $args["gf_search"] = urlencode($search_query); }
		if(!empty($sort_field)) { $args["sort"] = $sort_field; }
		if(!empty($sort_direction)) { $args["dir"] = $sort_direction; }
		if(!empty($star)) { $args["star"] = $star; }
		#$args['id'] = $form_id;
		
		$page_links = array(
			'base' =>  get_permalink().'%_%',
			'format' => '?page=%#%',
			'add_args' => $args,
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total' => ceil($lead_count / $page_size),
			'current' => $page_index + 1,
			'show_all' => $pagelinksshowall
		);
				
		$page_links = apply_filters('kws_gf_results_pagination', $page_links);
		
		$page_links = paginate_links($page_links);
		
		if($lightbox || $entrylightbox) {
			wp_print_scripts(array("thickbox"));
			wp_print_styles(array("thickbox"));
		}
		?>

		<script src="<?php echo GFCommon::get_base_url() ?>/js/jquery.json-1.3.js"></script>

		<script type="text/javascript">
			
			function not_empty(variable) { 
				if(variable == '' || variable == null || variable == 'undefined' || typeof(variable) == 'undefined') {
					return false;
				} else { 
					return true;
				}
			}
			
			function Search(search, sort_field_id, sort_direction){
				if(not_empty(search)) { var search = "&gf_search=" + search; } else {  var search = ''; }
				if(not_empty(sort_field_id)) { var sort = "&sort=" + sort_field_id; } else {  var sort = ''; }
				if(not_empty(sort_direction)) { var dir = "&dir=" + sort_direction; } else {  var dir = ''; }
				var page = '<?php if($wp_rewrite->using_permalinks()) { echo '?'; } else { echo '&'; } ?>page='+<?php echo isset($_GET['page']) ? intval($_GET['page']) : '"1"'; ?>;
				var location = "<?php echo remove_query_arg(array('gf_search','sort','dir'), add_query_arg(array())); ?>"+page+search+sort+dir;
				document.location = location;
			}

		<?php if($search) { ?>
			jQuery(document).ready(function(){
				jQuery("#lead_form").submit(function(event){
					event.preventDefault();
					Search(jQuery('#lead_search').val());
					return false;
				});

				jQuery("#lead_form").attr('action', "<?php echo $formaction; ?>");

			});
		<?php } ?>
		</script>
		<link rel="stylesheet" href="<?php echo GFCommon::get_base_url() ?>/css/admin.css" type="text/css" />

		<div class="wrap">
			<?php if($icon) { ?><img alt="<?php _e("Gravity Forms", "gravityforms") ?>" src="<?php echo GFCommon::get_base_url()?>/images/gravity-title-icon-32.png" style="float:left; margin:15px 7px 0 0;"/><?php } ?>
			<?php if($titleshow) { ?><h2><?php echo $titleprefix.$title; ?> </h2><?php } ?>
			<?php if($search) { ?>
			<form id="lead_form" method="get" action="<?php echo $formaction; ?>">
				<p class="search-box">
					<label class="hidden" for="lead_search"><?php _e("Search Entries:", "gravityforms"); ?></label>
					<input type="text" name="gf_search" id="lead_search" value="<?php echo $search_query ?>"<?php if($searchtabindex) { echo ' tabindex="'.intval($searchtabindex).'"';}?> />
					<input type="hidden" name="p" value="<?php echo isset($post->ID) ? $post->ID : 0; ?>" />
					<input type="submit" class="button" id="lead_search_button" value="<?php _e("Search", "gravityforms") ?>"<?php if($searchtabindex) { echo ' tabindex="'.intval($searchtabindex++).'"';}?> />
				</p>
			</form>
			<?php } ?>
				<div class="tablenav">

					<?php
					//Displaying paging links if appropriate
					
					if($showcount){
						if($lead_count == 0) { $first_item_index--; }
						?>
						<div class="tablenav-pages">
							<span class="displaying-num"><?php printf(__("Displaying %d - %d of %d", "gravityforms"), $first_item_index + 1, ($first_item_index + $page_size) > $lead_count ? $lead_count : $first_item_index + $page_size , $lead_count) ?></span>
							<?php } if($page_links){ echo $page_links; }if($showcount){ ?>
						</div>
						<?php
				   }
					?>
					<div class="clear"></div>
				</div>
				
				<table class="<?php echo $tableclass; ?>" cellspacing="0"<?php if(!empty($tablewidth)) { echo ' width="'.$tablewidth.'"'; } echo $tablestyle ? ' style="'.$tablestyle.'"' : ''; ?>>
				<?php if($thead) {?>
				<thead>
					<tr>
						<?php
						
						$addressesExist = false;
						foreach($columns as $field_id => $field_info){
							$dir = $field_id == 0 ? "DESC" : "ASC"; //default every field so ascending sorting except date_created (id=0)
							if($field_id == $sort_field) { //reverting direction if clicking on the currently sorted field
								$dir = $sort_direction == "ASC" ? "DESC" : "ASC";
							}
							if(is_array($adminonlycolumns) && !in_array($field_id, $adminonlycolumns) || (is_array($adminonlycolumns) && in_array($field_id, $adminonlycolumns) && $showadminonly) || !$showadminonly) {
							if($field_info['type'] == 'address' && $appendaddress && $hideaddresspieces) { $addressesExist = true; continue; }
							?>
							<th scope="col" class="manage-column" onclick="Search('<?php echo $search_query ?>', '<?php echo $field_id ?>', '<?php echo $dir ?>');" style="cursor:pointer;"><?php 
							if($field_info['type'] == 'id' && $entry) { $label = $entryth; }
							else { $label = $field_info["label"]; }
							
							$label = apply_filters('kws_gf_directory_th', apply_filters('kws_gf_directory_th_'.$field_id, apply_filters('kws_gf_directory_th_'.sanitize_title($label), $label)));
							echo esc_html($label); 
						   
							 ?></th>
							<?php
							}
						}
						
						if($appendaddress && $addressesExist) {
							?>
							<th scope="col" class="manage-column" onclick="Search('<?php echo $search_query ?>', '<?php echo $field_id ?>', '<?php echo $dir ?>');" style="cursor:pointer;"><?php 
							$label = apply_filters('kws_gf_directory_th', apply_filters('kws_gf_directory_th_address', 'Address'));
							echo esc_html($label) 
						   
							 ?></th>
							<?php
						}
						?>
					</tr>
				</thead>
				<?php } ?>
				<tbody class="list:user user-list">
					<?php
					#kws_print_r($leads);
					if(sizeof($leads) > 0 && $lead_count > 0){
					
						$field_ids = array_keys($columns);
						
						foreach($leads as $lead){
							echo "\n\t\t\t\t\t\t";
							
							$address = array(); $celltitle = '';
							$leadapproved = false; if($approved) { $leadapproved = kws_gf_check_approval($lead, $approvedcolumn); }
							
							
#							 if(in_array(key() $adminonlycolumns )
							
							if($leadapproved && $approved || !$approved) {
								$target = ''; if($linknewwindow) { $target = ' target="_blank"'; }
								$valignattr = ''; if($valign && $directoryview == 'table') { $valignattr = ' valign="'.$valign.'"'; } 
								$lightboxclass = ''; if($lightbox) { $lightboxclass = '	 class="thickbox"'; }
								$nofollow = ''; if($nofollowlinks) { $nofollow = ' rel="nofollow"'; }
									
							?><tr<?php if($showrowids){ ?> id="lead_row_<?php echo $lead["id"] ?>" <?php } ?>class='<?php echo $rowclass; echo $lead["is_starred"] ? " featured" : "" ?>'<?php echo $rowstyle ? ' style="'.$rowstyle.'"' : ''; echo $valignattr; ?>><?php
								$class = "";
								$is_first_column = true;
								$full_address = '';
								
								
								foreach($field_ids as $field_id){
									$value = isset($lead[$field_id]) ? $lead[$field_id] : '';
									$input_type = !empty($columns[$field_id]["inputType"]) ? $columns[$field_id]["inputType"] : $columns[$field_id]["type"];
									switch($input_type){
										
										case "address" :
											$address['id'] = floor((int)$field_id);
											$address[$field_id] = $value;
											if($hideaddresspieces) { $value = NULL; break; }
												break;
											
										case "checkbox" :
											$value = "";

											//looping through lead detail values trying to find an item identical to the column label. Mark with a tick if found.
											$lead_field_keys = array_keys($lead);
											foreach($lead_field_keys as $input_id){
												//mark as a tick if input label (from form meta) is equal to submitted value (from lead)
												if(is_numeric($input_id) && absint($input_id) == absint($field_id) && $lead[$input_id] == $columns[$field_id]["label"]){
													$value = "<img src='" . GFCommon::get_base_url() . "/images/tick.png'/>";
												}
											}
										break;

										case "post_image" :
											list($url, $title, $caption, $description) = explode("|:|", $value);
											$size = '';
											if(!empty($url)){
												//displaying thumbnail (if file is an image) or an icon based on the extension
												 $icon = kws_gf_get_icon_url($url);
												 if(!preg_match('/icon\_image\.gif/ism', $icon)) { 
												 	$lightboxclass = '';
												 	$size = @getimagesize($icon);
												 	$img = "<img src='$src' {$size[3]}/>";
												 } else { // No thickbox for non-images please
												 	switch(strtolower(trim($postimage))) {
												 		case 'image':
												 			$src = $url;
												 			break;
												 		case 'icon' :
												 		default:
												 			$src = $icon;
												 			break;
												 	}
												 	$size = @getimagesize($src);
												 }
												 $img = array(
												 	'src' => $src,
												 	'size' => $size,
												 	'title' => $title,
												 	'caption' => $caption,
												 	'description' => $description,
												 	'url' => $url,
												 	'code' => "<img src='$src' {$size[3]} />"
												 );
												 $img = apply_filters('kws_gf_directory_lead_image', apply_filters('kws_gf_directory_lead_image_'.$postimage, apply_filters('kws_gf_directory_lead_image_'.$lead['id'], $img)));
												 $value = "<a href='$url'$target$lightboxclass>{$img['code']}</a>";
											}
										break;

										case "fileupload" :
											$file_path = $value;
											if(!empty($file_path)){
												//displaying thumbnail (if file is an image) or an icon based on the extension
												 $thumb = kws_gf_get_icon_url($file_path);
												 $file_path = esc_attr($file_path);
												 $value = "<a href='$file_path'$target$nofollow$lightboxclass><img src='$thumb'/></a>";
											}
										break;

										case "source_url" :
											if($linkwebsite) {
												$value = "<a href='" . esc_attr($lead["source_url"]) . "'$target alt='" . esc_attr($lead["source_url"]) ."' title='" . esc_attr($lead["source_url"]) . "'$nofollow>.../" .esc_attr(GFCommon::truncate_url($lead["source_url"])) . "</a>";
											} else {
												$value = esc_attr(GFCommon::truncate_url($lead["source_url"]));
											}
										break;

										case "textarea" :
										case "post_content" :
										case "post_excerpt" :
											if($fulltext) {
												$long_text = $value = "";
												if(isset($lead[$field_id]) && strlen($lead[$field_id]) >= GFORMS_MAX_FIELD_LENGTH) {
													   $long_text = get_gf_field_value_long($lead["id"], $field_id);
												   }
												if(isset($lead[$field_id])) {
													$value = !empty($long_text) ? $long_text : $lead[$field_id];
												}
												
												if($wpautop) { $value = wpautop($value); };
												
											} else {
												$value = esc_html($value);
											}
										break;

										case "date_created" :
											$value = kws_gf_format_date($value, false, $datecreatedformat);
										break;

										case "date" :
											$field = RGFormsModel::get_field($form, $field_id);
											if($dateformat) {
												 $value = GFCommon::date_display($value, $dateformat);
											 } else {
											 	$value = GFCommon::date_display($value, $field["dateFormat"]); 
											 }
										break;
										
										case "id" :
											$linkClass = '';
											if($entry) {
												$entrytitle = apply_filters('kws_gf_directory_detail_title', apply_filters('kws_gf_directory_detail_title_'.$value, $entrytitle));
												if($entrylightbox || $lightbox && $entrylightbox === '') {
													$elwidth = apply_filters('kws_gf_directory_lightbox_entry_width', 600);
													$elheight = apply_filters('kws_gf_directory_lightbox_entry_height', ''); 
													$href = WP_PLUGIN_URL . "/" . basename(dirname(__FILE__)) . "/entry-details.php?leadid=$value&amp;form={$form['id']}&amp;post={$post->ID}&amp;KeepThis=true&amp;TB_iframe=true&amp;width=$elwidth&amp;height=$elheight"; $linkClass = ' class="thickbox lightbox"'; 
												} else {
													$multisite = (function_exists('is_multisite') && is_multisite() && $wpdb->blogid == 1);
													if($wp_rewrite->using_permalinks() && $multisite) {
														// example.com/example-directory/entry/4/14/
														$url = parse_url(add_query_arg(array()));
														$href = trailingslashit($url['path']).'entry/'.$form['id'].'-'.$value.'/';
														if(!empty($url['query'])) { $href .= '?'.$url['query']; }
													} else {
														// example.com/?page_id=24&leadid=14&form=4
														$href = add_query_arg(array('leadid'=>$value, 'form' => $form['id']));
													}
												}
												if($showrowids && $entryanchor) {
													// example.com/?page_id=24&leadid=14&form=4&row=58
													// example.com/example-directory/entry/4/14/?row=58
													$href = add_query_arg(array('row'=>$lead["id"]), $href);
												} 
												$value = '<a href="'.$href.'"'.$linkClass.' title="'.$entrytitle.'">'.$entrylink.'</a>';
											}
										break;
										
										default:

											$input_type = 'text';
											if(is_email($value) && $linkemail) {$value = "<a href='mailto:$value'$nofollow>$value</a>"; } 
											elseif(preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $value) && $linkwebsite) {
												if($lightbox) { 
													$elwidth = apply_filters('kws_gf_directory_lightbox_width', 600);
													$elheight = apply_filters('kws_gf_directory_lightbox_height', 400); 
													$href = $value .'?KeepThis=true&TB_iframe=true&amp;width='.$elwidth.'&amp;height='.$elheight; $linkClass = ' class="thickbox lightbox"'; 
												}
												if($truncatelink) {
													$value = apply_filters('kws_gf_directory_anchor_text', $value);
												}
												$value = "<a href='$href'{$nofollow}{$target}{$linkClass}>$value</a>"; 
											}
											else { $value = esc_html($value); }
									}
									if($is_first_column) { echo "\n"; }
									if($value !== NULL) {
#								kws_print_r($columns);
#								kws_print_r($field_ids);
										
										if(isset($columns["{$field_id}"]['label']) && $hovertitle || $directoryview !== 'table') {
											$celltitle = ' title="'.esc_html(apply_filters('kws_gf_directory_th', apply_filters('kws_gf_directory_th_'.$field_id, apply_filters('kws_gf_directory_th_'.sanitize_title($label), $columns["{$field_id}"]['label'])))).'"';
										} else {
											$celltitle = '';
										}
									 	echo "\t\t\t\t\t\t\t"; ?><td<?php echo empty($class) ? ' class="'.$input_type.'"' : ' class="'.$input_type.' '.$class.'"'; echo $valignattr; echo $celltitle; ?>><?php 
									 	
									 	
									 	$value = empty($value) ? '&nbsp;' : $value;
									 	$value = apply_filters('kws_gf_directory_value', apply_filters('kws_gf_directory_value_'.$input_type, apply_filters('kws_gf_directory_value_'.$field_id, $value)));
									 echo $value;
									 
									?></td><?php
										echo "\n";
										$is_first_column = false;
									}
								}
								
								if(is_array($address) && !empty($address) && $appendaddress) {
									$address = apply_filters('kws_gf_directory_td_address', $address);
									if(!is_array($address)) {
										echo "\t\t\t\t\t\t\t".'<td class="address" title="'.esc_html(apply_filters('kws_gf_directory_th', apply_filters('kws_gf_directory_th_address', 'Address'))).'">'.$address.'</td>';
									}
								}
							   	?>
						</tr>
							<?php }
						}
					}
					else{
						?>
						<tr>
							<td colspan="<?php echo sizeof($columns); ?>" style="padding:20px;"><?php _e("This form does not have any entries yet.", "gravityforms"); ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
				<?php if($tfoot) { ?>
				<tfoot>
					<tr>
						<?php
						foreach($columns as $field_id => $field_info){
							$dir = $field_id == 0 ? "DESC" : "ASC"; //default every field so ascending sorting except date_created (id=0)
							if($field_id == $sort_field) { //reverting direction if clicking on the currently sorted field
								$dir = $sort_direction == "ASC" ? "DESC" : "ASC";
							}
							if(is_array($adminonlycolumns) && !in_array($field_id, $adminonlycolumns) || (is_array($adminonlycolumns) && in_array($field_id, $adminonlycolumns) && $showadminonly) || !$showadminonly) {
							if($field_info['type'] == 'address' && $appendaddress && $hideaddresspieces) { $addressesExist = true; continue; }
							?>
							<th scope="col" class="manage-column" onclick="Search('<?php echo $search_query ?>', '<?php echo $field_id ?>', '<?php echo $dir ?>');" style="cursor:pointer;"><?php 
							if($field_info['type'] == 'id' && $entry) { $label = $entryth; }
							else { $label = $field_info["label"]; }
							
							$label = apply_filters('kws_gf_directory_th', apply_filters('kws_gf_directory_th_'.$field_id, apply_filters('kws_gf_directory_th_'.sanitize_title($label), $label)));
							echo esc_html($label) 
						   
							 ?></th>
							<?php
							}
						}
						if($appendaddress && $addressesExist) {
							?>
							<th scope="col" class="manage-column" onclick="Search('<?php echo $search_query ?>', '<?php echo $field_id ?>', '<?php echo $dir ?>');" style="cursor:pointer;"><?php 
							$label = apply_filters('kws_gf_directory_th', apply_filters('kws_gf_directory_th_address', 'Address'));
							echo esc_html($label) 
						   
							 ?></th>
							<?php
						}
						?>
					</tr>
				</tfoot>
				<?php } ?>
				</table>

				<div class="clear"></div>

				<div class="tablenav">

					<?php
					//Displaying paging links if appropriate
					if($page_links){
						?>
						<div class="tablenav-pages">
							<span class="displaying-num"><?php printf(__("Displaying %d - %d of %d", "gravityforms"), $first_item_index + 1, ($first_item_index + $page_size) > $lead_count ? $lead_count : $first_item_index + $page_size , $lead_count) ?></span>
							<?php echo $page_links ?>
						</div>
						<?php
					}
					?>
					<div class="clear"></div>
				</div>
		</div>
		<?php
		$content = ob_get_contents(); // Get the output
		ob_end_clean(); // Clear the cache
		
		// If the form is form #2, two filters are applied: `kws_gf_directory_output_2` and `kws_gf_directory_output`
		$content = apply_filters('kws_gf_directory_output', apply_filters('kws_gf_directory_output_'.$form_id, kws_gf_pseudo_filter($content, $directoryview)));
		
		return $content; // Return it!
	}
	
	function kws_gf_make_class($class) {
		$class = str_replace('-', '_', sanitize_title($class));
		return $class;
	}
	
	function kws_gf_format_date($gmt_datetime, $is_human = true, $dateformat = 'Y/m/d \a\t H:i'){
		if(empty($gmt_datetime))
			return "";

		//adjusting date to local configured Time Zone
		$lead_gmt_time = mysql2date("G", $gmt_datetime);
		$lead_local_time = GFCommon::get_local_timestamp($lead_gmt_time);
		
		$date_display = date_i18n($dateformat, $lead_local_time, true);
		
		return $date_display;
	}
	
	function kws_gf_get_icon_url($path){
		$info = pathinfo($path);

		switch(strtolower($info["extension"])){

			case "css" :
				$file_name = "icon_css.gif";
			break;

			case "doc" :
				$file_name = "icon_doc.gif";
			break;

			case "fla" :
				$file_name = "icon_fla.gif";
			break;

			case "html" :
			case "htm" :
			case "shtml" :
				$file_name = "icon_html.gif";
			break;

			case "js" :
				$file_name = "icon_js.gif";
			break;

			case "log" :
				$file_name = "icon_log.gif";
			break;

			case "mov" :
				$file_name = "icon_mov.gif";
			break;

			case "pdf" :
				$file_name = "icon_pdf.gif";
			break;

			case "php" :
				$file_name = "icon_php.gif";
			break;

			case "ppt" :
				$file_name = "icon_ppt.gif";
			break;

			case "psd" :
				$file_name = "icon_psd.gif";
			break;

			case "sql" :
				$file_name = "icon_sql.gif";
			break;

			case "swf" :
				$file_name = "icon_swf.gif";
			break;

			case "txt" :
				$file_name = "icon_txt.gif";
			break;

			case "xls" :
				$file_name = "icon_xls.gif";
			break;

			case "xml" :
				$file_name = "icon_xml.gif";
			break;

			case "zip" :
				$file_name = "icon_zip.gif";
			break;

			case "gif" :
			case "jpg" :
			case "jpeg":
			case "png" :
			case "bmp" :
			case "tif" :
			case "eps" :
				$file_name = "icon_image.gif";
			break;

			case "mp3" :
			case "wav" :
			case "wma" :
				$file_name = "icon_audio.gif";
			break;

			case "mp4" :
			case "avi" :
			case "wmv" :
			case "flv" :
				$file_name = "icon_video.gif";
			break;

			default:
				$file_name = "icon_generic.gif";
			break;
		}

		return GFCommon::get_base_url() . "/images/doctypes/$file_name";
	}
  
  	function kws_gf_get_lead_count($form_id, $search, $star=null, $read=null, $column, $approved = false, $leads = array()){
		global $wpdb;

		if(!is_numeric($form_id))
			return "";

/*
		if($approved) { 
			$approvedleads = 0;
			foreach($leads as $lead) {
				if(kws_gf_check_approval($lead, $column)) {
					$approvedleads++;
				}
			}
			return $approvedleads; 
		}
*/

		$detail_table_name = RGFormsModel::get_lead_details_table_name();
		$lead_table_name = RGFormsModel::get_lead_table_name();

		$star_filter = $star !== null ? $wpdb->prepare("AND is_starred=%d ", $star) : "";
		$read_filter = $read !== null ? $wpdb->prepare("AND is_read=%d ", $read) : "";
		$start_date_filter = empty($start_date) ? "" : " AND datediff(date_created, '$start_date') >=0";
		$end_date_filter = empty($end_date) ? "" : " AND datediff(date_created, '$end_date') <=0";

		$search_term = "%$search%";
		$search_filter = empty($search) ? "" : $wpdb->prepare("AND value LIKE %s", $search_term);

		$sql = "SELECT count(distinct l.id)
				FROM $lead_table_name l
				INNER JOIN $detail_table_name ld ON l.id = ld.lead_id
				WHERE l.form_id=$form_id
				AND ld.form_id=$form_id
				$star_filter
				$read_filter
				$start_date_filter
				$end_date_filter
				$search_filter";
		if($approved) {
			$sql .= ' AND ld.value ="Approved" AND round(ld.field_number,1)='.$column;
		}
		return $wpdb->get_var($sql);
	}
	
	function kws_gf_check_approval($lead, $column) {
		if(isset($lead["{$column}"]) && strtolower($lead["{$column}"]) == 'approved') {
			return true;
		}
		return false;
	}
	
	function kws_gf_remove_admin_only($leads, $adminOnly, $approved, $isleads, $single = false) {
		$i = 0;
		if($isleads) {
			if(is_array($leads)) {
				foreach($leads as $key => $lead) {
					if(is_array($adminOnly)) {
						if(@in_array($key, $adminOnly) && $key != $approved && $key != floor($approved)) {
							if($single) {
								foreach($adminOnly as $ao) {
									unset($lead[$ao]);
								}
							} else {
								unset($leads[$i]);
							}
						}
					}
				}
			}
		} else {
			if(is_array($leads)) {
				foreach($leads as $key => $lead) {
					if(is_array($adminOnly)) {
						if(@in_array($key, $adminOnly) && $key != $approved && $key != floor($approved) && !$single || ($single && (!isset($lead['id']) || isset($lead['id']) && in_array($lead['id'], $adminOnly)))) {
							if($single) {
								unset($leads[floor($key)]);
							} else {
								unset($leads[$key]);
							}
						}
					}
				}
			}
		}
		return $leads;
	}
	
	function kws_gf_get_approved_column($form) {
		if(!is_array($form)) { return false; }
		
		foreach($form['fields'] as $key=>$col) {
			if(isset($col['inputs']) && is_array($col['inputs'])) {
				foreach($col['inputs'] as $key2=>$input) {
					if(strtolower($input['label']) == 'approved' && $col['type'] == 'checkbox' && !empty($col['adminOnly'])) {
						return $input['id'];
					}
				}
			}
		}
		
		foreach($form['fields'] as $key=>$col) {
			if(strtolower($col['label']) == 'approved' && $col['type'] == 'checkbox') {
				if(isset($col['inputs'][0]['id']))
				return $key;
			}
		}

		return false;
	}
	
	function kws_gf_get_admin_only($form, $adminOnly = array()) {
		if(!is_array($form)) { return false; }
		foreach($form['fields'] as $key=>$col) {
			if(!empty($col['adminOnly'])) {
				$adminOnly[] = $col['id'];
			}
			if(isset($col['inputs']) && is_array($col['inputs'])) { 
				foreach($col['inputs'] as $key2=>$input) {
					if(!empty($col['adminOnly'])) {
						$adminOnly[] = $input['id'];
					}
				}
			}
		}
		return $adminOnly;
	}
	



//------------------------------------------------------
//------------- PAGE/POST EDIT PAGE ---------------------

	//Action target that adds the "Insert Form" button to the post/page edit screen
	function kws_gf_add_form_button($context){
		$image_btn = WP_PLUGIN_URL . "/" . basename(dirname(__FILE__)) . "/form-button-1.png";
		$out = '<a href="#TB_inline?width=640&inlineId=select_gf_directory" class="select_gf_directory thickbox" title="' . __("Add a Gravity Forms Directory", 'gravityforms') . '"><img src="'.$image_btn.'" alt="' . __("Add Gravity Form", 'gravityform') . '" /></a>';
		return $context . $out;
	}

	//Action target that displays the popup to insert a form to a post/page
	function kws_gf_add_mce_popup(){
		?>
		<script type="text/javascript">
			function addslashes (str) {
				   // Escapes single quote, double quotes and backslash characters in a string with backslashes	 
				   // discuss at: http://phpjs.org/functions/addslashes
				   return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
			}
						
			jQuery('document').ready(function($) { 
			
				jQuery('#select_gf_directory_form').bind('submit', function(e) {
					e.preventDefault();
					var shortcode = InsertGFDirectory();
					//send_to_editor(shortcode);
					return false;
				});
				
				jQuery('#insert_gf_directory').live('click', function(e) {
					e.preventDefault();
					
					$('#select_gf_directory_form').trigger('submit');
					return;
				});
				
				jQuery('a.select_gf_directory').live('click', function(e) {	
					// This auto-sizes the box
					if(typeof tb_position == 'function') {
						tb_position();
					}
					return;		
				});
				
				jQuery('a.kws_gf_advanced_settings').click(function(e) {  e.preventDefault(); jQuery('#kws_gf_advanced_settings').toggle(); return false; });
				
				function InsertGFDirectory(){
					var directory_id = jQuery("#add_directory_id").val();
					if(directory_id == ""){
						alert("<?php _e("Please select a form", "gravityforms") ?>");
						jQuery('#add_directory_id').focus();
						return false;
					}
				
			<?php 
					$js = kws_gf_make_popup_options(true); 
					#print_r($js);
					$ids = $idOutputList = $setvalues = $vars = '';

					foreach($js as $j) {
						$vars .= $j['js'] ."
						";
						$ids .= $j['idcode'] . " ";
						$setvalues .= $j['setvalue']."
						";
						$idOutputList .= $j['id'].'Output' .' + ';
					}
					echo $vars;
					echo $setvalues;
			?>

				var win = window.dialogArguments || opener || parent || top;
				var shortcode = "[directory form=\"" + directory_id +"\"" + <?php echo addslashes($idOutputList); ?>"]";
				win.send_to_editor(shortcode);
				return false;
			}
		});
			
		</script>

	<div id="select_gf_directory" style="overflow-x:hidden; overflow-y:auto;display:none;">
		<form action="#" method="get" id="select_gf_directory_form">
			<div class="wrap">
				<div>
					<div style="padding:15px 15px 0 15px;">
						<h3 style="color:#5A5A5A!important; font-family:Georgia,Times New Roman,Times,serif!important; font-size:1.8em!important; font-weight:normal!important;"><?php _e("Insert A Directory", "gravityforms"); ?></h3>
						<span>
							<?php _e("Select a form below to add it to your post or page.", "gravityforms"); ?>
						</span>
					</div>
					<div style="padding:15px 15px 0 15px;">
						<select id="add_directory_id">
							<option value="">  <?php _e("Select a Form", "gravityforms"); ?>  </option>
							<?php
								$forms = RGFormsModel::get_forms(1, "title");
								foreach($forms as $form){
									?>
									<option value="<?php echo absint($form->id) ?>"><?php echo esc_html($form->title) ?></option>
									<?php
								}
							?>
						</select> <br/>
						<div style="padding:8px 0 0 0; font-size:11px; font-style:italic; color:#5A5A5A"><?php _e("This form will be the basis of your directory.", "gravityforms"); ?></div>
					</div>
						<?php 
						
						kws_gf_make_popup_options(); 
						
						?>
					<div class="submit">
						<input type="submit" class="button-primary" style="margin-right:15px;" value="Insert Directory" id="insert_gf_directory" />
						<a class="button button-secondary" style="color:#bbb;" href="#" onclick="tb_remove(); return false;"><?php _e("Cancel", "gravityforms"); ?></a>
					</div>
				</div>
			</div>
		</form>
	</div>
		<?php
}

function kws_gf_make_field($type, $id, $default, $label) {
	$label = str_replace('&lt;code&gt;', '<code>', str_replace('&lt;/code&gt;', '</code>', $label));
	$output = '<li style="width:90%; clear:left; border-bottom: 1px solid #cfcfcf; padding:.25em .25em .4em; margin-bottom:.25em;">';
	$default = maybe_unserialize($default);
	if($type == "checkbox") { 
			if($default) { $default = ' checked="checked"';}
			$output .= '<label for="gf_settings_'.$id.'"><input type="checkbox" id="gf_settings_'.$id.'"'.$default.' /> '.$label.'</label>'."\n";	
	} elseif($type == "text") {
			$output .= '<input type="text" id="gf_settings_'.$id.'" value="'.htmlspecialchars($default).'" style="width:40%;" /> <label for="gf_settings_'.$id.'" style="font-size:11px; font-style:italic; color:#5A5A5A;'; if(strlen($label) > 100) { $output .= 'display:block;padding:8px 0 0 0;';} else { $output .= 'padding:8px 0 0 8px;';} 
			$output .= '">'.$label.'</label>'."\n";
	} elseif($type == 'radio') {
		if(is_array($default)) {
			foreach($default as $opt) {
				if(!empty($opt['default'])) { $checked = ' checked="checked"'; } else { $checked = ''; }
				$id_opt = $id.'_'.sanitize_title($opt['value']);
				$output .= '<label for="gf_settings_'.$id_opt.'"><input type="radio"'.$checked.' value="'.$opt['value'].'" id="gf_settings_'.$id_opt.'" name="'.$id.'" /> '.$opt['label'].'</label>'."\n";	
			}
		}
	} elseif($type == 'select') {
		if(is_array($default)) {
			$output .= '
			<label for="'.$id.'">
			<select name="'.$id.'" id="'.$id.'">';
			foreach($default as $opt) {
				if(!empty($opt['default'])) { $checked = ' selected="selected"'; } else { $checked = ''; }
				$id_opt = $id.'_'.sanitize_title($opt['value']);
				$output .= '<option'.$checked.' value="'.$opt['value'].'"> '.$opt['label'].'</option>'."\n";	
			}
			$output .= '</select>
			'.$label.'</label>
			';
		} else {
			$output = '';
		}
	}
	if(!empty($output)) {
		$output .= '</li>'."\n";
		echo $output;
	}
}

function kws_gf_make_popup_js($type, $id) {
	$defaults = kws_gf_directory_defaults();
	
	foreach($defaults as $key => $default) {
		if($default === true) {
			$defaults[$key] = 'true';
		} elseif($default === false) {
			$defaults[$key] = 'false';
		}
	}
	
	if($type == "checkbox") {
		$js = 'var '.$id.' = jQuery("#gf_settings_'.$id.'").is(":checked") ? "true" : "false";';
	} elseif($type == "text") {
		$js = 'var '.$id.' = jQuery("#gf_settings_'.$id.'").val();';
	} elseif($type == 'radio') {
		$js = '
		if(jQuery("input[name=\''.$id.'\']:checked").length > 0) { 
			var '.$id.' = jQuery("input[name=\''.$id.'\']:checked").val();
		} else {
			var '.$id.' = jQuery("input[name=\''.$id.'\']").eq(0).val();
		}';
	} elseif($type == 'select') {
		$js = '
		if(jQuery("select[name=\''.$id.'\']:selected").length > 0) { 
			var '.$id.' = jQuery("input[name=\''.$id.'\']:selected").val();
		} else {
			var '.$id.' = jQuery("select[name=\''.$id.'\']").eq(0).val();
		}';
	}
	$idCode = $id.'=\""+'.$id.'+"\"';
	

	$set = 'var '.$id.'Output = (jQuery.trim('.$id.') == "'.trim(addslashes($defaults["{$id}"])).'") ? "" : " '.$idCode.'";';
	
	$idCode = $id.'=\""+'.$id.'+"\"';
	
	return array('js'=>$js, 'id' => $id, 'idcode'=>$idCode, 'setvalue' => $set);
}

function kws_gf_make_popup_options($js = false) {
	$i = 0;
	$standard = array(
			array('text','page_size' , 20, "Number of entries to show at once"),
			array('select','directoryview' , array(array('value'=>'table', 'label' => 'Table'), array('value'=>'ul', 'label'=>'Unordered List'), array('value' => 'dl', 'label' => 'Definition List')), "Format for directory listings (directory view)"),
			array('select','entryview' , array(array('value'=>'table', 'label' => 'Table'), array('value'=>'ul', 'label'=>'Unordered List'), array('value' => 'dl', 'label' => 'Definition List')), "Format for single entries (single entry view)"),
			array('checkbox', 'search' , true, "Show the search field"),
		   );
	if(!$js) {
		echo '<ul style="padding:15px 15px 0 15px; width:100%;">';
		foreach($standard as $o) {
			kws_gf_make_field($o[0], $o[1], maybe_serialize($o[2]), esc_html($o[3]));
		}
		echo '</ul>';
	} else {
		foreach($standard as $o) {
			$out[$i] = kws_gf_make_popup_js($o[0], $o[1]);
			$i++;
		}
	}
		
		$advanced = array(	  
				 array('checkbox', 'approved' , false, "Show only entries that have been Approved (have a field in the form that is an Admin-only checkbox with a value of 'Approved')"),
				 array('checkbox', 'showadminonly', false, "Show admin only columns (the Approved column can always be shown, if desired.)"),
				array('checkbox', 'wpautop' , true, "Convert bulk paragraph text to...paragraphs"),
				array('checkbox', 'lightbox' , true, "Do you want your image uploads to be lightboxed?"),
				array('radio'	, 'postimage' , array(array('label' =>'<img src="'.GFCommon::get_base_url().'/images/doctypes/icon_image.gif" /> Show image icon', 'value'=>'icon', 'default'=>'1'), array('label' =>'Show full image', 'value'=>'image')), "How do you want images to appear in the directory?"),
				array('checkbox', 'showcount' , true, "Do you want to show 'Displaying 1-19 of 19'?"),
				array('checkbox', 'pagelinksshowall' , true, "Whether to show each page number, or just 7"),
				array('checkbox', 'showrowids' , true, "Whether or not to show the row ids, which are the entry IDs."),
				array('checkbox', 'fulltext' , true, "If there's a textarea or post content field, show the full content?"),
				array('checkbox', 'linkemail' , true, "Convert email fields to email mailto: links"),
				array('checkbox', 'linkwebsite' , true, "Convert URLs to links"),
				array('checkbox', 'linknewwindow' , false, "Open links in new window? (uses target='_blank')"),
				array('checkbox', 'nofollowlinks' , false, "Add nofollow to all links, including emails"),
				array('checkbox', 'icon' , false, "Show the GF icon as it does in admin?"),
				array('checkbox', 'titleshow' , true, "Show a form title? By default, the title will be the form title."),
				array('checkbox', 'searchtabindex' , false, "Adds tabindex='' to the search field"),
				array('checkbox', 'tfoot' , true, "Show the <tfoot>"),
				array('checkbox', 'thead' , true, "Show the <thead>"),
				array('checkbox', 'dateformat', false, "Override the options from Gravity Forms, and use standard PHP date formats"),
				array('checkbox', 'truncatelink', false, "Show more simple links for URLs (strip <code>http://</code>, <code>www.</code>, etc.)"),	#'truncatelink' => false,
				array('checkbox', 'appendaddress' , false, "Add the formatted address as a column at the end of the table"),
				array('checkbox', 'hideaddresspieces' , false, "Hide the pieces that make up an address (Street, City, State, ZIP, Country, etc.)")
		);
		$entry = array(
			array('checkbox', 'entry', true, "If there's a displayed Entry ID column, add link to each full entry"),
			array('checkbox', 'entrylightbox', false, "Open entry details in lightbox (defaults to lightbox settings)"),
			array('text', 'entrytitle', 'Entry Detail', "Title of entry lightbox window"),
			array('text', 'entrylink', 'View entry details', "Link text to show full entry"),
			array('text', 'entryth', 'More Info', "Entry ID column title"),
			array('text', 'entryback', '&larr; Back to directory',),
			array('checkbox', 'entryonly', true, "When viewing full entry, show entry only? Otherwise, show entry with directory below"),
			array('checkbox', 'entryanchor', true, "When returning to directory view from single entry, link to specific anchor row?"),
		);
	
	if(!$js) {
		echo '<a href="#kws_gf_advanced_settings" class="kws_gf_advanced_settings">Show advanced settings</a>';
		echo '<div style="display:none;" id="kws_gf_advanced_settings">';
		echo "<fieldset><legend style='margin:0; padding:0; font-weight:bold; font-size:1.5em; margin-top:1em; padding:.5em 0;'>Advanced Settings</legend>";
		echo "<h3 style='padding-top:1em; margin:0;'>Single Entry View</h3>";
		echo '<p class="howto">These settings control whether users can view each entry as a separate page or lightbox. Single entries will show all data associated with that entry.</p>';
		echo '<ul style="padding:0 15px 0 15px; width:100%;">';
		foreach($entry as $o) {
			if(isset($o[3])) { $o3 = esc_html($o[3]); } else { $o3 = '';}
			kws_gf_make_field($o[0], $o[1], maybe_serialize($o[2]), $o3);
		}
		echo '</ul>';
		echo '<hr style="margin-top:1em; display:block; border:none; outline:none; border-bottom:1px solid #ccc;" />';
		echo "<h3 style='padding-top:1em; margin:0;'>Checkboxes Galore</h3>";
		echo '<ul style="padding: 0 15px 0 15px; width:100%;">'; 		 
		foreach($advanced as $o) {
			kws_gf_make_field($o[0], $o[1], maybe_serialize($o[2]), esc_html($o[3]));
		}
		echo '</ul>';
		echo '<hr style="margin-top:1em; display:block; border:none; outline:none; border-bottom:1px solid #ccc;" />';
		echo "<h3 style='padding-top:1em; margin:0;'>Text Inputs Galore</h3>";
		echo '<ul style="padding: 0 15px 0 15px; width:100%;">';
	} else {
		foreach($entry as $o) {
			$out[$i] = kws_gf_make_popup_js($o[0], $o[1]);
			$i++;
		}
		foreach($advanced as $o) {
			$out[$i] = kws_gf_make_popup_js($o[0], $o[1]);
			$i++;
		}
	}
		$advanced = array(
				 array('text','tableclass', 'gf_directory widefat fixed', "Class for the <table>"),
				array('text','tablestyle', '', "inline CSS for the <table>"),
				array('text','rowclass', '', "Class for the <table>"),
				array('text','rowstyle', '', "Inline CSS for all <tbody><tr>'s"),
				array('text','valign', 'baseline',"Vertical align for table cells"),
				array('text','sort', 'date_created', "Use the input ID ( example: 1.3 or 7 or ip )"),
				array('text','dir', 'DESC',"Sort in ascending order (<code>ASC</code>) or descending (<code>DESC</code>)"),
				array('text','startpage' , 1, "If you want to show page 8 instead of 1"),
				array('text','pagelinkstype' , 'plain', "Type of pagination links. <code>plain</code> is just a string with the links separated by a newline character. The other possible values are either <code>array</code> or <code>list</code>."),
				array('text','titleprefix' , 'Entries for ', "Default GF behavior is 'Entries : '"),
				array('text','tablewidth' , '100%', "Set the 'width' attribute for the table"),
				array('text','datecreatedformat' , get_option('date_format').' \a\t '.get_option('time_format'), "Use <a href='http://php.net/manual/en/function.date.php' target='_blank'>standard PHP date formats</a>")
		);
	if(!$js) { 		  
		foreach($advanced as $o) {
			kws_gf_make_field($o[0], $o[1], maybe_serialize($o[2]), esc_html($o[3]));
		}
		echo '</ul></fieldset></div>';
	} else {
		foreach($advanced as $o) {
			$out[$i] = kws_gf_make_popup_js($o[0], $o[1]);
			$i++;
		}
		return $out;
	}
}
	
//Adding "embed form" button
add_action('media_buttons_context', 'kws_gf_add_form_button', 999);

if(in_array(RG_CURRENT_PAGE, array('post.php', 'page.php', 'page-new.php', 'post-new.php'))){
	add_action('admin_footer',	'kws_gf_add_mce_popup');
}

add_filter('get_shortlink', 'kws_gf_directory_shortlink');
function kws_gf_directory_shortlink($link) {
	global $post;
	if(apply_filters('kws_gf_directory_shortlink', true)) {
		$url = add_query_arg(array());
		if(preg_match('/entry\/([0-9]+)-([0-9]+)\/?/ism',$url, $matches)) {
			return add_query_arg(array('leadid'=>$matches[2], 'form'=>$matches[1]), $link);
		} elseif(isset($_REQUEST['leadid']) && isset($_REQUEST['form'])) {
			if(empty($link)) {
				$link = $post->guid.'?p='.$post->ID;
			}
			return add_query_arg(array('leadid'=>$_REQUEST['leadid'], 'form'=>$_REQUEST['form']), $link);
		}
		return $link;
	}
}

add_action('wp_head', 'kws_gf_directory_canonical_add', 1);
function kws_gf_directory_canonical_add() {
	if(apply_filters('kws_gf_directory_canonical_add', true)) {
		add_filter('post_link', 'kws_gf_directory_canonical', 1, 3);
		add_filter('page_link', 'kws_gf_directory_canonical', 1, 3);
		function kws_gf_directory_canonical($permalink, $sentPost = '', $leavename = '') {
			global $post; $post->permalink = $permalink; $url = add_query_arg(array());
			$sentPostID = is_object($sentPost) ? $sentPost->ID : $sentPost;
			// $post->ID === $sentPostID is so that add_query_arg match doesn't apply to prev/next posts; just current
			if($post->ID === $sentPostID && preg_match('/(entry\/[0-9]+-[0-9]+\/?)/ism',$url, $matches)) {
				return trailingslashit($permalink).$matches[0];
			} elseif($post->ID === $sentPostID && isset($_REQUEST['leadid']) && isset($_REQUEST['form'])) {
				return add_query_arg(array('leadid' =>$_REQUEST['leadid'], 'form'=>$_REQUEST['form']), trailingslashit($permalink));
			}
			return $permalink;
		}
	}
}

function kws_gf_process_lead_detail($inline = true, $entryback = '', $showadminonly = false, $adminonlycolumns = array(), $approvedcolumn = null, $options = array()) {
	global $wp,$post,$wp_rewrite,$wpdb;
	$formid = $leadid = false;
	
	if(isset($wp->query_vars['entry'])) {
		list($formid, $leadid) = explode('-', $wp->query_vars['entry']);
	}

	$formid = isset($_REQUEST['form']) ? (int)$_REQUEST['form'] : $formid;
	$leadid = isset($_REQUEST['leadid']) ? (int)$_REQUEST['leadid'] : $leadid;

	if($leadid && $formid) {
		if(!class_exists('GFEntryDetail')) { require_once(GFCommon::get_base_path() . "/entry_detail.php"); }
		if(!class_exists('GFCommon')) { require_once(WP_PLUGIN_DIR . "/gravityforms/common.php"); }
		if(!class_exists('RGFormsModel')) { require_once(WP_PLUGIN_DIR . "/gravityforms/forms_model.php"); }
		
		$form = RGFormsModel::get_form_meta((int)$formid);
		$lead = RGFormsModel::get_lead((int)$leadid);
		
		if(empty($approvedcolumn)) { $approvedcolumn = kws_gf_get_approved_column($form); }
		if(empty($adminonlycolumns) && !$showadminonly) { $adminonlycolumns = kws_gf_get_admin_only($form); }
		
		if(!$showadminonly)  {
			$lead = kws_gf_remove_admin_only(array($lead), $adminonlycolumns, $approvedcolumn, true, true);
			$lead = $lead[0];
		}
		
		if(!$showadminonly)  {
			$form['fields'] = kws_gf_remove_admin_only($form['fields'], $adminonlycolumns, $approvedcolumn, false, true);
		}
				
		ob_start(); // Using ob_start() allows us to filter output
			@kws_gf_lead_detail($form, $lead, false, $inline);
			$content = ob_get_contents(); // Get the output
		ob_end_clean(); // Clear the cache
		
		$current = remove_query_arg(array('row', 'page_id', 'leadid', 'form'));
		$url = parse_url(add_query_arg(array(), $current));
		if(function_exists('is_multisite') && is_multisite() && $wpdb->blogid != 1) {
			$href = $current;
		} else {
			$href = isset($post->permalink) ? $post->permalink : get_permalink();
		}
		if(!empty($url['query'])) { $href .= '?'.$url['query']; }
		if(isset($_REQUEST['row'])) { $href .= '#lead_row_'.$leadid; }
		
		// If there's a back link, format it
		if(!empty($entryback)) {
			$link = apply_filters('kws_gf_directory_backlink', '<p class="entryback"><a href="'.$href.'">'.esc_html($entryback).'</a></p>');
		} else { 
			$link = ''; 
		}
		
		$content = $link . $content; 
		$content = apply_filters('kws_gf_directory_detail', apply_filters('kws_gf_directory_detail_'.(int)$leadid, $content, true), true);
		
		
		if(isset($options['entryview'])) {
			$content = kws_gf_pseudo_filter($content, $options['entryview'], true);
		}
		
		return $content;
	} else {
		return false;
	}
}

function kws_gf_lead_detail($form, $lead, $allow_display_empty_fields=false, $inline = true) {
		$display_empty_fields = ''; $allow_display_empty_fields = true;
		if($allow_display_empty_fields){
			$display_empty_fields = $_COOKIE["gf_display_empty_fields"];
		}
		?>
		<table cellspacing="0" class="widefat fixed entry-detail-view">
		<?php if($inline) { ?>
			<thead>
				<tr>
					<th id="details" colspan="2" scope="col">
					<?php 
						$title = $form["title"] .' : Entry # '.$lead["id"]; 
						$title = apply_filters('kws_gf_directory_detail_title', apply_filters('kws_gf_directory_detail_title_'.(int)$lead['id'], array($title, $lead), true), true);
						if(is_array($title)) {
							echo $title[0];
						} else {
							echo $title;
						}					  	
					?>
					</th>
				</tr>
			</thead>
			<?php
			}
			?>
			<tbody>
				<?php
				$count = 0;
				$field_count = sizeof($form["fields"]);
				foreach($form["fields"] as $field){
					$count++;
					$is_last = $count >= $field_count ? true : false;

					switch(RGFormsModel::get_input_type($field)){
						case "section" :
							?>
							<tr>
								<td colspan="2" class="entry-view-section-break<?php echo $is_last ? " lastrow" : ""?>"><?php echo esc_html(GFCommon::get_label($field))?></td>
							</tr>
							<?php
						break;

						case "captcha":
						case "html":
							//ignore captcha field
						break;

						default :
							$value = RGFormsModel::get_lead_field_value($lead, $field);
							$display_value = GFCommon::get_lead_field_display($field, $value);
							if($display_empty_fields || !empty($display_value) || $display_value === "0"){
								?>
								<tr>
									<th scope="row" class="entry-view-field-name"><?php echo esc_html(GFCommon::get_label($field))?></th>
									<td class="entry-view-field-value<?php echo $is_last ? " lastrow" : ""?>"><?php echo empty($display_value) && $display_value !== "0" ? "&nbsp;" : $display_value ?></td>
								</tr>
								<?php
							}
						break;
					}
				}
				?>
			</tbody>
		</table>
		<?php
}


?>