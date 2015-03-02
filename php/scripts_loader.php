<?php

add_action('init', 'fn_adt_register_scripts');
add_action('wp_footer', 'fn_print_adt_scripts');

function fn_adt_register_scripts() 
{
	fn_adt_register_styles();
	fn_adt_register_javascripts();
}

function fn_print_adt_scripts() 
{
	//Reference "The Jedi Knight way" section - http://scribu.net/wordpress/optimal-script-loading.html
	global $add_adt_scripts;

	if ($add_adt_scripts)
	{
		wp_enqueue_style('jqueryui_css');	
		wp_enqueue_style('datatable_css');
		wp_enqueue_style('datatable_themeroller');
		
		wp_enqueue_script('jqueryui_js');
		wp_enqueue_script('datatable_js');
		wp_enqueue_script('demo_datatable_js');
		wp_localize_script('demo_datatable_js', 'AdoreDTAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}
}

/**
 * Function to register CSS Files
 */
function fn_adt_register_styles()
{
	//style registration
	
	//https://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css
	//https://cdn.datatables.net/plug-ins/f2c75b7247b/integration/jqueryui/dataTables.jqueryui.css
	//wp_register_style('jqueryui_css', 'https://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css');
	//wp_register_style('datatable_css', 'https://cdn.datatables.net/plug-ins/f2c75b7247b/integration/jqueryui/dataTables.jqueryui.css');
	wp_register_style('jqueryui_css', ADORE_DATATABLES_PLUGIN_URL.'/assets/css/ui-lightness/jquery-ui-1.9.2.custom.min.css');
	wp_register_style('datatable_css', ADORE_DATATABLES_PLUGIN_URL.'/assets/datatables-1.10.1/css/jquery.dataTables.min.css');
	wp_register_style('datatable_themeroller', ADORE_DATATABLES_PLUGIN_URL.'/assets/datatables-1.10.1/css/jquery.dataTables_themeroller.css');	
	
}

/**
 * Function to register JS
 */
function fn_adt_register_javascripts()
{
	//scripts registration here
	$static_version='1.1'; //used for js which will never be modified	
	$dynamic_version='1.1'; //used for js which will be modified	
	
	//https://cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js
	//https://cdn.datatables.net/plug-ins/f2c75b7247b/integration/jqueryui/dataTables.jqueryui.js
	
	//Plugin Files
	//wp_register_script('jqueryui_js', 'https://cdn.datatables.net/plug-ins/f2c75b7247b/integration/jqueryui/dataTables.jqueryui.js', array(jquery), $static_version,TRUE);
	//wp_register_script('datatable_js', 'https://cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js', array(jquery), $static_version,TRUE);
	
	wp_register_script('jqueryui_js', ADORE_DATATABLES_PLUGIN_URL.'/assets/js/jquery-ui-1.9.2.custom.min.js', array(jquery), $static_version,TRUE);
	wp_register_script('datatable_js', ADORE_DATATABLES_PLUGIN_URL.'/assets/datatables-1.10.1/js/jquery.dataTables.min.js', array(jquery), $static_version,TRUE);
		 
	wp_register_script('demo_datatable_js', ADORE_DATATABLES_PLUGIN_URL.'/assets/js/demo_datatables.js', array(jquery), $dynamic_version,TRUE);
	
	
}