<?php

/**
 * Function to register Javascript and CSS Files for Adore Datatables
 */
add_action('init', 'fn_adt_register_scripts');

function fn_adt_register_scripts() 
{
	fn_adt_register_styles();	
	fn_adt_register_javascripts();
}

/**
 * Enqueue Javascript and CSS files for Adore Datatables
 */
add_action('wp_footer', 'fn_print_adt_scripts');

								 
																									 
															  

function fn_print_adt_scripts() 
{
	//Reference "The Jedi Knight way" section - http://scribu.net/wordpress/optimal-script-loading.html
	global $add_demo_adt_scripts, $adt_global, $adt_options;
	
	$theme='';

								  
				  
										 

							  
										  
										  

						
											   
																												  
	 

															
											  
											   
												   
														 

							 
							 
													 
													  
				 
															
												  
															
					  
										
													 
														 
													 
				 
														   
												  
														   
					  
					
												  
												  
					  
		 

						   
										   

											   
							   
													
										   

																											  

		   
																											
																										   
		   
	 

	if ($add_demo_adt_scripts)
	{
		//optional
		wp_enqueue_style('jqueryui_css');
		
		//Datatable css and js
		wp_enqueue_style('datatable_css');
		wp_enqueue_script('datatable_js');
		
		//demo datatable
		wp_enqueue_script('demo_datatable_js');
		wp_localize_script('demo_datatable_js', 'AdoreDTAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}
	
	if(is_array($adt_global) && is_array($adt_options))
	{
		$theme=$adt_options['jquery_theme'];
		$adt_style=$adt_options['adt_style'];
		$load_jquery=$adt_options['load_jquery'];
		$load_bootstrap=$adt_options['load_bootstrap'];
		
		switch($adt_style)
		{
			case 'bootstrap':
				if($load_bootstrap=='enabled')
				{
					wp_enqueue_style('bootstrap_css');
				}
				wp_enqueue_style('bootstrap_datatable_css');
				wp_enqueue_script('datatable_js');
				wp_enqueue_script('bootstrap_datatable_js');
				break;
			case 'jqueryui_themeroller':
				if($load_bootstrap=='enabled')
				{
					fn_adt_register_jqueryui_css($theme);
					wp_enqueue_style('jqueryui_css');
				}
				wp_enqueue_style('jqueryui_datatable_css');
				wp_enqueue_script('datatable_js');
				wp_enqueue_script('jqueryui_datatable_js');
				break;
			default:
				wp_enqueue_style('datatable_css');
				wp_enqueue_script('datatable_js');				
				break;
		}
		
		//Custom CSS and JS
		wp_enqueue_style('adt_custom_css');
		
		//Load Javascript to make the datatable		
		fn_adt_inline_script();
		//Load custom script to handle the datatable
		wp_enqueue_script('adt_custom_js');
		
		wp_localize_script('adt_custom_js', 'AdoreDTAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		
		/**
		 * Ref http://wordpress.stackexchange.com/questions/24851/wp-enqueue-inline-script-due-to-dependancies
		 * http://wordpress.stackexchange.com/questions/32388/solutions-for-generating-dynamic-javascript-css
		 */
	}
		
	//print_my_inline_script();
	
}

/**
 * Dynamically generated Javascript code to handle Adore Datatables
 */
function fn_adt_inline_script()
{
	global $post, $adt_global, $adt_options;

	   
																								 
	   
						 

															
											   

										   


	/**
	 * First make the HTML Datatable then consider making the server side datatable by javascript
	 */
	$post_id=$post->ID; 
	 
	if(is_array($adt_global) && is_array($adt_options))
	{
		$adt_style=$adt_options['adt_style'];
			
		$adt_array=$adt_global[$post_id];
		

		$str_additional_script='';
		
		$str_return='
			<script type="text/javascript">
				$=jQuery;
				jQuery(document).ready(function($) 
				{
		';
		foreach($adt_array as $key=>$value)
		{
			//call function to create server processing files
			$datatable_json=json_encode($value);
			
			  
								
								   
			   
			fn_adt_server_side_files_maker($key,$datatable_json);			
			

							   
												
											   
														 
												   
													   
											 
														   
														 
																				 
								   
														 
													 

									 
												   
										
			 

										
											   
										   
			 
									 
											
									
			 

										  
												 
											 
			 

						  
								  
												   
			 

			$table_slug=$key;
			$table_id=$value['html_table_id'];
			$table_type=$value['table_type'];
			$pagination_type=$value['pagination_type'];
			$allow_search=$value['allow_search'];
			$allow_ordering=$value['allow_ordering'];
			$show_info=$value['show_info'];
			$allow_auto_width=$value['allow_auto_width'];
			$scroll_vertical=$value['scroll_vertical'];
			$individual_column_filtering=$value['individual_column_filtering'];
			$sdom=$value['sdom'];
			$fn_row_callback=$value['fn_row_callback'];
			$columns_array=$value['columns_array'];
			
			$str_autowidth='false';
			if($allow_auto_width=='enabled')
			{
				$str_autowidth='true';
			}
			
			$str_allow_search='false';
			if($allow_search=='enabled')
			{
				$str_allow_search='true';
			}
			$str_show_info='false';
			if($show_info=='enabled')
			{
				$show_info='true';
			}
			
			$str_allow_ordering='false';
			if($allow_ordering=='enabled')
			{
				$str_allow_ordering='true';
			}
			
			$str_dom='';
			if(!empty($sdom))
			{
				$str_dom='"dom": '.$sdom.',';
			}
			
			$str_row_callback='';
			if(!empty($fn_row_callback))
			{
				$str_row_callback='
					"fnRowCallback": function( nRow, aData, iDisplayIndex ) 
			         {
			         	'.$fn_row_callback.'			         	
				     }';
			}
			
			$str_col_defs='
				"columnDefs": [
			';
			
			foreach($columns_array as $col_key=>$col_value)
			{
				$is_visible='';
				if($col_value['is_visible']=='disabled')
				{
					$is_visible='"visible": false,';
				}
				$is_searchable='';
				if($col_value['is_searchable']=='disabled')
				{
					$is_searchable='"searchable": false,';
				}
				$is_sortable='';
				if($col_value['is_sortable']=='disabled')
				{
					$is_sortable='"orderable": false,';
				}
				
				$str_col_defs.='
					{
		                "targets": [ '.$col_key.' ],
		                '.$is_visible.'
		                '.$is_searchable.'
		                '.$is_sortable.'
		            },
				';
			}
			
			$str_col_defs.='],';
			
			
			if(empty($table_id))
			{
				$table_id=$table_slug;
			}
			$adt_table_id=str_replace('-', '_', $table_id);
			
			//more work pending
			if($table_type=='html')
			{
				$str_return.='
					var $'.$adt_table_id.'=$("#'.$table_id.'").dataTable(
					{
				        "bAutoWidth" : '.$str_autowidth.',
					    "pagingType": "'.$pagination_type.'",
					    "searching" : '.$str_allow_search.',
					    "info"	: '.$str_show_info.',
					    "ordering": '.$str_allow_ordering.',
					    '.$str_col_defs.'
					    '.$str_dom.'
					    '.$str_row_callback.'
				    });
				';
			}
			if($table_type=='server')
			{
				$str_return.='
					var $'.$adt_table_id.'=$("#'.$table_id.'").dataTable(
					{
				        "bAutoWidth" : '.$str_autowidth.',
					    "pagingType": "'.$pagination_type.'",
					    "searching" : '.$str_allow_search.',
					    "info"	: '.$str_show_info.',
					    "ordering": '.$str_allow_ordering.',
					    '.$str_col_defs.'
					    '.$str_dom.'
					    "bProcessing": true,
					    "bServerSide": true,
					    "sAjaxSource": AdoreDTAjax.ajaxurl+\'?action=fn_'.$adt_table_id.'_ajax\',	     
					    "bDeferRender": true,
					    "fnServerData": fn'.$adt_table_id.'Pipeline,
					    '.$str_row_callback.'
				    });
					
					
				';
				$str_additional_script.='
					var '.$adt_table_id.'_cache = {
						iCacheLower: -1
					};
					
					function fn'.$adt_table_id.'Pipeline ( sSource, aoData, fnCallback ) {
						var iPipe = 5; /* Ajust the pipe size */
						
						var bNeedServer = false;
						var sEcho = fnGetKey(aoData, "sEcho");
						var iRequestStart = fnGetKey(aoData, "iDisplayStart");
						var iRequestLength = fnGetKey(aoData, "iDisplayLength");
						var iRequestEnd = iRequestStart + iRequestLength;
						'.$adt_table_id.'_cache.iDisplayStart = iRequestStart;
						
						/* outside pipeline? */
						if ( '.$adt_table_id.'_cache.iCacheLower < 0 || iRequestStart < '.$adt_table_id.'_cache.iCacheLower || iRequestEnd > '.$adt_table_id.'_cache.iCacheUpper )
						{
							bNeedServer = true;
						}
						
						/* sorting etc changed? */
						if ( '.$adt_table_id.'_cache.lastRequest && !bNeedServer )
						{
							for( var i=0, iLen=aoData.length ; i<iLen ; i++ )
							{
								if ( aoData[i].name != "iDisplayStart" && aoData[i].name != "iDisplayLength" && aoData[i].name != "sEcho" )
								{
									if ( aoData[i].value != '.$adt_table_id.'_cache.lastRequest[i].value )
									{
										bNeedServer = true;
										break;
									}
								}
							}
						}
						
						/* Store the request for checking next time around */
						'.$adt_table_id.'_cache.lastRequest = aoData.slice();
						
						if ( bNeedServer )
						{
							if ( iRequestStart < '.$adt_table_id.'_cache.iCacheLower )
							{
								iRequestStart = iRequestStart - (iRequestLength*(iPipe-1));
								if ( iRequestStart < 0 )
								{
									iRequestStart = 0;
								}
							}
							
							'.$adt_table_id.'_cache.iCacheLower = iRequestStart;
							'.$adt_table_id.'_cache.iCacheUpper = iRequestStart + (iRequestLength * iPipe);
							'.$adt_table_id.'_cache.iDisplayLength = fnGetKey( aoData, "iDisplayLength" );
							fnSetKey( aoData, "iDisplayStart", iRequestStart );
							fnSetKey( aoData, "iDisplayLength", iRequestLength*iPipe );
							
							$.getJSON( sSource, aoData, function (json) { 
								/* Callback processing */
								'.$adt_table_id.'_cache.lastJson = jQuery.extend(true, {}, json);
								
								if ( '.$adt_table_id.'_cache.iCacheLower != '.$adt_table_id.'_cache.iDisplayStart )
								{
									json.aaData.splice( 0, '.$adt_table_id.'_cache.iDisplayStart-'.$adt_table_id.'_cache.iCacheLower );
								}
								json.aaData.splice( '.$adt_table_id.'_cache.iDisplayLength, json.aaData.length );
								
								fnCallback(json);
							} );
						}
						else
						{
							json = jQuery.extend(true, {}, '.$adt_table_id.'_cache.lastJson);
							json.sEcho = sEcho; /* Update the echo for each response */
							json.aaData.splice( 0, iRequestStart-'.$adt_table_id.'_cache.iCacheLower );
							json.aaData.splice( iRequestLength, json.aaData.length );
							fnCallback(json);
							return;
						}
					}
				';
			}
		}
		
		$str_return.='
				});
				'.$str_additional_script.'
				function fnSetKey( aoData, sKey, mValue )
				{
					for ( var i=0, iLen=aoData.length ; i<iLen ; i++ )
					{
						if ( aoData[i].name == sKey )
						{
							aoData[i].value = mValue;
						}
					}
				}
				
				function fnGetKey( aoData, sKey )
				{
					for ( var i=0, iLen=aoData.length ; i<iLen ; i++ )
					{
						if ( aoData[i].name == sKey )
						{
							return aoData[i].value;
						}
					}
					return null;
				}
			</script>
		';
		echo $str_return;
	}
	
	//fn_adt_server_side_files_maker($datatable_slug,$datatable_json)
}

/**
 * Enqueue Javascript for Adore Datatable Admin Control Panel 
 */
add_action('admin_enqueue_scripts', 'fn_adt_admin_load_scripts');

										   
							  

function fn_adt_admin_load_scripts($hook) 
{ 
	global $adt_settings_page;	
 
	if( $hook != $adt_settings_page ) //ref. https://pippinsplugins.com/loading-scripts-correctly-in-the-wordpress-admin/
	{
		return;		
	}
	wp_enqueue_script('jquery-ui-dialog');
	wp_enqueue_style("wp-jquery-ui-dialog");
 	wp_enqueue_script('datatables_admin_js');
}

/**
 * Function to register CSS Files
  
 */
function fn_adt_register_styles()
{
	$static_version='1.1'; //used for css which will never be modified	
	$dynamic_version='1.2'; //used for css which will be modified
	
	
	$adt_css_version=get_option('adt_css_version');

	if($adt_css_version==FALSE)
	{
		$adt_css_version=1;
		$deprecated = null;
	    $autoload = 'no';
	    add_option( 'adt_css_version', $adt_css_version, $deprecated, $autoload );			
	}
	
	//Web Assets
    wp_register_style('datatable_css' , '//cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css' , array() , $static_version , 'all');
    wp_register_style('bootstrap_datatable_css' , '//cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css' , array() , $static_version , 'all');
    wp_register_style('jqueryui_datatable_css' , '//cdn.datatables.net/1.10.13/css/dataTables.jqueryui.min.css' , array() , $static_version , 'all');
	
	//bootstrap css
	wp_register_style('bootstrap_css' , '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' , array() , $static_version , 'all');
	//custom adore datatable css
	wp_register_style('adt_custom_css', plugins_url('/assets/css/adt_custom_css.css', dirname(__FILE__) ),array(),$adt_css_version,'all');
	
	
}

/**
 * Function to register JS
 */
function fn_adt_register_javascripts()
{
	//scripts registration here
	$static_version='1.1'; //used for js which will never be modified	
	$dynamic_version='1.3'; //used for js which will be modified	
	
	$adt_js_version=get_option('adt_js_version');
	if($adt_js_version==FALSE)
	{
		$adt_js_version=1;
		$deprecated = null;
	    $autoload = 'no';
	    add_option( 'adt_js_version', $adt_js_version, $deprecated, $autoload );			
	}	
	
	 //Web Assets
    wp_register_script('datatable_js' , '//cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js' , array('jquery') , $static_version , TRUE);
    wp_register_script('bootstrap_datatable_js' , '//cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js' , array('jquery') , $static_version , TRUE);
    wp_register_script('jqueryui_datatable_js' , '//cdn.datatables.net/1.10.13/js/dataTables.jqueryui.js' , array('jquery') , $static_version , TRUE);
	
	
	//Javascript for Demo Adore Datatable
	wp_register_script('demo_datatable_js', plugins_url('/assets/js/demo_datatables.js', dirname(__FILE__) ), array('jquery'), $dynamic_version,TRUE); //Ref. https://codex.wordpress.org/Function_Reference/plugins_url	
	
	//Adore Datatable Custom Javascript Code
	wp_register_script('adt_custom_js', plugins_url('/assets/js/adt_custom_js.js', dirname(__FILE__) ), array('jquery'), $adt_js_version,TRUE); //Ref. https://codex.wordpress.org/Function_Reference/plugins_url
	
	//Javascript for Adore Datatable Admin Control Panel
	wp_register_script('datatables_admin_js', plugins_url('/assets/js/adore-datatables-admin.js', dirname(__FILE__) ), array('jquery'), $dynamic_version,TRUE); //Ref. https://codex.wordpress.org/Function_Reference/plugins_url	
}

/**
 * Function to jQueryUI CSS files based on jQueryUI Theme
 */
function fn_adt_register_jqueryui_css($theme='')
{
	$jqueryui_version='1.11.4'; //jQueryUI Css will be based on this version. Can be changed manually.	
	$jquery_ui_theme='smoothness';	//this value will be fetched from the database
	if(!empty($theme))
	{
		$jquery_ui_theme=$theme;
	}
	
	//register various jqueryui themes css	
	$cdn_url='https://code.jquery.com/ui/'.$jqueryui_version.'/themes/'.$jquery_ui_theme.'/jquery-ui.css'; //default theme
	wp_register_style('jqueryui_css', $cdn_url, array(), '1.1', 'all');
}
