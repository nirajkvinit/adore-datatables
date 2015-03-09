<?php

add_action('admin_enqueue_scripts', 'fn_adt_admin_load_scripts');
function fn_adt_admin_load_scripts($hook) 
{ 
	global $adt_settings_page;	
 
	if( $hook != $adt_settings_page ) //ref. https://pippinsplugins.com/loading-scripts-correctly-in-the-wordpress-admin/
	{
		return;		
	} 
	
 	wp_enqueue_script('datatables_admin_js');
}

add_action('admin_menu', 'fn_adt_settings_menu');
function fn_adt_settings_menu() 
{	
	global $adt_settings_page;
    $adt_settings_page=add_menu_page("Adore Datatables", "Adore Datatables", 'manage_options', 'adore-datatables', 'fn_adore_datatables_main_settings_page', 'dashicons-media-spreadsheet' );
}

function fn_adore_datatables_main_settings_page() 
{
	global $wpdb;
	
	$str_adt_options='
		<select id="select_adt_table">
			<option value="select">Select a Datatable</option>
			<option value="create">Create a Datatable</option>			
	';
	
	$datatables_db=$wpdb->get_results("SELECT adt_id, adt_table_name FROM adore_datatable_settings");
	
	foreach($datatables_db as $datatables)
	{
		$str_adt_options.='
			<option value="'.$datatables->adt_id.'">'.$datatables->adt_table_name.'</option>
		';
	}
	
	$str_adt_options.='
		</select>
	';
	
    $str_return='
    	<div class="wrap">
	        <h2>Adore Datatables</h2>
	        <div class="welcome-panel">
	        	<div class="welcome-panel-content">
	        		<h3>Welcome to Adore Datatables! (A jQuery Datatables based wordpress plugin)</h3>
	        		<!--<p class="about-description">Adore datatables is a jQuery Datatables based wordpress plugin.</p>
	        		<p>Using this you can easily create a datatables compatible table where data can be sourced directly from html table or database.</p>-->
	        		<hr />
	        		<table width="100%">
			        	<tr>
			        		<td align="right">Select or Create a Datatable:</td>
			        		<td>'.$str_adt_options.'</td>	        		
			        	</tr>
			        </table>
	        	</div>
	        </div>
	        
	        <hr />
	        <img id="adt_settings_loader" src="'.plugins_url('/assets/images/loader.gif', dirname(__FILE__)).'" style="display:none;"/>	        
	        <div id="div_adt_settings_area"></div>	        
    	</div>
    ';
	echo $str_return;
}


add_action('wp_ajax_fn_load_dt_settings_ajax', 'fn_load_dt_settings_ajax');

function fn_load_dt_settings_ajax()
{
	global $wpdb;
	$adt_id=$_POST['adt_id'];
	$str_return='';
	
	/*$tables_sql="select table_name from information_schema.tables where table_schema='".$wpdb->dbname."';";
	$tables_db=$wpdb->get_results($tables_sql);
	fn_applog(print_r($tables_db, TRUE));
	echo 'All Done.';
	die();*/
	
	
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
		echo 'Error! Input was incorrect! Please try again.';
		die();		
	}

	echo $str_return;
	die();
}

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
		<div class="welcome-panel">
        	<div class="welcome-panel-content">
        		<h3>Create a new Adore Datatable!</h3><hr />
        		<table width="100%">
        			<tr>
        				<th><label for="txt_adt_name" title="Name using which you would recognize your datatable instance">Datatable Name</label></th>
        				<th><label for="txt_adt_id" title="ID attribute for the HTML Table">Datatable ID</label></th>
        				<th><label for="txt_adt_class" title="Additional class for the HTML Table">Datatable Class</label></th>
        			</tr>
        			<tr>        				
        				<td><input type="text" id="txt_adt_name" style="width:100%" /></td>
        				<td><input type="text" id="txt_adt_id" style="width:100%" /></td>
        				<td><input type="text" id="txt_adt_class" style="width:100%" /></td>
        			</tr>
        			<tr>
        				<th><label for="select_adt_table_type" title="HTML Table or Server Side Ajaxed. Warning: Too many records may freeze your browser if you select HTML (DOM) sourced data.">Table Type</label></th>
        				<th><label for="select_adt_pagination" title="Datatable Pagination Types">Pagination</label></th>
        				<th><label for="select_adt_styles" title="Datatable styles">Styling</label></th>
        			</tr>
        			<tr>
        				<td>'.$str_table_type.'</td>
        				<td>'.$str_pagination_type.'</td>
        				<td>'.$str_table_styles.'</td>
        			</tr>
        			<tr>
        				<td align="right"><label><input type="checkbox" id="chk_adt_allow_search" checked>Allow Search</label></td>
        				<td align="right">Select a Table</td>
        				<td>'.$str_adt_table_option.'</td>
        			</tr>
        			<tr>
        				<td colspan="3"><div id="div_adt_table_columns_options"></div></td>
        			</tr>        			
        		</table>
        		<hr />
        		<p class="about-description" style="text-align:center;">Advanced Options (Leave as it is if you do not know what you are doing).</p>
        		<hr />
        		<table width="100%">
        			<tr>
        				<td><label><input type="checkbox" id="chk_adt_paging" checked>Paging</label></td>
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
        				<td align="right"><input type="button" id="cmd_create_adt_datatable" value="Save Datatable" /></td>
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
	fn_applog(print_r($columns_db, TRUE));
	if(!empty($columns_db))
	{
		$str_return='
			<hr />
			<table width="100%">
				<tr>
					<th>Column Name</th>
					<th>Visible</th>					
					<th>Searchable</th>
					<th>Sortable</th>
					<th title="sClass">Class</th>
				</tr>
		';
		
		foreach($columns_db as $columns)
		{
			$column_name=$columns->COLUMN_NAME;
			$str_return.='
				<tr class="row_adt_column_data">
					<th>'.$column_name.'</th>
					<td align="center"><input type="checkbox" class="chk_adt_column_visible" checked /></td>
					<td align="center"><input type="checkbox" class="chk_adt_column_searchable" checked /></td>
					<td align="center"><input type="checkbox" class="chk_adt_column_sortable" checked /></td>
					<td><input type="text" class="txt_adt_column_sclass" style="width:100%" /></td>
				</tr>
			';
		}
		$str_return.='
			</table>
		';
	}	
	echo $str_return;
	die();
	
}
