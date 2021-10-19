<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://acewebx.com
 * @since      1.0.0
 *
 * @package    Ace_User_Management
 * @subpackage Ace_User_Management/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ace_User_Management
 * @subpackage Ace_User_Management/admin
 * @author     Webbninja <webbninja2@gmail.com>
 */
class Ace_User_Management_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ace_User_Management_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ace_User_Management_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ace-user-management-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-fontawesome', plugin_dir_url( __FILE__ ) . 'css/ace-fontawesome.css', array(), $this->version, 'all' );
		if( !empty( $_GET['page'] ) && sanitize_text_field( $_GET['page'] ) == 'ace-custom-field' ){
			wp_enqueue_style( $this->plugin_name.'-bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name.'-fontawesome', plugin_dir_url( __FILE__ ) . 'css/ace-fontawesome.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ace_User_Management_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ace_User_Management_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ace-user-management-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'ajax', array( 'url' => admin_url( 'admin-ajax.php' )) );

		if( !empty( $_GET['page'] ) && sanitize_text_field( $_GET['page'] ) == 'ace-custom-field' ){
			wp_enqueue_script( $this->plugin_name.'-bootjs', plugin_dir_url( __FILE__ ) . 'js/ace-bootstrap.min.js', array(), $this->version, 'all' );
			wp_enqueue_script( $this->plugin_name.'-jquery-core', get_site_url() . '/wp-includes/js/jquery/ui/core.min.js', array(), $this->version, 'all' );
			wp_enqueue_script( $this->plugin_name.'-jquery-widget', get_site_url() . '/wp-includes/js/jquery/ui/widget.min.js', array(), $this->version, 'all' );
			wp_enqueue_script( $this->plugin_name.'-jquery-mouse', get_site_url() . '/wp-includes/js/jquery/ui/mouse.min.js', array(), $this->version, 'all' );
			wp_enqueue_script( $this->plugin_name.'-sortable', get_site_url() . '/wp-includes/js/jquery/ui/sortable.min.js', array(), $this->version, 'all' );

		}


	}
	// wordpress menu
	public function ace_register_options_page(){
		  	add_menu_page( 	 
		  					'User Registration', // page title
			                'User Registration', 	//menu title
			                'manage_options', //capability
			                'ace-user-registration', // menu slug 
			                array($this, 'ace_register_page'), // callback function
			                'dashicons-dashboard', // icon 
			                52						//position 
			);
		  	    add_submenu_page( 'ace-user-registration', 
				  	    	'Custom Fields', 
				  	    	'Custom Fields', 
				  	    	'manage_options', 
				  	    	'ace-custom-field', 
				  	    	array( $this, 'ace_add_fields_' ) 
		  	    ); 
			   add_submenu_page(
			  				 'ace-user-registration', // parent slug
			  				 'Settings',	  // page title
			  				 'Settings',	// menu title
			  				 'manage_options',	// capability
			  				 'Ace-Setting',	//menu slug
			  				 array($this, 'add_all_setting_plugin')	// callback function
			   );
	}

	public function language_load_textdomain() {
		$mo_file = plugins_url( 'ace_user_managament/languages/ace_user_managament' ).'-'.get_locale().'mo';
		load_textdomain('ace_user_managament', $mo_file );
	    load_plugin_textdomain('ace_user_managament', false, plugins_url( 'ace_user_managament/languages/' ) );
	}

	public	function add_all_setting_plugin(){
		global $wpdb;
		$recaptcha_keys = get_option('custom_reCapatcha_value');
		if( isset( $_POST['save_reCaptcha'] ) ){
			$secretkey =  sanitize_text_field( $_POST['secretKEY'] );
			$sitekey =  sanitize_text_field( $_POST['siteKEY'] );
			$success = '';
			if( !empty( $secretkey )){
				if( strlen( $secretkey ) == 40 ){
					if( !empty($sitekey) ) {
						if( strlen( $sitekey ) == 40){
							if( !empty( $_POST['reCapatchacheck'] ) == 1 ){
									$checked = 1;
								}else{
									$checked = 0;									
								}
								$update_reCap = update_option( 'custom_reCapatcha_value', 
												array( 'c_reCaptcha_value' => $checked,
														'author_reCap_secertkey' =>  $secretkey,
														'author_siteKey' => $sitekey
												 ) 
											);								
						}else{
							$error['sitekey_vald'] = '! Site key field is empty';
						}
					}else{
						$error['sitekey_vald'] = '! Site key field is empty';
					}
				}else{
					$error['secretkey_vald'] = '! Secret key not valid';	
				}
			}else{
				$error['empty_secert_key'] = '! Secret key field is empty';
			}			
			if( isset( $error ) ){ 
				foreach ($error as $error_key => $error_value) { ?>
					<br>
					<span class="ace-error-alert-danger"><strong>&nbsp&nbsp<?php print_r($error_value); ?></strong></span> 
			<?php	}
			} else { ?>
					<span class="ace-success-alert-success"><strong>&nbsp&nbsp Seccussfully update</strong></span>
			<?php  } 
		}
		$recaptcha_keys = get_option('custom_reCapatcha_value');
		if( $recaptcha_keys['c_reCaptcha_value'] == 1 ){
			$re_checked = 'checked="checked"';
		}else{
			$re_checked = '';
		}
	if( isset($_POST['custom_css']) ){
		$custom_css_plugin = sanitize_text_field( $_POST['custom_css_for_pages'] ) ;
		$update_css = update_option( 'ace_custom_css_plugin', $custom_css_plugin );
		if( $update_css == TRUE ){ 
			$update_cus_css = '<span class="ace-success-alert-success"><strong>&nbsp&nbsp Seccussfully update</strong></span>';
		 }
	}
	$get_custom_css = ''; $get_custom_css = get_option( 'ace_custom_css_plugin', TRUE );		
?> 
	<form action="" method="post">
	<h1 style='font-weight: 400; font-size: 23px; margin-left: 50px;' >Settings</h1>
	  <div class="ace-reCap-box">
		<table class="ace-reCap-setting">
			<tr>
				<td class="label"><h4><?php 
				if( $recaptcha_keys['c_reCaptcha_value'] == 1 ){
					echo 'Enable reCaptcha';
				}else{
					echo 'Disable reCaptcha';
				} ?> </h4></td>
				<td><label class="switch">
					<input type="checkbox" name="reCapatchacheck" value="1" <?php  echo $re_checked; ?> class="recaptcha-css">
					<span class="slider round"></span>
				</label></td>
				</tr>
				</tr>
				<tr>
		  			<td class="label"><h4>Site Key : </h4></td> 
		  			<td><i class="fa fa-key" aria-hidden="true"></i>
		  				<input type="text" name="siteKEY" class="sitekey" value="<?php  echo $recaptcha_keys['author_siteKey']; ?>" class="recaptcha-css"></td>
				</tr>
				<tr>
			  		<td class="label"><h4>Secert Key : </h4></td> 
			  		<td><i class="fa fa-key" aria-hidden="true"></i>
			  			<input type="text" name="secretKEY" class="secretkey" value="<?php  echo $recaptcha_keys['author_reCap_secertkey']; ?>" class="recaptcha-css" ></td>
			<tr>
				<td colspan="2"><input type="submit" name="save_reCaptcha" value="Save" class="ace-recap-save"></td>
			</tr>
		</table>
		<br>
	  </div>
		<br>
		<?php if( isset( $update_cus_css) ){ echo $update_cus_css; } ?>
	  <br>
	  <div class="ace-reCap-box">
		<table class="ace-reCap-setting">
			<tr>
				<td><h3>StyleSheet(plugin pages css)</h3></td>
			</tr>
			<tr>
				<td><textarea id="myTextArea" class="css-textarea" cols="100" rows="10" name="custom_css_for_pages"><?php echo $get_custom_css;  ?></textarea></td>
			</tr>
			<tr>
				<td><input type="submit" name="custom_css" value="Save" class="ace-recap-save desc_btn"></td>
			</tr>
		</table>
		<br>
	  </div>
	</form>		
<?php }

	public function ace_register_page(){
		global $title;
		global $wpdb;
		require_once plugin_dir_path( __FILE__ ) . 'partials/ace-user-management-admin-display.php';
	 }

	public function ace_add_fields_(){
		global $title;
		global $wpdb, $post;
		$fieldTable = $wpdb->prefix.'ace_register_fields';
	    print "<h1 style='font-weight: 410; font-size: 23px; margin-left: 50px;' >$title</h1>";
		if ( isset( $_REQUEST['submit_form'] ) ) {	
			$dropdown_options = "";
			if(!empty($_REQUEST['dropdown_options']))
			{
				$dropdown_options = sanitize_text_field($_REQUEST['dropdown_options']);
			}
			$post = array(
		            'input_label'  		  => sanitize_text_field($_REQUEST['input_label']), 
		            'input_placeholder'   => sanitize_text_field($_REQUEST['input_placeholder']),
		            'input_name'          => sanitize_text_field($_REQUEST['input_name']),
		            'input_type'  		  => sanitize_text_field($_REQUEST['input_type']),
		            'dropdown_options'    => sanitize_text_field($dropdown_options)
		        );
			$wpdb->insert($fieldTable, $post );
		}
	    print '<div class="wrap">';
	    if( isset( $_REQUEST['update'] ) ) {
	    	$input_name_update   = sanitize_text_field( $_REQUEST['input_name_update']);
			$dr_options_update   = ( isset($_REQUEST['dr_options_update']) ) ? sanitize_text_field( $_REQUEST['dr_options_update']) : '';
			$input_type_update   = ( isset($_REQUEST['input_type_update']) ) ? sanitize_text_field( $_REQUEST['input_type_update']) : '';
			$f_place_update      = ( isset($_REQUEST['f_place_update']) ) ? sanitize_text_field( $_REQUEST['f_place_update']) : '';
			$f_lable_update      = ( isset($_REQUEST['f_lable_update']) ) ? sanitize_text_field( $_REQUEST['f_lable_update']) : '';
			$post_id             = sanitize_text_field( $_REQUEST['id_update']);

			$update_suces = $wpdb->update( $fieldTable, array('input_label' => $f_lable_update,
			'input_placeholder' => $f_place_update, 'input_type' => $input_type_update, 'dropdown_options' => 
			 $dr_options_update, 'input_name' => $input_name_update ), array('id' => $post_id) );
			if( $update_suces){
				$update_massage = ucfirst($f_lable_update) .' '.'field are updated successfully';
			}
		}

		$result_tbl = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ace_register_fields", OBJECT );
		if( !empty($result_tbl ) ) {
		    if( isset( $_REQUEST['savelayout']) ){
		    	$id_update_  =  filter_var_array( $_REQUEST['id_update_'] );
				foreach ( $id_update_ as $sort_key => $sortby_value ) {
			      $wpdb->update( $fieldTable,array( 'sortby' => $sort_key ),array( 'id' => $sortby_value ) );
			    }  			
			}
		}
			
	    $url = 	plugin_dir_path( __FILE__ );
	    $file = plugin_dir_path( __FILE__ ) . 'partials/register_form.php';
	    if ( file_exists( $file ) )
	        require $file;
	    print "<p class='description'>Included from <code>$file</code></p>";
	    print '</div>';
	}

	/**
	 * Add new fields above 'Update' button.
	 *
	 * @param WP_User $user User object.
	 */
	function ace_additional_profile_fields( $user ) {
		global $wpdb;
	    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ace_register_fields", OBJECT ); ?>
	    <h3>Extra profile information</h3>
	    <?php
	    foreach ($results as $key => $value ) {
	    	$dropdown_options =  explode(",", $value->dropdown_options);
	    	$get_meta = get_user_meta( $user->ID , $value->input_name, true );
			?>
	    <table class="form-table">
	        <tr>
	            <th>
	                <label for="code"><?php echo ucfirst($value->input_label); ?></label>
	            </th>
	            <td>
	            <?php if($value->input_type == 'text'){ ?>
	                	<input type="text" placeholder="<?php echo $value->input_placeholder; ?>" name="<?php echo $value->input_name; ?>" id="<?php echo $value->input_name; ?>" value="<?php echo $get_meta; ?>" class="regular-text" />
	            <?php }
	            elseif ($value->input_type == 'textarea') { ?>
	            		<textarea placeholder="<?php echo $value->input_placeholder; ?>" name="<?php echo $value->input_name; ?>" id="<?php echo $value->input_name; ?>" rows="2" cols="20"><?php echo $get_meta; ?></textarea>
	            <?php }
	            elseif ($value->input_type == 'dropdown') { ?>
	            	<select name="<?php echo $value->input_name; ?>" id="<?php echo $value->input_name; ?>" placeholder="<?php echo $value->input_placeholder; ?>" value="<?php echo $get_meta; ?>"> 
	            		<?php 
	            		foreach ($dropdown_options as $key => $val) {
	            			$options = explode(":", $val);
	            			?>
	            			<option value="<?php echo $options[0]; ?>" <?php if($options[0] == $get_meta) { echo "selected";} ?> > <?php echo $options[1]; ?></option>
	            	<?php	} ?>
	            	</select> 
	            <?php } ?>
	            </td>
	        </tr>
	    </table>
	    <?php }
	}

	function ace_user_interests_fields_save( $user_id ) {
		global $wpdb;
        if ( !current_user_can( 'edit_user', $user_id ) ) return false;
        $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ace_register_fields", OBJECT );
        foreach ($results as $key => $value) {
        	print_r($results);
        	update_user_meta( $user_id, $value->input_name, sanitize_text_field( $_REQUEST[$value->input_name]) );
        }
    }

    function ace_delete_user(){
    	global $wpdb;
    	$id = intval( $_REQUEST['id'] );
    	$sql = "DELETE FROM {$wpdb->prefix}ace_register_fields where id=$id";
    	if( $wpdb->query( $sql ) ){
    		echo $delete_ma = "deleted scussfully !";
    	}else{
    		echo "error !";
    	} 
    }
}
