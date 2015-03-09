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
		
		$settings_loader.show();
    	
    	var submit_data = 
		{
			action: 'fn_load_dt_settings_ajax',			
			adt_id:$val,
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
});