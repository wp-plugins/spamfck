<?php
	/*
	Plugin Name: SpamFCK
	Plugin URI: http://cmsss.all4all.cz
	Description: Antispam protection:<br><strong>1.</strong> Add 3 hidden fields to Registration form and to Comments form, that only robots can see. Plus you can choose to add validation checkbox to forms.<br><strong>2</strong> Add to .htaccess file direct access protection for comments file.<br><strong>3.</strong> Check time user spent on page and time when user send a form.<br><strong>4.</strong> Disable trackbacks for past and future posts
	Version: 1.3
	Author: Zedna Brickick Website
	Author URI: http://www.mezulanik.cz
	License: GPL2
	*/

// CREATE SpamFCK STATISTICS
add_option( 'spamfck_stats', '0', '', 'yes' );

// THIRD WAY OF PROTECTION - COMPARE TIME USER SPENT ON THE PAGE AND TIME WHEN HE SEND A COMMENT
setcookie("spamfck-usertime-start", time());

 // FIRST WAY OF PROTECTION - ADD HIDDEN FIELDS / VALIDATION CHECKBOX TO FORMS
 //1. Add a new form elements to registration form
    add_action('register_form','spamfck_register_form');
    function spamfck_register_form (){
    $web = ( isset( $_POST['web'] ) ) ? $_POST['web']: '';
		$message = ( isset( $_POST['message'] ) ) ? $_POST['message']: '';
		$description = ( isset( $_POST['description'] ) ) ? $_POST['description']: '';
   $validation = ( isset( $_POST['validation'] ) ) ? $_POST['validation']: '';
   
 $validation = intval( $validation );
 $web = intval( $web );
 $message = intval( $message );
 $description = intval( $description ); 
  
 $validation = sanitize_text_field( $validation );
 $web = sanitize_text_field( $web );
 $message = sanitize_text_field( $message );
 $description = sanitize_text_field( $description );
   
   $robot_checkbox_validation = get_option('spamfck');
 if ($robot_checkbox_validation == "yes"){
		?>
   <p>
			<label for="validation"><?php _e('I am robot','mydomain') ?></label>
      <input type="checkbox" name="validation" id="validation" class="input" value="yes" checked/>
        </p>
        <?php } ?>
        <p style='display:none'>
            <label for="web"><?php _e('Web','mydomain') ?><br />
            <input type="text" name="web" id="web" class="input" value="<?php echo esc_attr(stripslashes($web)); ?>" size="25" /></label>
		</p>
		<p style='display:none'>
			<label for="message"><?php _e('Message','mydomain') ?><br />
            <input type="text" name="message" id="message" class="input" value="<?php echo esc_attr(stripslashes($message)); ?>" size="25" /></label>
        </p>
		<p style='display:none'>
			<label for="description"><?php _e('Description','mydomain') ?><br />
            <input type="text" name="description" id="description" class="input" value="<?php echo esc_attr(stripslashes($description)); ?>" size="25" /></label>
        </p>
        <?php
    }

    //2. Register form validation
    add_filter('registration_errors', 'spamfck_registration_errors', 10, 3);
    function spamfck_registration_errors ($errors, $sanitized_user_login, $user_email) {

// THIRD WAY OF PROTECTION - COMPARE TIME USER SPENT ON THE PAGE AND TIME WHEN HE SEND A COMMENT
$usertime_start = $_COOKIE['spamfck-usertime-start'];
$actualtime = time();
$usertimecompare = ($actualtime-$usertime_start);

$usertime_start = sanitize_text_field( $usertime_start );
$actualtime = sanitize_text_field( $actualtime );
$usertimecompare = sanitize_text_field( $usertimecompare );
// --THIRD WAY OF PROTECTION - COMPARE TIME USER SPENT ON THE PAGE AND TIME WHEN HE SEND A COMMENT
    
	$robot_checkbox_validation = get_option('spamfck');
 $validation = ( isset( $_POST['validation'] ) ) ? $_POST['validation']: '';
 
 $validation = intval( $validation );
 $web = intval( $_POST['web'] );
 $message = intval( $_POST['message'] );
 $description = intval( $_POST['description'] );
 
 $validation = sanitize_text_field( $validation );
 $web = sanitize_text_field( $web );
 $message = sanitize_text_field( $message );
 $description = sanitize_text_field( $description );
 
 $spamfckstats = get_option('spamfck_stats');   //Spam statistics
 $spamfckstats_update = $spamfckstats+1;        //Spam statistics update

    if($usertimecompare < 5)     //5 seconds        // THIRD WAY OF PROTECTION - COMPARE TIME USER SPENT ON THE PAGE AND TIME WHEN HE SEND A COMMENT
         
            $errors->add( 'time_error', __('<strong>ERROR</strong>: You are spammer. Wait at least 5s before your action.','mydomain') );
            update_option( 'spamfck_stats', $spamfckstats_update, 'yes' );     //Spam statistics update
    if ( !empty( $_POST['web'] ) )
            $errors->add( 'web_error', __('<strong>ERROR</strong>: You are spammer. Stupid robot! hahaha','mydomain') );
            update_option( 'spamfck_stats', $spamfckstats_update, 'yes' );    //Spam statistics update
    if ( !empty( $_POST['message'] ) )
            $errors->add( 'message_error', __('<strong>ERROR</strong>: You are spammer.Stupid robot! hahaha','mydomain') );
            update_option( 'spamfck_stats', $spamfckstats_update, 'yes' );     //Spam statistics update
    if ( !empty( $_POST['description'] ) )  
            $errors->add( 'description_error', __('<strong>ERROR</strong>: You are spammer.Stupid robot! hahaha','mydomain') );
            update_option( 'spamfck_stats', $spamfckstats_update, 'yes' );     //Spam statistics update
    if ( $robot_checkbox_validation == "yes")
            $errors->add( 'validation_error', __('<strong>ERROR</strong>: Are you spammer? You have to uncheck "I am robot".','mydomain') );
            update_option( 'spamfck_stats', $spamfckstats_update, 'yes' );      //Spam statistics update      
		return $errors;
    }
    
    
   //3. Comments fields customization 
// Add hidden fields after default fields above the comment box, validation checkbox is visible

add_action( 'comment_form_logged_in_after', 'additional_fields' );
add_action( 'comment_form_after_fields', 'additional_fields' );

function additional_fields () {
	echo '<p style=display:none>'.
	'<label for="title">' . __( 'Web url' ) . '</label>'.
	'<input id="urlad" name="urlad" type="text" size="30"  tabindex="1" /></p>';

	echo '<p style=display:none>'.
	'<label for="title">' . __( 'Name' ) . '</label>'.
	'<input id="namead" name="namead" type="text" size="30"  tabindex="2" /></p>';
 
 echo '<p style=display:none>'.
	'<label for="title">' . __( 'Message' ) . '</label>'.
	'<input id="messagead" name="messagead" type="text" size="30"  tabindex="3" /></p>';
 
 $robot_checkbox_validation = get_option('spamfck');
 if ($robot_checkbox_validation == "yes"){
 echo '<p class="comment-form-add-validation">'.
	'<label for="title" style="display:inline-block">' . __( '<strong>I am robot</strong>' ) . '</label>'.
	'<input style="width:20px" id="validationad" name="validationad" type="checkbox" tabindex="4" value="yes" checked/></p>';
 } 
}

//4. Comments fields validation
// Add the filter to check if the hidden comment meta data has been filled or not

add_filter( 'preprocess_comment', 'verify_comment_meta_data' );
function verify_comment_meta_data( $commentdata ) {
$robot_checkbox_validation = get_option('spamfck');            

// THIRD WAY OF PROTECTION - COMPARE TIME USER SPENT ON THE PAGE AND TIME WHEN HE SEND A COMMENT
$usertime_start = $_COOKIE['spamfck-usertime-start'];
$actualtime = time();
$usertimecompare = ($actualtime-$usertime_start);

$usertime_start = sanitize_text_field( $usertime_start );
$actualtime = sanitize_text_field( $actualtime );
$usertimecompare = sanitize_text_field( $usertimecompare );
// --THIRD WAY OF PROTECTION - COMPARE TIME USER SPENT ON THE PAGE AND TIME WHEN HE SEND A COMMENT
 
 $validation = intval( $validation );
 $web = intval( $_POST['web'] );
 $message = intval( $_POST['message'] );
 $description = intval( $_POST['description'] );
 
 $validation = sanitize_text_field( $validation );
 $web = sanitize_text_field( $web );
 $message = sanitize_text_field( $message );
 $description = sanitize_text_field( $description );
 
 $spamfckstats = get_option('spamfck_stats');  //Spam statistics
 $spamfckstats_update = $spamfckstats+1;       //Spam statistics update

if($usertimecompare < 10)     //10 seconds        // THIRD WAY OF PROTECTION - COMPARE TIME USER SPENT ON THE PAGE AND TIME WHEN HE SEND A COMMENT
            wp_die( __( 'Error: You are spammer. Wait at least 10s before your action.' ) );
            update_option( 'spamfck_stats', $spamfckstats_update, 'yes' );    //Spam statistics update
if ( !empty( $_POST['urlad'] ) )
            wp_die( __( 'Error: You are spammer. URL field is for stupid robots!' ) );
            update_option( 'spamfck_stats', $spamfckstats_update, 'yes' );   //Spam statistics update
if ( !empty( $_POST['namead'] ) )
            wp_die( __( 'Error: You are spammer. NAME field is for stupid robots!' ) );
            update_option( 'spamfck_stats', $spamfckstats_update, 'yes' );  //Spam statistics update
if ( !empty( $_POST['messagead'] ) )
            wp_die( __( 'Error: You are spammer. MESSAGE field is for stupid robots!' ) );
            update_option( 'spamfck_stats', $spamfckstats_update, 'yes' );   //Spam statistics update
if ( $robot_checkbox_validation == "yes")
     wp_die( __( 'Error: You must uncheck message "I am robot", if you are not a robot.' ) );
            update_option( 'spamfck_stats', $spamfckstats_update, 'yes' );  //Spam statistics update       
	return $commentdata;
}


//Add admin page
// Call extra_post_info_menu function to load plugin menu in dashboard
add_action( 'admin_menu', 'spamfck_menu' );

// Create WordPress admin menu
if( !function_exists("spamfck_menu") )
{
function spamfck_menu(){

  $page_title = 'SpamFCK';
  $menu_title = 'SpamFCK';
  $capability = 'manage_options';
  $menu_slug  = 'spamfck';
  $function   = 'spamfck_page';
  $icon_url   = 'dashicons-media-code';
  $position   = 99;
  
  add_menu_page( $page_title,
                 $menu_title,
                 $capability,
                 $menu_slug,
                 $function,
                 $icon_url,
                 $position );

  // Call update_spamfck function to update database
  add_action( 'admin_init', 'update_spamfck' );

}
}

// Create function to register plugin settings in the database
if( !function_exists("update_spamfck") )
{
function update_spamfck() {
  register_setting( 'spamfck-settings', 'spamfck' );     
}
}

// Create WordPress plugin page
if( !function_exists("spamfck_page") )
{
function spamfck_page(){
?>
  <h1>SpamFCK settings page</h1>
  <h2>This plugin:</h2>
  <h3>1.Add 3 hidden fields to registration and comments forms and now you can choose adding validation checkbox.</h3>
  <h3>2.Add direct access protection for comments file</h3>
  <h3>3.Check time user display and send a form</h3>
  <h3>4.Disable trackbacks for past and future posts</h3>
  <form method="post" action="options.php">
    <?php settings_fields( 'spamfck-settings' ); ?>
    <?php do_settings_sections( 'spamfck-settings' ); ?>
    <table class="form-table">
      <tr valign="top">
      <th scope="row">Allow 'I am robot' checkbox validation:</th>
      <td>
      <select name='spamfck'>
      <?php $selectedoption = get_option('spamfck'); 
      if ($selectedoption == "yes"){
       echo "<option value='yes' selected=selected>Yes</option>
             <option value='no'>No</option>";
      }else{ 
      echo "<option value='yes'>Yes</option>
            <option value='no' selected=selected>No</option>";
      }
      ?>
      </select>
      </td>
      </tr>
    </table>
  <?php submit_button(); ?>
  </form>
  <div>
  This page has been protected <strong><?php echo $spamfckstats = get_option('spamfck_stats');?></strong> times.
  </div>
<p>If you like this plugin, please donate us for faster upgrade</p>
  <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

<?php
}
}

// Plugin logic for adding extra info to posts
if( !function_exists("spamfck") )
{
  function spamfck($content)
  {
    $extra_info = get_option('spamfck');
    return $content;
  }
}

// Apply the spamfck function on our content  
add_filter('the_content', 'spamfck');


// SECOND WAY OF PROTECTION - BLOCK DIRECT ACCESS TO COMMENTS FILE 
//Modyfying WP .htaccess file to add a protection for wp-comments-post.php file
$server_folder = dirname($_SERVER['REQUEST_URI']);
$server_host = $_SERVER['HTTP_HOST'];

$search      = "wp-comments-post.php";

$lines       = file('.htaccess');

$line_number = false;

while (list($key, $line) = each($lines) and !$line_number) {
   $line_number = (strpos($line, $search) !== FALSE) ? $key + 1 : $line_number;
}

if(!$line_number){
$htaccess = fopen('.htaccess', 'a'); // otevře soubor pro zápis na konci  
fwrite($htaccess, "\n"); 
fwrite($htaccess, "\n"); 
fwrite($htaccess, "# BEGIN SpamFCK");
fwrite($htaccess, "\n"); 
fwrite($htaccess, "<IfModule mod_rewrite.c>");
fwrite($htaccess, "\n");
fwrite($htaccess, 'RewriteCond %{REQUEST_METHOD} POST'); // zapíše
fwrite($htaccess, "\n");
fwrite($htaccess, 'RewriteCond %{REQUEST_URI} .wp-comments-post.php*'); // zapíše
fwrite($htaccess, "\n");
fwrite($htaccess, 'RewriteCond %{HTTP_REFERER} !.*'.$server_host.'* [OR]'); // zapíše
fwrite($htaccess, "\n");
fwrite($htaccess, 'RewriteCond %{HTTP_USER_AGENT} ^$'); // zapíše
fwrite($htaccess, "\n");
fwrite($htaccess, 'RewriteRule (.*) ^http://%{REMOTE_ADDR}/$ [R=301,L]'); // zapíše
fwrite($htaccess, "\n");
fwrite($htaccess, '</IfModule>'); // zapíše
fwrite($htaccess, "\n");
fwrite($htaccess, "# END SpamFCK");
fclose($htaccess);
}

// FOURTH WAY OF PROTECTION - BLOCK FUTURE AND PAST TRACKBACKS
add_action( 'admin_init', 'close_trackbacks' );
if( !function_exists("close_trackbacks") )
{
function close_trackbacks() {
//disable trackbacks and pings in past
global $wpdb;
    $table_name = $wpdb->prefix . 'posts';
     $wpdb->query("UPDATE $table_name SET ping_status='closed'");
 
//disable trackbacks and pings for future 
update_option( 'default_ping_status', 'closed', 'yes' );    //Disable trackbacks
update_option( 'default_pingback_flag', '0', 'yes' );    //Disable trackbacks
update_option( 'use_trackback', '0', 'yes' );    //Disable trackbacks
}
}

  
?>