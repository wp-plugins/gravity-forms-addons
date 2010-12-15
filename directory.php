<?php 

add_shortcode('directory', 'kws_gf_directory');

function kws_gf_directory($atts) { 
	global $wpdb,$wp_rewrite;

    //quit if version of wp is not supported
    if(!GFCommon::ensure_wp_version())
        return;

    ob_start(); // Using ob_start() allows us to use echo instead of $output .=
    
	foreach($atts as $key => $att) { 
		if(strtolower($att) == 'false') { $atts[$key] = false; }
		if(strtolower($att) == 'true') { $atts[$key] = true; }
	}
	extract( shortcode_atts( array(
		  'form' => 1, // Gravity Forms form ID
		  'approved' => false, // Show only entries that have been Approved (have a field in the form that is an Admin-only checkbox with a value of 'Approved' 
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
	      'postimage' => 'image', // Whether to show icon, thumbnail, or large image
	      ), $atts ) );
		
		$form_id = $form;
        $sort_field = empty($_GET["sort"]) ? $sort : $_GET["sort"];
        $sort_direction = empty($_GET["dir"]) ? $dir : $_GET["dir"];
        $search_query = isset($_GET["gf_search"]) ? $_GET["gf_search"] : null;
        $page_index = empty($_GET["page"]) ? $startpage -1 : intval($_GET["page"]) - 1;
        $star = (isset($_GET["star"]) && is_numeric($_GET["star"])) ? intval($_GET["star"]) : null;
        $read = (isset($_GET["read"]) && is_numeric($_GET["read"])) ? intval($_GET["read"]) : null;
        $first_item_index = $page_index * $page_size;

        $form = RGFormsModel::get_form_meta($form_id);
        
        if($titleshow === true) { $title = $form["title"]; }
        
        $sort_field_meta = RGFormsModel::get_field($form, $sort_field);
        $is_numeric = $sort_field_meta["type"] == "number";

		$columns = RGFormsModel::get_grid_columns($form_id, true);
		$approvedcolumn = kws_gf_get_approved_column($form);
		$adminonlycolumns = kws_gf_get_admin_only($form);

        $leads = RGFormsModel::get_leads($form_id, $sort_field, $sort_direction, $search_query, $first_item_index, $page_size, $star, $read, $is_numeric);
        
        if(!$showadminonly)  { 
        	$leads = kws_gf_remove_admin_only($leads, $adminonlycolumns, $approvedcolumn, true); 
        	$columns = kws_gf_remove_admin_only($columns, $adminonlycolumns, $approvedcolumn, false); 
        }
        
        $lead_count = kws_gf_get_lead_count($form_id, $search_query, $star, $read, $approvedcolumn, $approved);

        
        
		// Get a list of query args for the pagination links
        if(!empty($search_query)) { $args["gf_search"] = urlencode($search_query); }
        if(!empty($sort_field)) { $args["sort"] = $sort_field; }
        if(!empty($sort_direction)) { $args["dir"] = $sort_direction; }
        if(!empty($star)) { $args["star"] = $star; }
		$args['id'] = $form_id;
		
        $page_links = array(
            'base' =>  get_permalink().'?%_%',
            'format' => (sizeof($_GET) >= 1) ? '&page=%#%' : '?page=%#%',
            'add_args' => $args,
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => ceil($lead_count / $page_size),
            'current' => $page_index + 1,
            'show_all' => $pagelinksshowall
        );

		$page_links = paginate_links($page_links);
		
		if($lightbox) {
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
				var page = '<?php if($wp_rewrite->using_permalinks()) { echo '?'; } else { echo '&'; } ?>page='+<?php echo intval($_GET['page']); ?>;
                var location = "<?php echo get_permalink(); ?>"+page+search+sort+dir;
                document.location = location;
            }

		<?php if($search) { ?>
            jQuery(document).ready(function(){
                jQuery("#lead_form").submit(function(event){
                	event.preventDefault();
                	Search(jQuery('#lead_search').val());
                	return false;
                });
                jQuery("#lead_form").attr('action', "<?php $link_params['page'] = $page_index; echo remove_query_arg(array('gf_search','sort','dir'), add_query_arg($link_params)); ?>");

            });
		<?php } ?>
        </script>
        <link rel="stylesheet" href="<?php echo GFCommon::get_base_url() ?>/css/admin.css" type="text/css" />

        <div class="wrap">
            <?php if($icon) { ?><img alt="<?php _e("Gravity Forms", "gravityforms") ?>" src="<?php echo GFCommon::get_base_url()?>/images/gravity-title-icon-32.png" style="float:left; margin:15px 7px 0 0;"/><?php } ?>
            <?php if($titleshow) { ?><h2><?php echo $titleprefix.$title; ?> </h2><?php } ?>
			<?php if($search) { ?>
            <form id="lead_form" method="get">
                <p class="search-box">
                    <label class="hidden" for="lead_search"><?php _e("Search Entries:", "gravityforms"); ?></label>
                    <input type="text" id="lead_search" value="<?php echo $search_query ?>"<?php if($searchtabindex) { echo ' tabindex="'.intval($searchtabindex).'"';}?> /><input type="submit" class="button" id="lead_search_button" value="<?php _e("Search", "gravityforms") ?>"<?php if($searchtabindex) { echo ' tabindex="'.intval($searchtabindex++).'"';}?> />
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

                <table class="<?php echo $class; ?>" cellspacing="0"<?php if(!empty($tablewidth)) { echo ' width="'.$tablewidth.'"'; } echo $tablestyle ? ' style="'.$tablestyle.'"' : ''; ?>>
                <?php if($thead) {?>
                <thead>
                    <tr>
                        <?php
                        foreach($columns as $field_id => $field_info){
                            $dir = $field_id == 0 ? "DESC" : "ASC"; //default every field so ascending sorting except date_created (id=0)
                            if($field_id == $sort_field) { //reverting direction if clicking on the currently sorted field
                                $dir = $sort_direction == "ASC" ? "DESC" : "ASC";
                            }
                            if(is_array($adminonlycolumns) && !in_array($field_id, $adminonlycolumns) || (is_array($adminonlycolumns) && in_array($field_id, $adminonlycolumns) && $showadminonly) || !$showadminonly) {
                            ?>
                            <th scope="col" class="manage-column" onclick="Search('<?php echo $search_query ?>', '<?php echo $field_id ?>', '<?php echo $dir ?>');" style="cursor:pointer;"><?php 
                            $label = $field_info["label"];
                            
                            $label = apply_filters('kws_gf_directory_th', apply_filters('kws_gf_directory_th_'.$field_id, apply_filters('kws_gf_directory_th_'.sanitize_title($label), $label)));
                            echo esc_html($label) 
                           
                             ?></th>
                            <?php
                            }
                        }
                        ?>
                    </tr>
                </thead>
                <?php } ?>
                <tbody class="list:user user-list">
                    <?php
                    if(sizeof($leads) > 0 && $lead_count > 0){
                        $field_ids = array_keys($columns);
						
						foreach($leads as $lead){
                            echo "\n\t\t\t\t\t\t";
                            
                            $leadapproved = false; if($approved) { $leadapproved = kws_gf_check_approval($lead, $approvedcolumn); }
                            
                            
#                            if(in_array(key() $adminonlycolumns )
                            
                            if($leadapproved && $approved || !$approved) {
                            	$target = ''; if($linknewwindow) { $target = ' target="_blank"'; }
                                $valignattr = ''; if($valign) { $valignattr = ' valign="'.$valign.'"'; } 
                                $lightboxclass = ''; if($lightbox) { $lightboxclass = '  class="thickbox"'; }
                                $nofollow = ''; if($nofollowlinks) { $nofollow = ' rel="nofollow"'; }
                                    
                            ?><tr<?php if($showrowids){ ?> id="lead_row_<?php echo $lead["id"] ?>" <?php } ?>class='<?php echo $rowclass; echo $lead["is_starred"] ? " featured" : "" ?>'<?php echo $rowstyle ? ' style="'.$rowstyle.'"' : ''; echo $valignattr; ?>><?php
                                $class = "";
                                $is_first_column = true;
                                foreach($field_ids as $field_id){
                                    $value = isset($lead[$field_id]) ? $lead[$field_id] : '';
                                    $input_type = !empty($columns[$field_id]["inputType"]) ? $columns[$field_id]["inputType"] : $columns[$field_id]["type"];
                                    switch($input_type){
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
                                                 	'code' => "<img src='$src' {$size[3]} alt='' />"
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
												$value = get_gf_field_value_long($lead['id'], $field_id);
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

                                        default:
                                        	$class = '';
                                        	$input_type = 'text';
                                        	if(is_email($value) && $linkemail) {$value = "<a href='mailto:$value'$nofollow>$value</a>"; } 
                                        	elseif(preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $value) && $linkwebsite) {
                                        		if($lightbox) { $href = $value .'?KeepThis=true&TB_iframe=true&height=400&width=600'; $class = ' class="thickbox lightbox"'; }
                                        		$value = "<a href='$href'{$nofollow}{$target}{$class}>$value</a>"; 
                                        	}
                                        	else { $value = esc_html($value); }
                                    }
									if($is_first_column) { echo "\n"; }
                                	echo "\t\t\t\t\t\t\t"; ?><td<?php echo empty($class) ? '' : ' class="'.$input_type.' '.$class.'"'; echo $valignattr; ?>><?php 
                                	
                                	$value = empty($value) ? '&nbsp;' : $value;
                                	$value = apply_filters('kws_gf_directory_value', apply_filters('kws_gf_directory_value_'.$input_type, apply_filters('kws_gf_directory_value_'.$field_id, $value)));
                                	echo $value;
                                	
                                	?></td><?php
                                	echo "\n";
                                	$is_first_column = false;
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
                <tfoot><?php echo "\n\t\t\t"; ?><tr><?php
                        foreach($columns as $field_id => $field_info){
                            $dir = $field_id == 0 ? "DESC" : "ASC"; //default every field so ascending sorting except date_created (id=0)
                            if($field_id == $sort_field) //reverting direction if clicking on the currently sorted field
                                $dir = $sort_direction == "ASC" ? "DESC" : "ASC";
                            echo "\n\t\t\t\t";
                            ?><th scope="col" class="manage-column" onclick="Search('<?php echo $field_id ?>', '<?php echo $dir ?>', <?php echo $form_id ?>, '<?php echo $search_query ?>', '<?php echo $star ?>', '<?php echo $read ?>');" style="cursor:pointer;"><?php echo esc_html($field_info["label"]) ?></th><?php

                        }
                        echo "\n\t\t\t";
                        ?></tr>
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
		$content = apply_filters('kws_gf_directory_output', apply_filters('kws_gf_directory_output_'.$form_id, $content));
		
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
  
  	function kws_gf_get_lead_count($form_id, $search, $star=null, $read=null, $column, $approved = false){
        global $wpdb;

        if(!is_numeric($form_id))
            return "";

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
        	$sql .= 'AND ld.value ="Approved" AND round(ld.field_number,1)='.$column;
        }
        
        return $wpdb->get_var($sql);
    }
    
    function kws_gf_check_approval($lead, $column) {
		if(strtolower($lead[$column]) == 'approved') {
			return true;
		}
		return false;
	}
	
	function kws_gf_remove_admin_only($leads, $adminOnly, $approved, $isleads) {
		$i = 0;
		if($isleads) {
			if(is_array($leads)) {
				foreach($leads as $key => $lead) {
					if(is_array($adminOnly)) {
						if(is_array($adminOnly) && @in_array($key, $adminOnly) && $key != $approved && $key != floor($approved)) {
							unset($leads[$i]);
						}
					}
				}
			}
		} else {
			if(is_array($leads)) {
				foreach($leads as $key => $lead) {
					if(is_array($adminOnly)) {
						if(is_array($adminOnly) && @in_array($key, $adminOnly) && $key != $approved && $key != floor($approved)) {
							unset($leads[$key]);
						}
					}
				}
			}
		}
		return $leads;
	}
	
	function kws_gf_get_approved_column($form) {
		foreach($form['fields'] as $key=>$col) {
			if(strtolower($col['label']) == 'approved' && $col['type'] == 'checkbox') {
				return $key;
			}
		}
		
		foreach($form['fields'] as $key=>$col) {
			if(is_array($col['inputs'])) {
				foreach($col['inputs'] as $key2=>$input) {
					if(strtolower($input['label']) == 'approved' && $col['type'] == 'checkbox' && !empty($col['adminOnly'])) {
						return $input['id'];
					}
				}
			}
		}

		return false;
	}
	
	function kws_gf_get_admin_only($form) {
		foreach($form['fields'] as $key=>$col) {
			if(!empty($col['adminOnly'])) {
				$adminOnly[] = $col['id'];
			}
			if(is_array($col['inputs'])) { 
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
        $out = '<a href="#" class="select_gf_directory" title="' . __("Add a Gravity Forms Directory", 'gravityforms') . '"><img src="'.$image_btn.'" alt="' . __("Add Gravity Form", 'gravityform') . '" /></a>';
        return $context . $out;
    }

    //Action target that displays the popup to insert a form to a post/page
    function kws_gf_add_mce_popup(){
        ?>
        <script type="text/javascript">
        	jQuery('document').ready(function($) { 
        		$('#insert_gf_directory').click(function(e) {
        			e.preventDefault();
        			InsertGFDirectory();
        			return false;
        		});
        		
        		$('a.select_gf_directory').click(function(e) { e.preventDefault(); 
        			$.modal($('#select_gf_directory'), {
//							closeHTML:"<a href='#'>Close</a>",
							minHeight:400,
							minWidth: 640,
							opacity:80,
							overlayCss: {backgroundColor:"#000"},
							containerCss:{
								backgroundColor: "#fff",
								borderColor: '#000',
								borderWidth: 5,
								padding:10,
								escClose: true,
								minWidth:640,
								maxWidth:800,
								minHeight:500,
							},
							overlayClose:true,
							onShow: function(dlg) {
								var iframeHeight = $('#select_gf_directory', $(dlg.container)).height();
								var containerHeight = $(dlg.container).height();
								var iframeWidth = $('#select_gf_directory', $(dlg.container)).width();
								var containerWidth = $(dlg.container).width();
								
								if(containerHeight < iframeHeight) { $(dlg.container).height(iframeHeight); }
								else { $('#select_gf_directory', $(dlg.container)).height(containerHeight); }
								
								if(containerWidth < iframeWidth) { $(dlg.container).width(iframeWidth); }
								else { $('#select_gf_directory', $(dlg.container)).width(containerWidth); }
							}
						});
        			return false; 
        		});
        		$('a.kws_gf_advanced_settings').click(function(e) {  e.preventDefault(); $('#kws_gf_advanced_settings').toggle(); return false; });
        	});
            function InsertGFDirectory(){
                var directory_id = jQuery("#add_directory_id").val();
                if(directory_id == ""){
                    alert("<?php _e("Please select a form", "gravityforms") ?>");
                    jQuery('#add_directory_id').focus();
                    return false;
                }
                
            <?php 
					$js = kws_gf_make_popup_options(true); 
					$vars = '';
					$ids = '';
					foreach($js as $j) {
						$vars .= $j['js'] ."
						";
						$ids .= $j['id'] . " ";
					}
            		echo $vars;
            ?>

                var form_name = jQuery("#add_directory_id option[value='" + directory_id + "']").text().replace(" ", "");

                var win = window.dialogArguments || opener || parent || top;

				win.send_to_editor("[directory form=" + directory_id + " <?php echo $ids; ?>]");
				jQuery.modal.close();
            }
        </script>

        <div id="select_gf_directory" style="overflow-x:hidden; overflow-y:auto;display:none;">
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
					<div style="padding:15px;">
                        <input type="button" class="button-primary" value="Insert Directory" id="insert_gf_directory" />&nbsp;&nbsp;&nbsp;
                    <a class="button" style="color:#bbb;" href="#" onclick="tb_remove(); return false;"><?php _e("Cancel", "gravityforms"); ?></a>
                    </div>
                </div>
            </div>
        </div>

        <?php
}

function kws_gf_make_field($type, $id, $default, $label) {
	
	$output = '<li style="width:90%; clear:left; border-bottom: 1px solid #cfcfcf; padding:.25em .25em .4em; margin-bottom:.25em;">';
	if($type == "checkbox") { 
			if($default) { $default = ' checked="checked"';}
			$output .= '<label for="'.$id.'"><input type="checkbox" id="'.$id.'"'.$default.' /> '.$label.'</label>'."\n";	
	} elseif($type == "text") {
			$output .= '<input type="text" id="'.$id.'" value="'.$default.'" style="width:40%;" /> <label for="'.$id.'" style="font-size:11px; font-style:italic; color:#5A5A5A;'; if(strlen($label) > 100) { $output .= 'display:block;padding:8px 0 0 0;';} else { $output .= 'padding:8px 0 0 8px;';} 
			$output .= '">'.$label.'</label>'."\n";
	} elseif($type == 'radio') {
		if(is_array($default)) {
			foreach($default as $opt) {
				if(!empty($opt['default'])) { $checked = ' checked="checked"'; } else { $checked = ''; }
				$id_opt = $id.'_'.sanitize_title($opt['value']);
				$output .= '<label for="'.$id_opt.'"><input type="radio"'.$checked.' value="'.$opt['value'].'" id="'.$id_opt.'" name="'.$id.'" /> '.$opt['label'].'</label>'."\n";	
			}
		}
	}
	$output .= '</li>'."\n";
	echo $output;
}

function kws_gf_make_popup_js($type, $id) {
	if($type == "checkbox") {
		$js = 'var '.$id.' = jQuery("#'.$id.'").is(":checked") ? "true" : "false";';
	} elseif($type == "text") {
		$js = 'var '.$id.' = jQuery("#'.$id.'").val();';
	} elseif($type == 'radio') {
		$js = 'var '.$id.' = jQuery("#'.$id.':checked").val();';
	}
	$id = $id.'=\""+'.$id.'+"\"';
	return array('js'=>$js, 'id'=>$id);
}

function kws_gf_make_popup_options($js = false) {
	$i = 0;
	$standard = array(
		  array('text','page_size' , 20, "Number of entries to show at once"),
	      array('checkbox', 'search' , true, "Show the search field"),
	      );
	if(!$js) {
		echo '<ul style="padding:15px 15px 0 15px; width:100%;">';
		foreach($standard as $o) {
			kws_gf_make_field($o[0], $o[1], esc_html($o[2]), esc_html($o[3]));
		}
		echo '</ul>';
		echo '<a href="#kws_gf_advanced_settings" class="kws_gf_advanced_settings">Show advanced settings</a>';
		echo '<div style="display:none;" id="kws_gf_advanced_settings">';
		echo "<fieldset><legend style='margin:0; padding:0; font-weight:bold; font-size:1.5em; margin-top:1em; padding:.5em 0;'>Advanced Settings</legend>";
		echo "<h3 style='margin-top:1em;'>Inputs Galore</h3>";
		echo '<ul style="padding: 0 15px 0 15px; width:100%;">';
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
		      array('radio'   , 'postimage' , array(array('label' =>'<img src="'.GFCommon::get_base_url().'/images/doctypes/icon_image.gif" /> Show image icon', 'value'=>'icon', 'default'=>'1'), array('label' =>'Show full image', 'value'=>'image')), "How do you want images to appear in the directory?"),
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
		);
	if(!$js) { 	     
		foreach($advanced as $o) {
			kws_gf_make_field($o[0], $o[1], $o[2], esc_html($o[3]));
		}
		echo '</ul>';
		echo '<hr style="margin:1em; display:block; outline:none; border-bottom:1px solid #ccc;" />';
		echo "<h3 style='margin-top:1em;'>Inputs Galore</h3>";
		echo '<ul style="padding: 0 15px 0 15px; width:100%;">';
	} else {
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
			kws_gf_make_field($o[0], $o[1], esc_html($o[2]), esc_html($o[3]));
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
    add_action('admin_footer',  'kws_gf_add_mce_popup');
}

?>