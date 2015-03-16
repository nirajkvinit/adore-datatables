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
	
	
	
	return 'Hello Adore Datatable. Adore Datatable is under construction! Please wait for the version 0.0.3';
}
