<?php
add_shortcode( 'adore-datatables', 'fn_adore_datatables_shortcode' );

 /**
 * Shortcode tags are:
 * [adore-datatables id="1"] 
 * Or 
 * [adore-datatables table="adore-demo"] 
 */
function fn_adore_datatables_shortcode($atts, $content = null)
{
	extract(shortcode_atts(array(
        'id' => null,
        'table' => null
    ), $atts));
	
	$str_return=fn_adore_datatables_maker($id,$table);
	
	return $str_return;
}

function fn_adore_datatables_maker($id,$table)
{
	global $wpdb;
	
	$table=sanitize_text_field($table);
	
	if(is_null($id) && is_null($table))
	{
		return '<div class="error">Error! You have not provided Adore Datatable ID or Name in your shortcode tag.</div>';
	}
	if(!is_null($id) && !is_numeric($id))
	{
		return '<div class="error">Error! Adore Datatable ID is incorrect. Please check the shortcode tag.</div>';
	}
	
	$adt_datatable_dataset=$wpdb->get_results("SELECT * FROM adore_datatable_settings where adt_id=$id or adt_table_slug='$table'");
	if(empty($adt_datatable_dataset))
	{
		return '<div class="error">Error! Adore Datatable information not found in the database. Please check the shortcode tag.</div>';
	}
	return 'Hello Datatable.';
}


/*<!--<textarea name="" id="txt_adt_custom_css" style="width:100%;" rows="20">
		</textarea>
		<input type="hidden" id="hidden_adt_css_nonce" val="'.wp_nonce_field('adore-datatable-css').'" />
		<table width="100%">
			<tr>
				<td><img id="adt_custom_css_save_loader" src="'.plugins_url('/assets/images/loader.gif', dirname(__FILE__)).'" style="display:none;"/></td>
				<td align="right"><input type="button" class="button button-primary" id="cmd_adt_datatable_save_css" value="Save CSS" /></td>
			</tr>
		</table>-->*/