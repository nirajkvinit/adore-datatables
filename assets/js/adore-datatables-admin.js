$=jQuery;
jQuery(document).ready(function($) 
{
	$('#select_adt_table').change(function()
    {
		var $selected = $(this).find('option:selected');
		
		var $div_adt_settings_area=$("#div_adt_settings_area");
		var $settings_loader=$("#adt_settings_loader");
		
		var $val=$selected.val();
		if($val=='select')
		{
			$div_adt_settings_area.html('');
			return;
		}
				
		$slug='';
		if($val!='create')
		{
			$slug=$selected.data('slug');
		}				
		
		$settings_loader.show();
    	
    	var submit_data = 
		{
			action: 'fn_load_dt_settings_ajax',			
			adt_id:$val,
			slug:$slug
		};
		jQuery.post(ajaxurl, submit_data, function(response)
        {
        	$div_adt_settings_area.html(response);
        }).complete(function()
        {
        	$settings_loader.hide();
        });
    });
    $("#div_adt_settings_area").on('change', "#select_adt_db_tables_list", function()
    {
    	var $selected = $('#select_adt_db_tables_list option:selected');
    	var $val=$selected.val();
    	var $div_adt_table_columns_options=$("#div_adt_table_columns_options");
    	var $settings_loader=$("#adt_settings_loader");
    	
    	$div_adt_table_columns_options.html('');
    	if($val=='select')
    	{
    		return;
    	}
    	$settings_loader.show();
    	
    	var submit_data = 
		{
			action: 'fn_show_adt_coulms_ajax',			
			adt_selected_table:$val,
		};
		jQuery.post(ajaxurl, submit_data, function(response)
        {
        	$div_adt_table_columns_options.html(response);
        }).complete(function()
        {
        	$settings_loader.hide();
        });
    });
    $("#div_adt_settings_area").on('click', "#cmd_create_adt_datatable", function()
    {
    	var $button=$(this);
    	$result_area="div_adt_table_save_result_area";
    	$adt_nonce=$("#hidden_adt_save_nonce").val();
    	$datatable_name=$("#txt_adt_datatable_name").val();
    	$html_table_id=$("#txt_adt_table_id").val();
    	$html_table_class=$("#txt_adt_table_class").val();
    	
    	$table_type=$("#select_adt_table_type option:selected").val();
    	$pagination_type=$("#select_adt_pagination option:selected").val();
    	
    	$allow_search='disabled';
    	$allow_ordering='disabled';
    	$show_info='disabled';
    	$allow_auto_width='disabled';
    	$scroll_vertical='disabled';
    	$individual_column_filtering='disabled';
    	
    	$sdom=$("#txt_adt_sdom").val();
    	$fn_row_callback=$("#txt_adt_fnrowcallback").val();
    	
    	$database_table_name=$("#select_adt_db_tables_list option:selected").val();
    	
    	if($("#chk_adt_allow_search").is(":checked"))
    	{
    		$allow_search='enabled';    		
    	}
    	if($("#chk_adt_ordering").is(":checked"))
    	{
    		$allow_ordering='enabled';    		
    	}
    	if($("#chk_adt_showinfo").is(":checked"))
    	{
    		$show_info='enabled';    		
    	}
    	if($("#chk_adt_autowidth").is(":checked"))
    	{
    		$allow_auto_width='enabled';    		
    	}
    	if($("#chk_adt_scrollvertical").is(":checked"))
    	{
    		$scroll_vertical='enabled';    		
    	}
    	if($("#chk_adt_individual_column_filtering").is(":checked"))
    	{
    		$individual_column_filtering='enabled';
    	}
    	
    	if($datatable_name.length<=0)
    	{
    		$str_message="Please input datatable name.";
    		$message_type="warning";
    		fn_adt_show_info_msg($str_message, $result_area, $message_type);
    		return;
    	}
    	if($database_table_name.length<=0)
    	{
    		$str_message="Please select a Database Table.";
    		$message_type="warning";
    		fn_adt_show_info_msg($str_message, $result_area, $message_type);
    		return;
    	}
    	
    	$table_columns_array_for_post=[];
    	
    	$column_position_array=[];
    	
    	$duplicate_column_position=false;
    	$empty_column_position=false;
    	$column_position_not_numeric=false;
    	$negative_column_position=false;
    	$excess_column_position=false;
    	$decimal_column_position=false;
    	
    	$total_columns=$('#tbl_adt_column_select tbody tr').length-1;    	
    	
    	$('#tbl_adt_column_select tbody tr').each(function() 
    	{
			$column_name=$(this).find('td:eq(0)').html();
			$column_title=$(this).find('.txt_adt_column_title').val();	       
			$sclass=$(this).find('.txt_adt_column_sclass').val();
			$column_position=$(this).find('.txt_adt_column_position').val();
	       
			$chk_adt_column_visible=$(this).find('.chk_adt_column_visible');
			$chk_adt_column_searchable=$(this).find('.chk_adt_column_searchable');
			$chk_adt_column_sortable=$(this).find('.chk_adt_column_sortable');
			
			$is_visible='disabled';
			$is_searchable='disabled';
			$is_sortable='disabled';
			
			if($chk_adt_column_visible.is(":checked"))
			{
	    		$is_visible='enabled';
	    	}
	    	if($chk_adt_column_searchable.is(":checked"))
			{
	    		$is_searchable='enabled';
	    	}
	    	if($chk_adt_column_sortable.is(":checked"))
			{
	    		$is_sortable='enabled';
	    	}
	    	
	    	//check if column position is empty or not
	    	if($column_position.length<=0)
	    	{
	    		$empty_column_position=true;
				return false;
	    	}
	    	
	    	//check if column position is numeric or not
	    	if(!$.isNumeric($column_position))
	    	{
	    		$column_position_not_numeric=true;
	    		return false;	    		
	    	}
	    	
	    	//check if column position is duplicate or not
	    	if($.inArray( $column_position, $column_position_array)<0)
			{
				$column_position_array.push($column_position);
			}
			else
			{
				$duplicate_column_position=true;
				return false;
			}
	    	
	    	//check if column position is negative or not
	    	if($column_position<0)
	    	{
	    		$negative_column_position=true;
				return false;
	    	}
	    	
	    	//check if column position has desimal or not
	    	if($column_position % 1 != 0)
	    	{
	    		$decimal_column_position=true;
				return false;
	    	}
	    	
	    	//check if column position exceeds total column numbers or not
	    	if($column_position>$total_columns)
	    	{
	    		$excess_column_position=true;
				return false;
	    	}
	    	
	    	var data = {};
	        data.column_name = $column_name;
	        data.column_title = $column_title;
	        data.sclass = $sclass;
	        data.column_position = $column_position;
	        data.is_visible = $is_visible;
	        data.is_searchable = $is_searchable;
	        data.is_sortable = $is_sortable;	        
	        $table_columns_array_for_post.push(data);
		});
    	
    	//column position empty
    	if($empty_column_position)
    	{
    		$str_message="Table Column position cannot be empty. Please check your input and try again.";
    		$message_type="error";
    		fn_adt_show_info_msg($str_message, $result_area, $message_type);
    		return;
    	}
    	
    	//column position not numeric
    	if($column_position_not_numeric)
    	{
    		$str_message="Table Column position is not a number. Please check your input and try again.";
    		$message_type="error";
    		fn_adt_show_info_msg($str_message, $result_area, $message_type);
    		return;
    	}
    	
    	//column position duplicate    	
    	if($duplicate_column_position)
    	{
    		$str_message="Table Column position cannot be duplicate. Please check your input and try again.";
    		$message_type="error";
    		fn_adt_show_info_msg($str_message, $result_area, $message_type);
    		return;
    	}    	
    	
    	//column position negative    	
    	if($negative_column_position)
    	{
    		$str_message="Table Column position cannot be a negative number. Please check your input and try again.";
    		$message_type="error";
    		fn_adt_show_info_msg($str_message, $result_area, $message_type);
    		return;
    	}
    	
    	//column position decimal number
    	if($decimal_column_position)
    	{
    		$str_message="Table Column position cannot be a decimal number. Please check your input and try again.";
    		$message_type="error";
    		fn_adt_show_info_msg($str_message, $result_area, $message_type);
    		return;
    	}
    	
    	//column position exceeds total column numbers
    	if($excess_column_position)
    	{
    		$str_message="Table Column position cannot exceed total columns numbers. Please check your input and try again.";
    		$message_type="error";
    		fn_adt_show_info_msg($str_message, $result_area, $message_type);
    		return;
    	}
    	
    	$adt_table_save_loader=$("#adt_table_save_loader");    	
    	$adt_table_save_loader.show();
    	
    	$button.hide();
    	
    	var submit_data = 
		{
			action: 'fn_adt_table_save_ajax',
			adt_nonce:$adt_nonce,
			datatable_name:$datatable_name,
			html_table_id:$html_table_id,
			html_table_class:$html_table_class,
			table_type:$table_type,
			pagination_type:$pagination_type,
			allow_search:$allow_search,
			allow_ordering:$allow_ordering,
			show_info:$show_info,
			allow_auto_width:$allow_auto_width,
			scroll_vertical:$scroll_vertical,
			individual_column_filtering:$individual_column_filtering,
			sdom:$sdom,
			fn_row_callback:$fn_row_callback,
			database_table_name:$database_table_name,
			adt_column_data:$table_columns_array_for_post
		};
		jQuery.post(ajaxurl, submit_data, function(response)
        {
        	$("#"+$result_area).html(response);
        	
        	var obj = $.parseJSON(response);				
			var $result=obj.result;
			var $result_message=obj.result_message;
			
        	if($result=='error')
        	{
        		fn_adt_show_info_msg($result_message, $result_area, 'error');
        	}
        	else
        	{
        		fn_adt_show_info_msg($result_message, $result_area, 'success');
        		
        		var $new_datatable_id=obj.datatable_id;
        		var $new_datatable_name=obj.datatable_name;
        		var $new_datatable_slug=obj.datatable_slug;
        		
        		$('#select_adt_table').append('<option value="'+$new_datatable_id+'" data-slug="'+$new_datatable_slug+'">'+$new_datatable_name+'</option>');
        		$('#select_adt_table').val($new_datatable_id);
        		setTimeout(function()
				{
					$('#select_adt_table').change();					
				},1500);
        	}
        	
        }).complete(function()
        {
        	$adt_table_save_loader.hide();
        	$button.show();
        });
    	
    });
});

var $time_out_var=null;
function fn_adt_show_info_msg($str_msg, $element_id, $message_type)
{
	clearTimeout($time_out_var);
	$message='';
	$element=$("#"+$element_id);
	switch($message_type)
	{
		case 'error':
			$message='<div class="error form-invalid">'+$str_msg+'</div>';
			break;
		case 'warning':
			$message='<div class="error">'+$str_msg+'</div>';
			break;
		case 'success':
			$message='<div class="updated">'+$str_msg+'</div>';
			break;
		default:
			$message='<div class="alternate">'+$str_msg+'</div>';
			break;
	}
	$element.html($message);
	$time_out_var=setTimeout(function()
	{
		$element.html('');
	},7000);
}