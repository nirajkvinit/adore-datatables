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
	        		<h3>Welcome to Adore Datatables! (A jQuery Datatables based wordpress plugin)</h3>
	        		<h2 class="nav-tab-wrapper">
						<a class="nav-tab'.$str_manage_tab_active.'" href="?page='.PLUGIN_ADMIN_PAGE_SLUG.'&tab=manage">Manage</a>
						<a class="nav-tab'.$str_css_tab_active.'" href="?page='.PLUGIN_ADMIN_PAGE_SLUG.'&tab=css">CSS</a>
						<a class="nav-tab'.$str_javascript_tab_active.'" href="?page='.PLUGIN_ADMIN_PAGE_SLUG.'&tab=javascript">Javascript</a>
						<a class="nav-tab'.$str_settings_tab_active.'" href="?page='.PLUGIN_ADMIN_PAGE_SLUG.'&tab=settings">Settings</a>
					</h2>
					'.$str_content.'
	        	</div>
	        </div>
		</div>
	';
	echo $str_return;
}

function fn_adt_admin_main_content() 
{
	global $wpdb;
	
	$str_adt_options='
		<select id="select_adt_table">
			<option value="select">Select a Datatable</option>
			<option value="create">Create a Datatable</option>			
	';
	
	$datatables_db=$wpdb->get_results("SELECT adt_id, adt_table_name, adt_table_slug FROM adore_datatable_settings");
	
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
		<table width="100%">
        	<tr>
        		<td align="right">Select or Create a Datatable:</td>
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

function fn_adt_admin_css_content()
{
	$str_return='
		<br />
		<p class="about-description">Write your custom CSS code here. This code will be loaded on every page/post where Adore Datatable instance exists.</p>
		<textarea name="" id="txt_adt_custom_css" style="width:100%;" rows="20"></textarea>
		<table width="100%">
			<tr>
				<td><img id="adt_custom_css_save_loader" src="'.plugins_url('/assets/images/loader.gif', dirname(__FILE__)).'" style="display:none;"/></td>
				<td align="right"><input type="button" class="button button-primary" id="cmd_adt_datatable_save_css" value="Save CSS" /></td>
			</tr>
		</table>
	';
	return $str_return;		
}

function fn_adt_admin_js_content()
{
	$str_return='
		<br />
		<div class="postbox">
		    <h3 class="hndle"><span>Custom Adore Datatables Javascript (Global)</span></h3>
		    <div class="inside">
		        <p class="about-description">Write your custom Javascript code here. This code will be loaded on every page/post where Adore Datatable instance exists.</p>
				<textarea name="" id="txt_adt_custom_javascript" style="width:100%;" rows="20"></textarea>
				<table width="100%">
					<tr>
						<td><img id="adt_custom_js_save_loader" src="'.plugins_url('/assets/images/loader.gif', dirname(__FILE__)).'" style="display:none;"/></td>
						<td align="right"><input type="button" class="button button-primary" id="cmd_adt_datatable_save_js" value="Save Javascript" /></td>
					</tr>
				</table>
		    </div>
		</div>
	';
	return $str_return;	
}

function fn_adt_admin_settings_content()
{
	$str_table_styles='
		<select id="select_adt_styles" style="width:100%">
			<option value="base_style">Base style</option>
			<option value="base_style_noclass">Base style - no styling classes</option>
			<option value="base_style_cell_borders">Base style - cell borders</option>
			<option value="base_style_compact">Base style - compact</option>
			<option value="base_style_hover">Base style - hover</option>
			<option value="base_style_order_column">Base style - order-column</option>
			<option value="base_style_row_borders">Base style - row borders</option>
			<option value="base_style_stripe">Base style - stripe</option>
			<option value="bootstrap">Bootstrap</option>
			<option value="jqueryui_themeroller">jQuery UI ThemeRoller</option>			
		</select>
	';
	$str_return='
		<br />
		<table width="100%">
			<tr>
				<td><label><input type="checkbox" id="chk_adt_custom_css_file">Load Custom CSS as file</label></td>
        		<td><label><input type="checkbox" id="chk_adt_custom_js_file">Load Custom JS as file</label></td>
				<td align="right"><label for="select_adt_styles" title="Datatable styles">Styling</label></td>
				<td>'.$str_table_styles.'</td>
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
	$str_return='';
		
	if(is_numeric($adt_id))
	{
		$str_return=fn_return_adt_form($adt_id);
	}
	else if($adt_id=='create')
	{
		$str_return=fn_create_new_adt_form();
	}
	else
	{
		echo '<div class="error">Error! Input was incorrect! Please try again.</div>';
		die();		
	}

	echo $str_return;
	die();
}

/**
 * Function to return datatable information for shortcode or for update or delete.
 */
function fn_return_adt_form($adt_id)
{
	$str_return='Under Construction!';
	return $str_return;
}

function fn_create_new_adt_form()
{
	global $wpdb;
	
	$tables_sql="select table_name from information_schema.tables where table_schema='".$wpdb->dbname."';";
	$tables_db=$wpdb->get_results($tables_sql);
	
	$str_adt_table_option='
		<select id="select_adt_db_tables_list" style="width:100%">
			<option value="select">Select a Table</option>
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
			<option value="server">Server-side processing</option>
			<option value="html">HTML (DOM) sourced data</option>
		</select>
	';
	$str_pagination_type='
		<select id="select_adt_pagination" style="width:100%">
			<option value="full_numbers">Full Numbers</option>
			<option value="full">Full</option>
			<option value="simple_numbers">Simple Numbers</option>
			<option value="simple">Simple</option>
		</select>
	';
	
			
	$str_return='
		<div class="welcome-panel">
        	<div class="welcome-panel-content">
        		<h3>Create a new Adore Datatable!</h3><hr />
        		<table width="100%">
        			<tr>
        				<th><label for="txt_adt_datatable_name" title="Name using which you would recognize your datatable instance">Datatable Name</label></th>
        				<th><label for="txt_adt_table_id" title="CSS ID attribute for the HTML Table">Table ID (Optional)</label></th>
        				<th><label for="txt_adt_table_class" title="Additional class for the HTML Table">Table Class (Optional)</label></th>
        			</tr>
        			<tr>        				
        				<td><input type="text" id="txt_adt_datatable_name" style="width:100%" /></td>
        				<td><input type="text" id="txt_adt_table_id" style="width:100%" /></td>
        				<td><input type="text" id="txt_adt_table_class" style="width:100%" /></td>
        			</tr>
        			<tr>
        				<th><label for="select_adt_table_type" title="HTML Table or Server Side Ajaxed. Warning: Too many records may freeze your browser if you select HTML (DOM) sourced data.">Table Type</label></th>
        				<th><label for="select_adt_pagination" title="Datatable Pagination Types">Pagination</label></th>
        				<th><label for="select_adt_db_tables_list">Select a Database Table</label></th>
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
        				<td><label><input type="checkbox" id="chk_adt_allow_search" checked>Allow Search</label></td>
        				<td><label><input type="checkbox" id="chk_adt_ordering" checked>Ordering</label></td>
        				<td><label><input type="checkbox" id="chk_adt_showinfo" checked>Show Info</label></td>
        			</tr>
        			<tr>
        				<td><label><input type="checkbox" id="chk_adt_autowidth">bAutoWidth</label></td>
        				<td><label><input type="checkbox" id="chk_adt_scrollvertical">Scroll Vertical</label></td>
        				<td><label><input type="checkbox" id="chk_adt_individual_column_filtering">Individual Column Filtering</label></td>
        			</tr>
        			<tr>
        				<th colspan="3"><label for="txt_adt_sdom" title="Datatable DOM Positioning (Leave it blank if you do not know what you are doing.)">sDom</label></th>
        			</tr>
        			<tr>
        				<td colspan="3"><input type="text" id="txt_adt_sdom" style="width:100%" /></td>
        			</tr>        			
        			<tr>
        				<th colspan="3"><label for="txt_adt_fnrowcallback" title="Extra options while datatable is rendering rows (Leave it blank if you do not know what you are doing.)">fnRowCallback</label></th>
        			</tr>
        			<tr>
        				<td colspan="3"><textarea name="" id="txt_adt_fnrowcallback" style="width:100%" rows="5"></textarea></td>
        			</tr>
        			<tr>
        				<td colspan="2"><img id="adt_table_save_loader" src="'.plugins_url('/assets/images/loader.gif', dirname(__FILE__)).'" style="display:none;"/></td>
        				<td align="right"><input type="button" class="button button-primary" id="cmd_create_adt_datatable" value="Save Datatable" /></td>
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
	
	$str_return='Column select error!';
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
						<th>Name</th>
						<th>Title</th>
						<th>Visible</th>					
						<th>Searchable</th>
						<th>Sortable</th>
						<th title="sClass">CSS Class(es)</th>
						<th title="Column Position">Column Position</th>
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
					<td><input type="text" class="txt_adt_column_sclass" style="width:100%" /></td>
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

add_action('wp_ajax_fn_adt_table_save_ajax', 'fn_adt_table_save_ajax');
function fn_adt_table_save_ajax()
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
	$result_array['result_message']='Datatable could not be saved. Please try again!';
	
	
	
	$nonce_result=wp_verify_nonce( $adt_nonce, PLUGIN_ADMIN_PAGE_SLUG);
	if(!$nonce_result)
	{
		$result_array['result_message']='Security Error! Security could not be validated! Please try again!';	
		echo json_encode($result_array);	
		die();	
	}
	
	
	if(empty($datatable_name))
	{		
		$result_array['result_message']='Error! Please provide datatable name and try again.';
		echo json_encode($result_array);
		die();
	}
	
	if(empty($database_table_name))
	{		
		$result_array['result_message']='Error! Please select a database table/view and try again.';
		echo json_encode($result_array);
		die();
	}
	
	$datatable_slug=sanitize_title($datatable_name);
	
	//check if datatable name is duplicate or not. we do not allow duplicate as the adore datatable shortcode will be available via ID or SLUG.
	
	$is_duplicate_adt=$wpdb->get_results("select adt_table_slug from adore_datatable_settings where adore_datatable_settings='$datatable_slug'");
	if(!empty($is_duplicate_adt))
	{
		$result_array['result_message']='Error! Datatable name cannot be duplicate. Please input another name and try again.';
		echo json_encode($result_array);
		die();
	}
	
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
	
	fn_applog($datatable_json);
	
	$insert_array=array(
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
