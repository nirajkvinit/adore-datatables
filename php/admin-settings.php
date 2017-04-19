<?php

add_action('admin_menu', 'fn_adt_settings_menu');
function fn_adt_settings_menu()
{
	global $adt_settings_page;
    $adt_settings_page=add_menu_page("Adore Datatables", "Adore Datatables", 'manage_options', PLUGIN_ADMIN_PAGE_SLUG, 'fn_adore_datatables_main_settings_page', 'dashicons-media-spreadsheet' );
}

function fn_adore_datatables_main_settings_page()
{
	$str_manage_tab_active='';
	$str_css_tab_active='';
	$str_javascript_tab_active='';
	$str_settings_tab_active='';

	$str_content='';

	$tab='';
	if(isset($_GET['tab']))
	{
		$tab=$_GET['tab'];
	}


	switch($tab)
	{
		case 'css':
			$str_css_tab_active=' nav-tab-active';
			$str_content=fn_adt_admin_css_content();
			break;
		case 'javascript':
			$str_javascript_tab_active=' nav-tab-active';
			$str_content=fn_adt_admin_js_content();
			break;
		case 'settings':
			$str_settings_tab_active=' nav-tab-active';
			$str_content=fn_adt_admin_settings_content();
			break;
		default:
			$str_manage_tab_active=' nav-tab-active';
			$str_content=fn_adt_admin_main_content();
			break;
	}

	$str_return='
		<div class="wrap">
			<h2>Adore Datatables</h2>
	        <div class="welcome-panel">
	        	<div class="welcome-panel-content">
	        		<h3>'.__('Welcome to Adore Datatables! (A jQuery Datatables based wordpress plugin)','adt').'</h3>
	        		<h2 class="nav-tab-wrapper">
						<a class="nav-tab'.$str_manage_tab_active.'" href="?page='.PLUGIN_ADMIN_PAGE_SLUG.'&tab=manage">'.__('Manage','adt').'</a>
						<a class="nav-tab'.$str_css_tab_active.'" href="?page='.PLUGIN_ADMIN_PAGE_SLUG.'&tab=css">CSS</a>
						<a class="nav-tab'.$str_javascript_tab_active.'" href="?page='.PLUGIN_ADMIN_PAGE_SLUG.'&tab=javascript">Javascript</a>
						<a class="nav-tab'.$str_settings_tab_active.'" href="?page='.PLUGIN_ADMIN_PAGE_SLUG.'&tab=settings">'.__('Settings','adt').'</a>
					</h2>
					'.$str_content.'
	        	</div>
	        </div>
		</div>
	';
	echo $str_return;
}

/**
 *
 * @global type $wpdb
 * @return string
 * @version 2, 19/4/2017, lenasterg
 */
function fn_adt_admin_main_content()
{
	global $wpdb;

	//check if the adore_datatable_settings table exist or not

	$str_sql="SELECT * FROM information_schema.tables WHERE table_schema = '".$wpdb->dbname."' AND table_name = 'adore_datatable_settings';";

	$settings_table_exist=$wpdb->get_results($str_sql);

	if(empty($settings_table_exist))
	{
		return __('Settings table does not exist.','adt');
	}

	$str_adt_options='
		<select id="select_adt_table">
			<option value="select">'.__('Select a Datatable','adt').'</option>
			<option value="create">'.__('Create a Datatable','adt').'</option>
	';

	$datatables_db=$wpdb->get_results("SELECT adt_id, adt_table_name, adt_table_slug FROM adore_datatable_settings WHERE adt_blog_id=". get_current_blog_id());

	foreach($datatables_db as $datatables)
	{
		$str_adt_options.='
			<option value="'.$datatables->adt_id.'" data-slug="'.$datatables->adt_table_slug.'">'.$datatables->adt_table_name.'</option>
		';
	}

	$str_adt_options.='
		</select>
	';

    $str_return='
    	<br />
    	<p class="about-description">'.__('Use the shortcode','adt').' <code>[adt_demo_datatables]</code> '.__('in any page/post to checkout the demo. To create a new Adore Datatable instance, please use the form below:','adt').'</p>
    	<hr />
		<table width="100%">
        	<tr>
        		<td align="right">'.__('Select or Create a Datatable:','adt').'</td>
        		<td>'.$str_adt_options.'</td>
        	</tr>
        </table>
        <hr />
        <input type="hidden" id="hidden_adt_save_nonce" value="'.wp_create_nonce(PLUGIN_ADMIN_PAGE_SLUG).'" />
        <img id="adt_settings_loader" src="'.plugins_url('/assets/images/loader.gif', dirname(__FILE__)).'" style="display:none;"/>
        <div id="div_adt_settings_area"></div>
    ';
	return $str_return;
}

function fn_adt_admin_settings_content()
{
	$jquery_theme_array=array("smoothness","ui-lightness","ui-darkness","humanity","redmond","start","cupertino","trontastic",
		"black-tie","blitzer","dark-hive","dot-luv","eggplant","excite-bike","flick","hot-sneaks","le-frog","mint-choc","overcast",
		"pepper-grinder","south-street","sunny","swanky-purse","vader");

	$adore_datatable_style_array=array(
		'base_style'=>'Base style',
		'base_style_noclass'=>'Base style - no styling classes',
		'base_style_cell_borders'=>'Base style - cell borders',
		'base_style_compact'=>'Base style - compact',
		'base_style_hover'=>'Base style - hover',
		'base_style_order_column'=>'Base style - order-column',
		'base_style_row_borders'=>'Base style - row borders',
		'base_style_stripe'=>'Base style - stripe',
		'bootstrap'=>'Bootstrap',
		'jqueryui_themeroller'=>'jQuery UI ThemeRoller'
	);

	$adt_admin_config=get_option('adt_admin_config');
	if($adt_admin_config==FALSE)
	{
		$adt_admin_config_array=array();
		$adt_admin_config_array['adt_style']='base_style';
		$adt_admin_config_array['jquery_theme']='smoothness';
		$adt_admin_config_array['load_jquery']='enabled';
		$adt_admin_config_array['load_bootstrap']='enabled';

		$adt_admin_config=json_encode($adt_admin_config_array);
		$deprecated = null;
	    $autoload = 'no';
	    add_option('adt_admin_config', $adt_admin_config, $deprecated, $autoload);
	}
	$adt_admin_config=json_decode($adt_admin_config,TRUE);

	$str_table_styles='<select id="select_adt_styles" style="width:100%">';
	foreach($adore_datatable_style_array as $key => $value)
	{
		$str_selected='';
		if($key==$adt_admin_config['adt_style'])
		{
			$str_selected=" selected";
		}
		$str_table_styles.='
			<option value="'.$key.'" '.$str_selected.'>'.$value.'</option>
		';
	}
	$str_table_styles.='</select>';

	$str_jqueryui_theme_selector='<select id="select_adt_jquery_ui_theme" style="width:100%">';
	foreach($jquery_theme_array as $theme)
	{
		$str_selected='';
		if($theme==$adt_admin_config['jquery_theme'])
		{
			$str_selected=" selected";
		}
		$str_jqueryui_theme_selector.='
			<option value="'.$theme.'" '.$str_selected.'>'.$theme.'</option>
		';
	}
	$str_jqueryui_theme_selector.='
		</select>
	';

	$str_load_jquery_checked='';
	$str_load_bootstrap_checked='';
	if($adt_admin_config['load_jquery']=='enabled')
	{
		$str_load_jquery_checked=' checked';
	}

	if($adt_admin_config['load_bootstrap']=='enabled')
	{
		$str_load_bootstrap_checked=' checked';
	}

	$str_return='
		<br />
		<table width="100%">
			<tr>
				<td align="right"><label for="select_adt_styles" title="Datatable styles">'.__('Datatable Styling','adt').'</label></td>
				<td>'.$str_table_styles.'</td>
				<td align="right"><label for="select_adt_jquery_ui_theme">Jquery Theme</label></td>
				<td>'.$str_jqueryui_theme_selector.'</td>
			</tr>
			<tr>
				<td colspan="4">
					<p class="alternate">'.__('If any of your theme or other existing plugin already loads Bootstrap/jQueryUI files then do not load them here.','adt').'</p>
					<p class="alt">'.__('If you do not allow Adore Datatable to Load JqueryUI here then jQueryUI themes would not be available.','adt').'</p>
					<hr/>
				</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td><label><input type="checkbox" id="chk_adt_load_bootstrap" '.$str_load_bootstrap_checked.'>'.__('Load Bootstrap through plugin','adt').'</label></td>
        		<td><label><input type="checkbox" id="chk_adt_load_jqueryui" '.$str_load_jquery_checked.'>'.__('Load jQueryUI through plugin','adt').'</label></td>
			</tr>
			<tr>
				<td colspan="4">
					<hr />
					<div id="div_adt_settings_save_result"></div>
					<input type="hidden" id="hidden_adt_settings_nonce" value='.wp_create_nonce('adt_settings_save').' />
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<img id="adt_settings_save_loader" src="'.plugins_url('/assets/images/loader.gif', dirname(__FILE__)).'" style="display:none;"/>
				</td>
				<td align="right"><input type="button" class="button button-primary" id="cmd_adt_settings_save" value="'.__('Save Settings','adt').'" /></td>
			</tr>
		</table>
	';
	return $str_return;
}

add_action('wp_ajax_fn_load_dt_settings_ajax', 'fn_load_dt_settings_ajax');

function fn_load_dt_settings_ajax()
{
	global $wpdb;
	$adt_id=$_POST['adt_id'];
	$slug=sanitize_text_field($_POST['slug']);
	$str_return='';

	if(is_numeric($adt_id))
	{
		$str_return=fn_return_adt_form($adt_id,$slug);
	}
	else if($adt_id=='create')
	{
		$str_return=fn_create_new_adt_form();
	}
	else
	{
		echo '<div class="error">'.__('Error! Input was incorrect! Please try again.','adt').'</div>';
		die();
	}

	echo $str_return;
	die();
}

/**
 * Function to return datatable information for shortcode or for update or delete.
 */
function fn_return_adt_form($adt_id,$slug)
{
	$str_return='
		<div class="welcome-panel">
        	<div class="welcome-panel-content">
        		<h3>Shortcode :	</h3>
        		<hr />
        		<code>[adore-datatables id="'.$adt_id.'"]</code> "'.__('Or','adt').'<code>[adore-datatables table="'.$slug.'"]</code>
        		<input type="submit" value="'.__('Delete this Datatable Instance','adt').'" class="button" id="cmd_delete_adt_instance">
				<div id="div_adt_delete_result">
				</div>
        		<br />
        	</div>
        </div>
	'.fn_update_adt_form($adt_id,$slug);
	return $str_return;
}

add_action('wp_ajax_fn_delete_adt_ajax', 'fn_delete_adt_ajax');

/**
 *
 * @global type $wpdb
 * @version 2, lenasterg
 * @todo Fix the echo message
 */
function fn_delete_adt_ajax()
{
	global $wpdb;

	//get datatable information datatable slug, datatable id,
	$selected_adt_id=$_POST['selected_adt_id'];

	if(!is_numeric($selected_adt_id))
	{
		_e('Error deleting Datatable! Datatable ID was not in proper format','adt');
		die();
	}

	//delete datatable from adore_datatable_settings table by id $selected_adt_id
	$str_sql="DELETE FROM adore_datatable_settings WHERE adt_id=$selected_adt_id AND adt_blog_id=". get_current_blog_id();
	$wpdb->query($str_sql);

	_e('Success! Adore Datatable instance was deleted successfully!','adt');

	die();
}


/**
 *
 * @global type $wpdb
 * @param type $adt_id
 * @param type $slug
 * @return string
 * @todo Make it work
 */
function fn_update_adt_form($adt_id,$slug)
{
	global $wpdb;

	if(!is_numeric($adt_id))
	{
		return '';
	}

	$adt_datatable_dataset=$wpdb->get_results("SELECT * FROM adore_datatable_settings WHERE adt_id=$adt_id AND adt_blog_id=". get_current_blog_id());

	$adt_table_settings='';
	$adt_name='';
	$adt_table_slug='';

	foreach($adt_datatable_dataset as $adt_datatable)
	{
		$adt_table_settings=json_decode($adt_datatable->adt_table_settings,TRUE);
		$adt_name=$adt_datatable->adt_table_name;
		$adt_table_slug=$adt_datatable->adt_table_slug;
	}

	$tables_sql="SELECT table_name FROM information_schema.tables WHERE table_schema='".$wpdb->dbname."';";
	$tables_db=$wpdb->get_results($tables_sql);

	$str_adt_table_option='
		<select id="select_adt_db_tables_list" style="width:100%">
			<option value="select">'.__('Select a Table','adt').'</option>
	';

	foreach($tables_db as $tables)
	{
		$table_name=$tables->table_name;
		$str_selected='';
		if($table_name==$adt_table_settings['database_table_name'])
		{
			$str_selected='selected';
		}
		$str_adt_table_option.='
			<option value="'.$table_name.'" '.$str_selected.'>'.$table_name.'</option>
		';
	}
	$str_adt_table_option.='
		</select>
	';


	//table_type
	$str_server_adt='';
	$str_html_adt='';
	switch($adt_table_settings['table_type'])
	{
		case 'server':
			$str_server_adt='selected';
			break;
		case 'html':
			$str_html_adt='selected';
			break;
	}

	$str_table_type='
		<select id="select_adt_table_type" style="width:100%">
			<option value="server" '.$str_server_adt.'>'.__('Server-side processing','adt').'</option>
			<option value="html" '.$str_html_adt.'>'.__('HTML (DOM) sourced data','adt').'</option>
		</select>
	';

	$str_page_full='';
	$str_page_full_numbers='';
	$str_page_simple='';
	$str_page_simple_numbers='';

	switch($adt_table_settings['pagination_type'])
	{
		case 'full_numbers':
			$str_page_full_numbers='selected';
			break;
		case 'full':
			$str_page_full='selected';
			break;
		case 'simple_numbers':
			$str_page_simple_numbers='selected';
			break;
		case 'simple':
			$str_page_simple='selected';
			break;
	}

	$str_pagination_type='
		<select id="select_adt_pagination" style="width:100%">
			<option value="full_numbers" '.$str_page_full_numbers.'>'.__('Full Numbers','adt').'</option>
			<option value="full" '.$str_page_full.'>'.__('Full','adt').'</option>
			<option value="simple_numbers" '.$str_page_simple_numbers.'>'.__('Simple Numbers','adt').'</option>
			<option value="simple" '.$str_page_simple.'>'.__('Simple','adt').'</option>
		</select>
	';

	$chk_adt_allow_search='';
	$chk_adt_ordering='';
	$chk_adt_showinfo='';
	$chk_adt_autowidth='';
	$chk_adt_scrollvertical='';
	$chk_adt_individual_column_filtering='';
	if($adt_table_settings['allow_search']=='enabled')
	{
		$chk_adt_allow_search='checked';
	}
	if($adt_table_settings['allow_ordering']=='enabled')
	{
		$chk_adt_ordering='checked';
	}
	if($adt_table_settings['show_info']=='enabled')
	{
		$chk_adt_showinfo='checked';
	}
	if($adt_table_settings['allow_auto_width']=='enabled')
	{
		$chk_adt_autowidth='checked';
	}
	if($adt_table_settings['scroll_vertical']=='enabled')
	{
		$chk_adt_scrollvertical='checked';
	}
	if($adt_table_settings['individual_column_filtering']=='enabled')
	{
		$chk_adt_individual_column_filtering='checked';
	}

	$str_return='
		<div class="welcome-panel">
        	<div class="welcome-panel-content">
        		<h3>'.__('Update Adore Datatable','adt').' - '.$adt_name.'</h3><hr />
        		<table width="100%">
        			<tr>
        				<th><label for="txt_adt_datatable_name" title="'.__('Name using which you would recognize your datatable instance','adt').'">'.__('Datatable Name','adt').'</label></th>
        				<th><label for="txt_adt_table_id" title="'.__('CSS ID attribute for the HTML Table','adt').'">'.__('Table ID','adt').' ('.__('Optional','adt').')</label></th>
        				<th><label for="txt_adt_table_class" title="'.__('Additional class for the HTML Table','adt').'">'.__('Table Class','adt').' ('.__('Optional','adt').')</label></th>
        			</tr>
        			<tr>
        				<td><input type="text" id="txt_adt_datatable_name" style="width:100%" value="'.$adt_name.'" /></td>
        				<td><input type="text" id="txt_adt_table_id" style="width:100%" value="'.$adt_table_settings['html_table_id'].'" /></td>
        				<td><input type="text" id="txt_adt_table_class" style="width:100%" value="'.$adt_table_settings['html_table_class'].'" /></td>
        			</tr>
        			<tr>
        				<th><label for="select_adt_table_type" title="'.__('HTML Table or Server Side Ajaxed. Warning: Too many records may freeze your browser if you select HTML (DOM) sourced data.','adt').'">'.__('Table Type','adt').'</label></th>
        				<th><label for="select_adt_pagination" title="'.__('Datatable Pagination Types','adt').'">'.__('Pagination','adt').'</label></th>
        				<th><label for="select_adt_db_tables_list">'.__('Select a Database Table','adt').'</label></th>
        			</tr>
        			<tr>
        				<td>'.$str_table_type.'</td>
        				<td>'.$str_pagination_type.'</td>
        				<td>'.$str_adt_table_option.'</td>
        			</tr>
        			<tr>
        				<td colspan="3"><div id="div_adt_table_columns_options"></div></td>
        			</tr>
        			<tr>
        				<td><label><input type="checkbox" id="chk_adt_allow_search" '.$chk_adt_allow_search.'>'.__('Allow Search','adt').'</label></td>
        				<td><label><input type="checkbox" id="chk_adt_ordering" '.$chk_adt_ordering.'>'.__('Ordering','adt').'</label></td>
        				<td><label><input type="checkbox" id="chk_adt_showinfo" '.$chk_adt_showinfo.'>'.__('Show Info','adt').'</label></td>
        			</tr>
        			<tr>
        				<td><label><input type="checkbox" id="chk_adt_autowidth" '.$chk_adt_autowidth.'>'.__('AutoWidth','adt').'</label></td>
        				<td><label><input type="checkbox" id="chk_adt_scrollvertical" '.$chk_adt_scrollvertical.'>'.__('Scroll Vertical','adt').'</label></td>
        				<td><label><input type="checkbox" id="chk_adt_individual_column_filtering" '.$chk_adt_individual_column_filtering.'>'.__('Individual Column Filtering','adt').'</label></td>
        			</tr>
        			<tr>
        				<th colspan="3"><label for="txt_adt_sdom" title="'.__('Datatable DOM Positioning (Leave it blank if you do not know what you are doing.)">Dom','adt').'</label></th>
        			</tr>
        			<tr>
        				<td colspan="3"><input type="text" id="txt_adt_sdom" style="width:100%" value="'.$adt_table_settings['sdom'].'" /></td>
        			</tr>
        			<tr>
        				<th colspan="3"><label for="txt_adt_fnrowcallback" title="'.__('Extra options while datatable is rendering rows (Leave it blank if you do not know what you are doing.)','adt').'">fnRowCallback</label></th>
        			</tr>
        			<tr>
        				<td colspan="3"><textarea name="" id="txt_adt_fnrowcallback" style="width:100%" rows="5">'.$adt_table_settings['fn_row_callback'].'</textarea></td>
        			</tr>
        			<tr>
        				<td colspan="2"><img id="adt_table_save_loader" src="'.plugins_url('/assets/images/loader.gif', dirname(__FILE__)).'" style="display:none;"/></td>
        				<td align="right"><input type="button" class="button button-primary" id="cmd_create_adt_datatable" value="'.__('Save Datatable','adt').'" /></td>
        			</tr>
        			<tr>
        				<td colspan="3"><div id="div_adt_table_save_result_area"></div></td>
        			</tr>
        		</table>
        		<input type="hidden" id="hidden_adt_slug" value="'.$slug.'" />
        	</div>
        </div>
	';
	return $str_return;
}

function fn_create_new_adt_form()
{
	//Ref. https://datatables.net/reference/option/
	//implement length change
	//implement paging

	global $wpdb;

	$tables_sql="SELECT table_name FROM information_schema.tables WHERE table_schema='".$wpdb->dbname."';";
	$tables_db=$wpdb->get_results($tables_sql);

	$str_adt_table_option='
		<select id="select_adt_db_tables_list" style="width:100%">
			<option value="select">'.__('Select a Table','adt').'</option>
	';

	foreach($tables_db as $tables)
	{
		$table_name=$tables->table_name;
		$str_adt_table_option.='
			<option value="'.$table_name.'">'.$table_name.'</option>
		';
	}
	$str_adt_table_option.='
		</select>
	';

	$str_table_type='
		<select id="select_adt_table_type" style="width:100%">
			<option value="server">'.__('Server-side processing','adt').'</option>
			<option value="html">'.__('HTML (DOM) sourced data','adt').'</option>
		</select>
	';
	$str_pagination_type='
		<select id="select_adt_pagination" style="width:100%">
			<option value="full_numbers">'.__('Full Numbers','adt').'</option>
			<option value="full">'.__('Full','adt').'</option>
			<option value="simple_numbers">'.__('Simple Numbers','adt').'</option>
			<option value="simple">'.__('Simple','adt').'</option>
		</select>
	';


	$str_return='
		<div class="welcome-panel">
        	<div class="welcome-panel-content">
        		<h3>'.__('Create a new Adore Datatable!','adt').'</h3><hr />
        		<table width="100%">
        			<tr>
        				<th><label for="txt_adt_datatable_name" title="'.__('Name using which you would recognize your datatable instance">Datatable Name','adt').'</label></th>
        				<th><label for="txt_adt_table_id" title="'.__('CSS ID attribute for the HTML Table">Table ID','adt').' ('.__('Optional','adt').')</label></th>
        				<th><label for="txt_adt_table_class" title="'.__('Additional class for the HTML Table">Table Class','adt').' ('.__('Optional','adt').')</label></th>
        			</tr>
        			<tr>
        				<td><input type="text" id="txt_adt_datatable_name" style="width:100%" /></td>
        				<td><input type="text" id="txt_adt_table_id" style="width:100%" /></td>
        				<td><input type="text" id="txt_adt_table_class" style="width:100%" /></td>
        			</tr>
        			<tr>
        				<th><label for="select_adt_table_type" title="'.__('HTML Table or Server Side Ajaxed. Warning: Too many records may freeze your browser if you select HTML (DOM) sourced data.">Table Type','adt').'</label></th>
        				<th><label for="select_adt_pagination" title="'.__('Datatable Pagination Types','adt').'">'.__('Pagination','adt').'</label></th>
        				<th><label for="select_adt_db_tables_list">'.__('Select a Database Table','adt').'</label></th>
        			</tr>
        			<tr>
        				<td>'.$str_table_type.'</td>
        				<td>'.$str_pagination_type.'</td>
        				<td>'.$str_adt_table_option.'</td>
        			</tr>
        			<tr>
        				<td colspan="3"><div id="div_adt_table_columns_options"></div></td>
        			</tr>
        			<tr>
        				<td><label><input type="checkbox" id="chk_adt_allow_search" checked>'.__('Allow Search','adt').'</label></td>
        				<td><label><input type="checkbox" id="chk_adt_ordering" checked>'.__('Ordering','adt').'</label></td>
        				<td><label><input type="checkbox" id="chk_adt_showinfo" checked>'.__('Show Info','adt').'</label></td>
        			</tr>
        			<tr>
        				<td><label><input type="checkbox" id="chk_adt_autowidth">'.__('AutoWidth','adt').'</label></td>
        				<td><label><input type="checkbox" id="chk_adt_scrollvertical">'.__('Scroll Vertical','adt').'</label></td>
        				<td><label><input type="checkbox" id="chk_adt_individual_column_filtering">'.__('Individual Column Filtering','adt').'</label></td>
        			</tr>
        			<tr>
        				<th colspan="3"><label for="txt_adt_sdom" title="'.__('Datatable DOM Positioning (Leave it blank if you do not know what you are doing.)','adt').'">Dom</label></th>
        			</tr>
        			<tr>
        				<td colspan="3"><input type="text" id="txt_adt_sdom" style="width:100%" /></td>
        			</tr>
        			<tr>
        				<th colspan="3"><label for="txt_adt_fnrowcallback" title="'.__('Extra options while datatable is rendering rows (Leave it blank if you do not know what you are doing.)','adt').'">fnRowCallback</label></th>
        			</tr>
        			<tr>
        				<td colspan="3"><textarea name="" id="txt_adt_fnrowcallback" style="width:100%" rows="5"></textarea></td>
        			</tr>
        			<tr>
        				<td colspan="2"><img id="adt_table_save_loader" src="'.plugins_url('/assets/images/loader.gif', dirname(__FILE__)).'" style="display:none;"/></td>
        				<td align="right"><input type="button" class="button button-primary" id="cmd_create_adt_datatable" value="'.__('Save Datatable','adt').'" /></td>
        			</tr>
        			<tr>
        				<td colspan="3"><div id="div_adt_table_save_result_area"></div></td>
        			</tr>
        		</table>

        	</div>
        </div>
	';
	return $str_return;
}

add_action('wp_ajax_fn_show_adt_coulms_ajax', 'fn_show_adt_coulms_ajax');
function fn_show_adt_coulms_ajax()
{
	global $wpdb;

	$str_return=__('Column select error!','adt');
	$selected_table_name=sanitize_text_field($_POST['adt_selected_table']);

	$str_sql="SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$wpdb->dbname."' AND TABLE_NAME = '$selected_table_name';";

	$columns_db=$wpdb->get_results($str_sql);
	if(!empty($columns_db))
	{
		$str_return='
			<hr />
			<table id="tbl_adt_column_select" width="100%">
				<thead>
					<tr>
						<th>'.__('Name','adt').'</th>
						<th>'.__('Title','adt').'</th>
						<th>'.__('Visible','adt').'</th>
						<th>'.__('Searchable','adt').'</th>
						<th>'.__('Sortable','adt').'</th>
						<th title="className">'.__('CSS Class(es)','adt').'</th>
						<th title="Column Position">'.__('Column Position','adt').'</th>
					</tr>
				</thead>
				<tbody>
		';
		$column_position=0;
		foreach($columns_db as $columns)
		{
			$column_name=$columns->COLUMN_NAME;
			$str_return.='
				<tr class="row_adt_column_data">
					<td>'.$column_name.'</td>
					<td><input type="text" class="txt_adt_column_title" style="width:100%" value="'.$column_name.'" /></td>
					<td align="center"><input type="checkbox" class="chk_adt_column_visible" checked /></td>
					<td align="center"><input type="checkbox" class="chk_adt_column_searchable" checked /></td>
					<td align="center"><input type="checkbox" class="chk_adt_column_sortable" checked /></td>
					<td><input type="text" class="txt_adt_column_className" style="width:100%" /></td>
					<td align="center"><input type="text" class="txt_adt_column_position small-text" value="'.$column_position.'" /></td>
				</tr>
			';
			$column_position++;
		}
		$str_return.='
				</tbody>
			</table><hr />
		';
	}
	echo $str_return;
	die();
}

add_action('wp_ajax_fn_save_adt_table_ajax', 'fn_save_adt_table_ajax');

/**
 * Saves datatable into adt_settings array
 * @version 2, 19/4/2017
 */
function fn_save_adt_table_ajax()
{
	global $wpdb;
	$adt_nonce=$_POST['adt_nonce'];
	$datatable_name=sanitize_text_field($_POST['datatable_name']);
	$html_table_id=sanitize_text_field($_POST['html_table_id']);
	$html_table_class=sanitize_text_field($_POST['html_table_class']);
	$table_type=sanitize_text_field($_POST['table_type']);
	$pagination_type=sanitize_text_field($_POST['pagination_type']);
	$allow_search=sanitize_text_field($_POST['allow_search']);
	$allow_ordering=sanitize_text_field($_POST['allow_ordering']);
	$show_info=sanitize_text_field($_POST['show_info']);
	$allow_auto_width=sanitize_text_field($_POST['allow_auto_width']);
	$scroll_vertical=sanitize_text_field($_POST['scroll_vertical']);
	$individual_column_filtering=sanitize_text_field($_POST['individual_column_filtering']);
	$sdom=sanitize_text_field($_POST['sdom']);
	$fn_row_callback=sanitize_text_field($_POST['fn_row_callback']);
	$database_table_name=sanitize_text_field($_POST['database_table_name']);
	$adt_column_data=$_POST['adt_column_data'];

	$result_array=array();
	$result_array['result']='error';
	$result_array['result_message']=__('Datatable could not be saved. Please try again!','adt');



	$nonce_result=wp_verify_nonce( $adt_nonce, PLUGIN_ADMIN_PAGE_SLUG);
	if(!$nonce_result)
	{
		$result_array['result_message']=__('Security Error! Security could not be validated! Please try again!','adt');
		echo json_encode($result_array);
		die();
	}


	if(empty($datatable_name))
	{
		$result_array['result_message']=__('Error! Please provide datatable name and try again.','adt');
		echo json_encode($result_array);
		die();
	}

	if(empty($database_table_name))
	{
		$result_array['result_message']=__('Error! Please select a database table/view and try again.','adt');
		echo json_encode($result_array);
		die();
	}

	$datatable_slug=sanitize_title($datatable_name);

	//check if datatable name is duplicate or not. we do not allow duplicate as the adore datatable shortcode will be available via ID or SLUG.
	$str_duplicate_sql="SELECT adt_table_slug FROM adore_datatable_settings WHERE adt_table_slug='$datatable_slug' AND adt_blog_id=". get_current_blog_id();

	$is_duplicate_adt=$wpdb->get_results($str_duplicate_sql);
	if(!empty($is_duplicate_adt))
	{
		$result_array['result_message']=__('Error! Datatable name cannot be duplicate. Please input another name and try again.','adt');
		echo json_encode($result_array);
		die();
	}

	//Sort the column array by user provided column position
	fn_sort_md_array_by_value($adt_column_data,'column_position');

	$datatable_array=array();

	$datatable_array['html_table_id']=$html_table_id;
	$datatable_array['html_table_class']=$html_table_class;
	$datatable_array['table_type']=$table_type;
	$datatable_array['pagination_type']=$pagination_type;
	$datatable_array['allow_search']=$allow_search;
	$datatable_array['allow_ordering']=$allow_ordering;
	$datatable_array['show_info']=$show_info;
	$datatable_array['allow_auto_width']=$allow_auto_width;
	$datatable_array['scroll_vertical']=$scroll_vertical;
	$datatable_array['individual_column_filtering']=$individual_column_filtering;
	$datatable_array['sdom']=$sdom;
	$datatable_array['fn_row_callback']=$fn_row_callback;
	$datatable_array['database_table_name']=$database_table_name;

	$datatable_array['columns_array']=$adt_column_data;

	$datatable_json=json_encode($datatable_array);

	$insert_array=array(
                'adt_blog_id' => get_current_blog_id(),
		'adt_table_name'=>$datatable_name,
		'adt_table_slug'=>$datatable_slug,
		'adt_table_settings'=>$datatable_json
	);
	$wpdb->insert('adore_datatable_settings', $insert_array);
	$datatable_id=$wpdb->insert_id;


	$result_array['datatable_id']=$datatable_id;
	$result_array['datatable_name']=$datatable_name;
	$result_array['datatable_slug']=$datatable_slug;

	//assuming jquery validation of the table columns are effective. Will implement the PHP validation code here later.

	$result_array['result']='success';
	$result_array['result_message']='Adore Datatable Settings were saved! Please wait!';
	echo json_encode($result_array);
	die();
}

/**
 * Function to sort multi-dimentional array by a value key
 */
function fn_sort_md_array_by_value(&$array, $key)
{
	//ref. http://stackoverflow.com/questions/2699086/sort-multi-dimensional-array-by-value
    $sorter=array();
    $ret=array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii]=$va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    $array=$ret;
}

add_action('wp_ajax_fn_adt_settings_save_ajax', 'fn_adt_settings_save_ajax');
function fn_adt_settings_save_ajax()
{
	$adt_settings_nonce=$_POST['adt_settings_nonce'];
	$adt_style=sanitize_text_field($_POST['adt_style']);
	$jqueryui_theme=sanitize_text_field($_POST['jqueryui_theme']);
	$load_bootstrap=sanitize_text_field($_POST['load_bootstrap']);
	$load_jqueryui=sanitize_text_field($_POST['load_jqueryui']);

	$result_array=array();
	$result_array['result']='error';
	$result_array['result_message']=__('Adore Datatables Settings could not be saved. Please try again.','adt');



	$nonce_result=wp_verify_nonce( $adt_settings_nonce, 'adt_settings_save');
	if(!$nonce_result)
	{
		$result_array['result_message']=__('Security Error! Security could not be validated! Please try again!','adt');
		echo json_encode($result_array);
		die();
	}
	if(empty($adt_style)||empty($jqueryui_theme)||empty($load_bootstrap)||empty($load_jqueryui))
	{
		$result_array['result_message']=__('Settings values are empty. Please try again!','adt');
		echo json_encode($result_array);
		die();
	}

	$adt_admin_config_array=array();
	$adt_admin_config_array['adt_style']=$adt_style;
	$adt_admin_config_array['jquery_theme']=$jqueryui_theme;
	$adt_admin_config_array['load_jquery']=$load_jqueryui;
	$adt_admin_config_array['load_bootstrap']=$load_bootstrap;

	$adt_admin_config=json_encode($adt_admin_config_array);

	update_option( 'adt_admin_config', $adt_admin_config );

	$result_array['result']='success';
	$result_array['result_message']=__('Adore Datatables Settings were saved successfully!','adt');
	echo json_encode($result_array);
	die();
}


/************************************************************************************************************************************
 ************************************************************************************************************************************
 * 						Codes to save Custom Javascript and CSS in Adore Datatables Admin Control Panel  using Wordpress Filesystem API
 */
 function fn_adt_admin_css_content()
{
	//$adt_css_file_name=ADT_PLUGIN_DIR_PATH.'assets/css/adt_custom_css.css';//ADT_PLUGIN_DIR_PATH
	//$adt_css_folder_path=ADT_PLUGIN_DIR_PATH.'assets/css/';//ADT_PLUGIN_DIR_PATH

	//refrence http://www.webdesignerdepot.com/2012/08/wordpress-filesystem-api-the-right-way-to-operate-with-local-files/

	$form_url = 'admin.php?page='.PLUGIN_ADMIN_PAGE_SLUG.'&tab=css';
	$output = $error = '';

	/**
	 * write submitted text into file (if any)
	 * or read the text from file - if there is no submission
	 **/
	if(isset($_POST['txt_adt_css']))//new submission
	{

	    if(false === ($output = fn_adt_css_write($form_url)))
	    {
	        return; //we are displaying credentials form - no need for further processing

	    }
	    elseif(is_wp_error($output))
	    {
	        $error = $output->get_error_message();
	        $output = '';
	    }

	} else {//read from file

	    if(false === ($output = fn_adt_css_read($form_url))){
	        return; //we are displaying credentials form no need for further processing

	    } elseif(is_wp_error($output)) {
	        $error = $output->get_error_message();
	        $output = '';
	    }
	}

	$output = $output; //escaping for printing

	$str_error='';
	if($error)
	{
		$str_error='<div class="error below-h2">'.$error.'</div>';
	}

	$str_return='
		<div class="postbox">
		    <h3 class="hndle"><span>Custom Adore Datatables CSS (Global)</span></h3>
		    <div class="inside">
		        <p class="alternate">Write your custom CSS here. This CSS will be loaded on every page/post where Adore Datatable instance exists.</p>
		        '.$str_error.'
				<form method="post" action="">
					'.wp_nonce_field('adt_css_save_nonce').'
					<fieldset class="form-table">
					    <label for="txt_adt_css">Write your custom CSS Below</label><br>
					    <textarea id="txt_adt_css" name="txt_adt_css" rows="8" class="large-text">'.$output.'</textarea>
					</fieldset>
					<input type="submit" value="Save CSS" class="button button-primary" id="cmd_adt_css_submit" name="cmd_adt_css_submit">
				</form>
		    </div>
		</div>
	';
	return $str_return;
}

/**
 * Initialize Filesystem object
 *
 * @param str $form_url - URL of the page to display request form
 * @param str $method - connection method
 * @param str $context - destination folder
 * @param array $fields - fileds of $_POST array that should be preserved between screens
 * @return bool/str - false on failure, stored text on success
 **/
function fn_adt_filesystem_init($form_url, $method, $context, $fields = null)
{
    global $wp_filesystem;


    /* first attempt to get credentials */
    if (false === ($creds = request_filesystem_credentials($form_url, $method, false, $context, $fields))) {

        /**
         * if we comes here - we don't have credentials
         * so the request for them is displaying
         * no need for further processing
         **/
        return false;
    }

    /* now we got some credentials - try to use them*/
    if (!WP_Filesystem($creds)) {

        /* incorrect connection data - ask for credentials again, now with error message */
        request_filesystem_credentials($form_url, $method, true, $context);
        return false;
    }

    return true; //filesystem object successfully initiated
}

/**
 * Perform writing into file
 *
 * @param str $form_url - URL of the page to display request form
 * @return bool/str - false on failure, stored text on success
 **/
function fn_adt_css_write($form_url)
{
    global $wp_filesystem;

    check_admin_referer('adt_css_save_nonce');

    $csstext = esc_textarea($_POST['txt_adt_css']); //sanitize the input
    $form_fields = array('txt_adt_css'); //fields that should be preserved across screens
    $method = ''; //leave this empty to perform test for 'direct' writing

    $context = ADT_PLUGIN_DIR_PATH.'assets/css/'; //target folder

    $form_url = wp_nonce_url($form_url, 'adt_css_save_nonce'); //page url with nonce value

    if(!fn_adt_filesystem_init($form_url, $method, $context, $form_fields)) {
        return false; //stop further processign when request form is displaying
    }

    /*
     * now $wp_filesystem could be used
     * get correct target file first
     **/
    $target_dir = $wp_filesystem->find_folder($context);
    $target_file = trailingslashit($target_dir).'adt_custom_css.css';


    /* write into file */
    if(!$wp_filesystem->put_contents($target_file, $csstext, FS_CHMOD_FILE))
	{
		return new WP_Error('writing_error', 'Error when writing file'); //return error object
	}
	else
	{
		//update css version
		$css_version=1;
		$adt_css_version=get_option('adt_css_version');
		if($adt_css_version==FALSE)
		{

			$deprecated = null;
		    $autoload = 'no';
		    add_option( 'adt_css_version', $css_version, $deprecated, $autoload );
		}
		else
		{
			$css_version=$adt_css_version+1;
			update_option( 'adt_css_version', $css_version );
		}
	}
    return $csstext;
}

/**
 * Read text from file
 *
 * @param str $form_url - URL of the page where request form will be displayed
 * @return bool/str - false on failure, stored text on success
 **/
function fn_adt_css_read($form_url)
{
    global $wp_filesystem;

    $csstext = '';

    $form_url = wp_nonce_url($form_url, 'adt_css_save_nonce');
    $method = ''; //leave this empty to perform test for 'direct' writing

    //$adt_css_folder_path=ADT_PLUGIN_DIR_PATH.'assets/css/';//ADT_PLUGIN_DIR_PATH
    $context = ADT_PLUGIN_DIR_PATH.'assets/css/'; //target folder

    if(!fn_adt_filesystem_init($form_url, $method, $context)) {
        return false; //stop further processign when request formis displaying
    }

    /*
     * now $wp_filesystem could be used
     * get correct target file first
     **/
    $target_dir = $wp_filesystem->find_folder($context);
    $target_file = trailingslashit($target_dir).'adt_custom_css.css';


    /* read the file */
    if($wp_filesystem->exists($target_file)){ //check for existence

        $csstext = $wp_filesystem->get_contents($target_file);
        if(!$csstext) {
            return new WP_Error('reading_error', 'File is empty.'); //return error object
        }

    }

    return $csstext;
}

function fn_adt_admin_js_content()
{
	$form_url = 'admin.php?page='.PLUGIN_ADMIN_PAGE_SLUG.'&tab=javascript';
	$output = $error = '';

	/**
	 * write submitted text into file (if any)
	 * or read the text from file - if there is no submission
	 **/
	if(isset($_POST['txt_adt_js'])){//new submission

	    if(false === ($output = fn_adt_js_write($form_url))){
	        return; //we are displaying credentials form - no need for further processing

	    } elseif(is_wp_error($output)){
	        $error = $output->get_error_message();
	        $output = '';
	    }

	} else {//read from file

	    if(false === ($output = fn_adt_js_read($form_url))){
	        return; //we are displaying credentials form no need for further processing

	    } elseif(is_wp_error($output)) {
	        $error = $output->get_error_message();
	        $output = '';
	    }
	}

	$output = $output; //escaping for printing

	$str_error='';
	if($error)
	{
		$str_error='<div class="error below-h2">'.$error.'</div>';
	}
	$str_return='
		<div class="postbox">
		    <h3 class="hndle"><span>Custom Adore Datatables Javascript (Global)</span></h3>
		    <div class="inside">
		        <p class="alternate">Write your custom JS Code here. This Javascript will be loaded on every page/post where Adore Datatable instance exists.</p>
		        '.$str_error.'
				<form method="post" action="">
					'.wp_nonce_field('adt_js_save_nonce').'
					<fieldset class="form-table">
					    <label for="txt_adt_js">Write your custom Javascript Code Below</label><br>
					    <textarea id="txt_adt_js" name="txt_adt_js" rows="8" class="large-text">'.$output.'</textarea>
					</fieldset>
					<input type="submit" value="Save Javascript" class="button button-primary" id="cmd_adt_js_submit" name="cmd_adt_js_submit">
				</form>
		    </div>
		</div>
	';
	return $str_return;
}

function fn_adt_js_read($form_url)
{
    global $wp_filesystem;

    $jstext = '';

    $form_url = wp_nonce_url($form_url, 'adt_js_save_nonce');
    $method = ''; //leave this empty to perform test for 'direct' writing

    $context = ADT_PLUGIN_DIR_PATH.'assets/js/'; //target folder

    if(!fn_adt_filesystem_init($form_url, $method, $context))
        return false; //stop further processign when request formis displaying


    /*
     * now $wp_filesystem could be used
     * get correct target file first
     **/
    $target_dir = $wp_filesystem->find_folder($context);
    $target_file = trailingslashit($target_dir).'adt_custom_js.js';


    /* read the file */
    if($wp_filesystem->exists($target_file))
    { //check for existence
        $jstext = $wp_filesystem->get_contents($target_file);
        if(!$jstext)
            return new WP_Error('reading_error', 'File is empty!'); //return error object

    }

    return $jstext;
}

function fn_adt_js_write($form_url)
{
    global $wp_filesystem;

    check_admin_referer('adt_js_save_nonce');

    $jstext = esc_textarea($_POST['txt_adt_js']); //sanitize the input
    $form_fields = array('txt_adt_js'); //fields that should be preserved across screens
    $method = ''; //leave this empty to perform test for 'direct' writing

    $context = ADT_PLUGIN_DIR_PATH.'assets/js/'; //target folder

    $form_url = wp_nonce_url($form_url, 'adt_js_save_nonce'); //page url with nonce value

    if(!fn_adt_filesystem_init($form_url, $method, $context, $form_fields))
        return false; //stop further processign when request form is displaying


    /*
     * now $wp_filesystem could be used
     * get correct target file first
     **/
    $target_dir = $wp_filesystem->find_folder($context);
    $target_file = trailingslashit($target_dir).'adt_custom_js.js';


    /* write into file */
    if(!$wp_filesystem->put_contents($target_file, $jstext, FS_CHMOD_FILE))
	{
		return new WP_Error('writing_error', 'Error when writing file'); //return error object
	}
	else
	{
		//update custom js version
		$js_version=1;
		$adt_js_version=get_option('adt_js_version');
		if($adt_js_version==FALSE)
		{
                    $deprecated = null;
		    $autoload = 'no';
		    add_option( 'adt_js_version', $js_version, $deprecated, $autoload );
		}
		else
		{
			$js_version=$adt_js_version+1;
			update_option( 'adt_js_version', $js_version );
		}
	}
    return $jstext;
}


/**
 * Javascript strings for translation
 * @return array
 * @since 0.0.7
 * @author lenasterg
 */
function fn_adt_admin_js_strings() {
    $strings = array(
        'table_no_space' => __("Table ID text cannot have space." , 'adt') ,
        'selectTable' => __("Please select a Database Table." , 'adt') ,
        'inputDTname' => __("Please input datatable name." , 'adt') ,
        'colPosEmpty' => __("Table Column position cannot be empty. Please check your input and try again." , 'adt') ,
        'colPosNoNum' => __("Table Column position is not a number. Please check your input and try again." , 'adt') ,
        'colPosDuplicate' => __("Table Column position cannot be duplicate. Please check your input and try again." , 'adt') ,
        'colPosNegative' => __("Table Column position cannot be a negative number. Please check your input and try again." , 'adt') ,
        'colPosDecimal' => __("Table Column position cannot be a decimal number. Please check your input and try again." , 'adt') ,
        'colPosOutOfRange' => __("Table Column position cannot exceed total columns numbers. Please check your input and try again." , 'adt') ,
        'DeleteThis' => __("Delete this Datatable?" , 'adt') ,
    );

    return $strings;
}