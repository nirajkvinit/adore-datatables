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
	global $add_demo_adt_scripts;

	if ($add_demo_adt_scripts)
	{
		//Web Assets
		wp_enqueue_style('datatable_css');
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
	$static_version='1.1'; //used for css which will never be modified	
	$dynamic_version='1.2'; //used for css which will be modified
	//Web Assets	
	wp_register_style('datatable_css', 'http://cdn.datatables.net/1.10.5/css/jquery.dataTables.css',array(),$static_version,'all');
	wp_register_style('bootstrap_datatable_css', 'http://cdn.datatables.net/plug-ins/f2c75b7247b/integration/bootstrap/3/dataTables.bootstrap.css',array(),$static_version,'all');
	wp_register_style('jqueryui_datatable_css', 'http://cdn.datatables.net/plug-ins/f2c75b7247b/integration/jqueryui/dataTables.jqueryui.css',array(),$static_version,'all');
	
	//Admin Panel should have option whether the following should be loaded or not. Admin Panel should have more options
	wp_register_style('bootstrap_css', 'http://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',array(),$static_version,'all');
	wp_register_style('jqueryui_css', 'http://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css',array(),$static_version,'all');
}

/**
 * Function to register JS
 */
function fn_adt_register_javascripts()
{
	//scripts registration here
	$static_version='1.1'; //used for js which will never be modified	
	$dynamic_version='1.2'; //used for js which will be modified		
	
	//Web Assets
	wp_register_script('datatable_js', 'http://cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js', array(jquery), $static_version,TRUE);
	wp_register_script('bootstrap_datatable_js', 'http://cdn.datatables.net/plug-ins/f2c75b7247b/integration/bootstrap/3/dataTables.bootstrap.js', array(jquery), $static_version,TRUE);	
	wp_register_script('jqueryui_datatable_js', 'http://cdn.datatables.net/plug-ins/f2c75b7247b/integration/jqueryui/dataTables.jqueryui.js', array(jquery), $static_version,TRUE);
	
	
	//Javascript for Demo Adore Datatable
	wp_register_script('demo_datatable_js', plugins_url('/assets/js/demo_datatables.js', dirname(__FILE__) ), array(jquery), $dynamic_version,TRUE); //Ref. http://codex.wordpress.org/Function_Reference/plugins_url	
	
	//Javascript for Adore Datatable Admin Control Panel
	wp_register_script('datatables_admin_js', plugins_url('/assets/js/adore-datatables-admin.js', dirname(__FILE__) ), array(jquery), $dynamic_version,TRUE); //Ref. http://codex.wordpress.org/Function_Reference/plugins_url	
}

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