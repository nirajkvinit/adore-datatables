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
	//style registration	
	
	//Web Assets
	wp_register_style('datatable_css', 'http://cdn.datatables.net/1.10.5/css/jquery.dataTables.css');	
}

/**
 * Function to register JS
 */
function fn_adt_register_javascripts()
{
	//scripts registration here
	$static_version='1.1'; //used for js which will never be modified	
	$dynamic_version='1.1'; //used for js which will be modified		
	
	//Web Assets
	wp_register_script('datatable_js', 'http://cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js', array(jquery), $static_version,TRUE);
	wp_register_script('demo_datatable_js', plugins_url('/assets/js/demo_datatables.js', dirname(__FILE__) ), array(jquery), $dynamic_version,TRUE); //Ref. http://codex.wordpress.org/Function_Reference/plugins_url
	
	
}