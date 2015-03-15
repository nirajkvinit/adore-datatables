<?php
/*
Plugin Name: Filesystem API Demo
Version: 1.0
Author: Anna Ladoshkina
Description: Example of practical usage WordPress Filesystem API.
Author URI: http://www.foralien.com

DO NOT USE THIS ON A PRODUCTION SITE. THIS IS DEMONSTRATION CODE ONLY.
*/




/**
 * Create Demo page (under Tools menu)
 * 
 **/
add_action('admin_menu', 'filesystem_demo_page');

function filesystem_demo_page() {
    
    add_submenu_page( 'tools.php', 'Filesystem API Demo page', 'Filesystem Demo', 'upload_files', 'filesystem_demo', 'filesystem_demo_screen' );
}

function filesystem_demo_screen() {

$form_url = "tools.php?page=filesystem_demo";
$output = $error = '';

/**
 * write submitted text into file (if any)
 * or read the text from file - if there is no submission
 **/
if(isset($_POST['demotext'])){//new submission
    
    if(false === ($output = filesystem_demo_text_write($form_url))){
        return; //we are displaying credentials form - no need for further processing
    
    } elseif(is_wp_error($output)){
        $error = $output->get_error_message();
        $output = '';
    }
    
} else {//read from file
    
    if(false === ($output = filesystem_demo_text_read($form_url))){
        return; //we are displaying credentials form no need for further processing
    
    } elseif(is_wp_error($output)) {
        $error = $output->get_error_message();
        $output = '';
    }
}


$output = esc_textarea($output); //escaping for printing

?>
    
<div class="wrap">
<div class="icon32" id="icon-tools"><br></div>
<h2>Filesystem API Demo page</h2>

<?php if(!empty($error)): ?>
    <div class="error below-h2"><?php echo $error;?></div>
<?php endif; ?>

<form method="post" action="" style="margin-top: 3em;">

<?php wp_nonce_field('filesystem_demo_screen'); ?>

<fieldset class="form-table">
    <label for="demotext">Demo Text</label><br>
    <textarea id="demotext" name="demotext" rows="8" class="large-text"><?php echo $output;?></textarea>
</fieldset>
    
   
<?php submit_button('Submit', 'primary', 'demotext_submit', true);?>

</form>
</div>
<?php
}


/**
 * Initialize Filesystem object
 *
 * @param str $form_url - URL of the page to display request form
 * @param str $method - connection method
 * @param str $context - destination folder
 * @param array $fields - fileds of $_POST array that should be preserved between screens
 * @return bool/str - false on failure, stored text on success
 **/
function filesystem_init($form_url, $method, $context, $fields = null) {
    global $wp_filesystem;
    
    
    /* first attempt to get credentials */
    if (false === ($creds = request_filesystem_credentials($form_url, $method, false, $context, $fields))) {
        
        /**
         * if we comes here - we don't have credentials
         * so the request for them is displaying
         * no need for further processing
         **/
        return false;
    }
    
    /* now we got some credentials - try to use them*/        
    if (!WP_Filesystem($creds)) {
        
        /* incorrect connection data - ask for credentials again, now with error message */
        request_filesystem_credentials($form_url, $method, true, $context);
        return false;
    }
    
    return true; //filesystem object successfully initiated
}


/**
 * Perform writing into file
 *
 * @param str $form_url - URL of the page to display request form
 * @return bool/str - false on failure, stored text on success
 **/
function filesystem_demo_text_write($form_url){
    global $wp_filesystem;
    
    check_admin_referer('filesystem_demo_screen');
    
    $demotext = sanitize_text_field($_POST['demotext']); //sanitize the input
    $form_fields = array('demotext'); //fields that should be preserved across screens
    $method = ''; //leave this empty to perform test for 'direct' writing
    $context = WP_PLUGIN_DIR . '/filesystem-demo'; //target folder
            
    $form_url = wp_nonce_url($form_url, 'filesystem_demo_screen'); //page url with nonce value
    
    if(!filesystem_init($form_url, $method, $context, $form_fields))
        return false; //stop further processign when request form is displaying
    
    
    /*
     * now $wp_filesystem could be used
     * get correct target file first
     **/
    $target_dir = $wp_filesystem->find_folder($context);
    $target_file = trailingslashit($target_dir).'test.txt';
       
    
    /* write into file */
    if(!$wp_filesystem->put_contents($target_file, $demotext, FS_CHMOD_FILE)) 
        return new WP_Error('writing_error', 'Error when writing file'); //return error object
      
    
    return $demotext;
}


/**
 * Read text from file
 *
 * @param str $form_url - URL of the page where request form will be displayed
 * @return bool/str - false on failure, stored text on success
 **/
function filesystem_demo_text_read($form_url){
    global $wp_filesystem;

    $demotext = '';
    
    $form_url = wp_nonce_url($form_url, 'filesystem_demo_screen');
    $method = ''; //leave this empty to perform test for 'direct' writing
    $context = WP_PLUGIN_DIR . '/filesystem-demo'; //target folder   
    
    if(!filesystem_init($form_url, $method, $context))
        return false; //stop further processign when request formis displaying
    
    
    /*
     * now $wp_filesystem could be used
     * get correct target file first
     **/
    $target_dir = $wp_filesystem->find_folder($context);
    $target_file = trailingslashit($target_dir).'test.txt';
    
    
    /* read the file */
    if($wp_filesystem->exists($target_file)){ //check for existence
        
        $demotext = $wp_filesystem->get_contents($target_file);
        if(!$demotext)
            return new WP_Error('reading_error', 'Error when reading file'); //return error object           
        
    }   
    
    return $demotext;    
}




?>