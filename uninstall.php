
//Reference http://codex.wordpress.org/Function_Reference/register_uninstall_hook

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

//delete Adore Datatable Settings option
$option_name = 'adt_admin_config';
delete_option( $option_name );

//delete Adore Datatable Custom CSS version option
$option_name = 'adt_css_version';
delete_option( $option_name );

//delete Adore Datatable Custom JS version option
$option_name = 'adt_js_version';
delete_option( $option_name );

//drop a custom db table
global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}adt_demo_table" );

//delete table adore_datatable_settings
$wpdb->query( "DROP TABLE IF EXISTS adore_datatable_settings" );