<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://acewebx.com
 * @since      1.0.0
 *
 * @package    Ace_User_Management
 * @subpackage Ace_User_Management/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ace_User_Management
 * @subpackage Ace_User_Management/public
 * @author     Webbninja <webbninja2@gmail.com>
 */
class Ace_User_Management_Public {
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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ace-user-management-public.css', array(), $this->version, 'all' );
	}
	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) .'js/ace-user-management-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( 'ajax-script', 'my_ajax_object',array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( $this->plugin_name.'api', plugin_dir_url( __FILE__ ) . 'js/ace-api.js', array(), $this->version, 'all' );

	}

	public function ace_pages_permalink() {
		global $page_permalink; 
			$page_permalink = array(
				'login_permalink' => get_page_by_title('login'),
				'reg_permalink' => get_page_by_title('registration'),
				'profile_permalink' => get_page_by_title('profile'),
				'confirm_pass'  => get_page_by_title('Confirm Password')
			 );
	}

	public function ace_show_admin_bar_status(){ 
		if ( ! current_user_can( 'manage_options' ) ) {
	    show_admin_bar( false );
		}
	}

	// user logout 
	public	function ace_custom_logout_page(){
		global $page_permalink;
		wp_redirect( site_url().$page_permalink['login_permalink']->ID );
		exit;
	}

	function ace_register_url($link){
		global $page_permalink;
	    return str_replace(site_url('wp-login.php?action=register', 'login'),site_url().$page_permalink['reg_permalink']->ID,$link);	
	}

	// login error
	public function ace_my_login_redirect($redirect_to, $requested_redirect_to, $user) {
		global $page_permalink;
		$login_permalink = get_page_by_title( 'login' );
	    if (is_wp_error($user)) {
	    	if( sanitize_text_field($_REQUEST['wp-submit']) == 'Submit'	 ){
	    	    wp_redirect( get_permalink($login_permalink->ID)."?&login=failed" ); 
	    	}else{
	       	 	wp_redirect( get_permalink($login_permalink->ID)."?&login=failed" ); 
	        }
	        exit;
	    }else{
	    	if( $user->roles[0] == 'administrator' ) {
	    		wp_redirect( site_url('/wp-admin'));
	    	}else {
	    		wp_redirect( site_url($page_permalink['profile_permalink']->ID)	);
	    	}
	    }
	}

	public function ace_catch_empty_user( $username, $pwd ) {
		$login_permalink = get_page_by_title( 'login' );
		$recaptcha_keys = get_option('custom_reCapatcha_value');
		if( $recaptcha_keys['c_reCaptcha_value'] == 1  ){
		  	$recEmpty = empty($_POST['g-recaptcha-response']);
		  }
		if( $_POST['wp-submit']){
		  if ( empty( $username )  || empty($pwd) || $recEmpty )  {
		    wp_redirect( get_permalink($login_permalink->ID).'?&login=empty' );
		    exit;
		  }
		} else {
			wp_redirect( site_url() . "/login" ); 
		    exit;
		}
  	}

	// user custom profile page
	public function ace_subscriber_login(){
		 global $current_user, $page_permalink;
		get_currentuserinfo();
		if ( ! user_can( $current_user, "administrator" ) ) {
		 	wp_redirect( site_url(). $page_permalink['profile_permalink']->ID );
		 } 
	}

	public function ace_loginout_menu_link( $items, $args ) {
		global $page_permalink;
	   if ($args->theme_location == 'primary') {
	      if (is_user_logged_in()) {
	      	 $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="'. get_permalink($page_permalink['profile_permalink']->ID).'">'. __("Profile") .'</a></li>';
	         $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="'. wp_logout_url() .'">'. __("Log Out") .'</a></li>';
	      } else {
	        $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="'. get_permalink($page_permalink['login_permalink']->ID).'">'. __("Log In") .'</a></li>';
	      	$items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="'. get_permalink($page_permalink['reg_permalink']->ID).'">'. __("Sign Up") .'</a></li>';
	      }
	   }
	   return $items;
	}
	
	// login and profile access
	public function ace_redirect_to_specific_page() {
		global $current_user, $page_permalink;
		get_currentuserinfo();
		if ( ! user_can( $current_user, "administrator" ) ) {
			if ( is_user_logged_in()  ) {
				if ( is_page($page_permalink['login_permalink']->ID))  {
					wp_redirect( get_permalink( $page_permalink['profile_permalink']->ID ) ); 
					exit;
			    }
			    if ( is_page($page_permalink['reg_permalink']->ID)) {
					wp_redirect( site_url() ); 
					exit;
			    }
			}else{
				if( is_page($page_permalink['profile_permalink']->ID)) {
					wp_redirect( get_permalink($page_permalink['login_permalink']->ID ) ) ; 
					exit;	
				}
			}
		}
	}

	public function ace_page_load_action_hooks(){

		// custom profile page 
		function ace_subscriber_profile(){
			global $page_permalink;
			if(current_user_can( 'manage_options' )) return ''; 
			if( strpos( site_url(), 'wp_admin/?action=reg.php' ) ){
				wp_redirect( get_permalink($page_permalink['login_permalink']->ID ) );
				exit;
			}
		}

		function ace_user_profile_template(){

			require_once ( __DIR__ ). '/partials/ace-profile.php';				
		}
		add_shortcode('ace-profile-page', 'ace_user_profile_template');
		
		function ace_login_form(){
			$login_permalink = get_page_by_title( 'login' );
		    if( isset( $_GET['forget-password'] ) ){ 
		   	global $wpdb, $page_permalink ;
				if(isset($_POST['reset-submit'])){
					$reset_password = $wpdb->escape($_POST['reset_password']);
					$error 			= array();
					if(!empty($reset_password)){
						if(is_email($reset_password)){
							if(email_exists($reset_password)){
									$user = get_user_by('email',$reset_password);
									$user_id 	=  $user->ID;
									$user_email =  sanitize_text_field( $user->user_email);
									$random_code = str_shuffle( rand(10000,1000000) );
									$table_name = $wpdb->prefix."ace_reset_password" ;
									$wpdb->insert($table_name, 
												array(
												  "user_id" 	=> $user_id,
												  "email"   	=> $reset_password,
												  "randomCode"  => $random_code )
											);							
									$to      = $reset_password;
								    $subject = __('Lost Password','ace-user-management');
								    $message = get_permalink( $page_permalink['confirm_pass']->ID)."?randNum=".$random_code;
								    $headers = get_option('admin_email');		    
								    if( wp_mail( $to, $subject, $message, $headers )){ ?>
										<div class="ace-all-success"><?php _e('Please check you email for reset password','ace-user-management'); ?></div><br>
									<?php exit;		
									} else { ?>
									<div class="ace-all-error"><?php _('! Link not send','ace-user-management'); ?></div><br>
							<?php	} 

							} else {
								$error['email_exists'] = __('! Email Not exists','ace-user-management');
							}
						} else {
							$error['email_validate'] = __('! Please enter a validate email','ace-user-management');
						}

					} else {
						$error['empty_email'] = __('! Please enter Email field','ace-user-management'); 
					}
					foreach ($error as $email_error) {
						 ?>
					<div class="ace-all-error"><?php print_r($email_error); ?></div><br>
					<?php
					}
				}

				?>
		    	<div class="ace-user_forget-form">
					<form name="mylostpasswordform" id="ace-mylostpasswordform" class="ace-mylostpasswordform" action="" method="post">
					    <div class="ace-form-label">
					        <label><?php _e('Username or E-mail:','ace-user-management'); ?><br><br>
					        <input type="text" name="reset_password" value="" size="20" tabindex="10"></label>
					    </div><br>    
					    <input type="hidden" name="redirect_to" value="<?php echo $redirect ?>">
					    <input type="submit" name="reset-submit" class="ace-btn-rest-pass" id="rest-password" value="<?php _e('Get New Password','ace-user-management'); ?>">
					</form>
				</div>
		   <?php

		    } else {
		      $recaptcha_keys = get_option('custom_reCapatcha_value');
			if ( isset( $_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response']) {
			   var_dump($_POST);
			   $captcha = $_POST['g-recaptcha-response']  ;
			   $secretKey = '<?php  echo $recaptcha_keys["author_reCap_secertkey"]; ?>';
			   $remoteip = $_SERVER['REMOTE_ADDR'];
			   $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secretKey . "&response=" . $captcha . "&remoteip=" . $remoteip);
		    	$responseKeys = json_decode($response, true);
			        if (intval($responseKeys["success"]) !== 1) {
			            return "failed";
			        } else {
			            return "success";
			        }
		    }
		     	  
		     	  $url = ( __DIR__ );
			require_once ( __DIR__ ). '/partials/ace-login.php';
		     }
	 	}
		add_shortcode( 'ace-login-public-form', 'ace_login_form');
	
		function ace_confirm_code_page(){
				global $wpdb;
				$get_randcode = (isset( $_GET['randNum'] ) ) ? sanitize_text_field( $_GET['randNum'] ) : '' ;
				$tablename = $wpdb->prefix."ace_reset_password";
				$sql = $wpdb->get_row( "SELECT * FROM $tablename WHERE randomCode = '$get_randcode'" );
				if( !empty( $sql->randomCode ) ){ 
					$user_id = $sql->user_id;
					 if( sanitize_text_field( $_POST ) ){
						$password = (isset( $_POST['password'] ) ) ? sanitize_text_field($_POST['password']) : '' ;
						$con_password = (isset( $_POST['password'] ) ) ? sanitize_text_field($_POST['confirm_password']) : '' ;
						if(!empty($password)){
							if(strlen($password) > 5){
								if(!empty($con_password)){
									if(strcmp($password, $con_password) == 0){
										if(isset( $_POST['submit_password'])){
											 $user = intval($_POST['u']);
											 $password = md5($password);
											 $userTable = $wpdb->prefix.'users';
											 $update_pass = $wpdb->query("UPDATE $userTable SET user_pass = '$password' WHERE ID =$user");
												$wpdb->delete( $tablename, [ 'user_id' => $user] ); ?>
											<div class="ace-all-success"><?php _e('Password seccessfully update','ace-user-management'); ?></div>
								  <?php }

									} else {
										$error['confrim_password'] = __('! Confrim password not match','ace-user-management');
									}
								} else {
									$error['empty_password'] = __('! Please enter confrim password','ace-user-management');	
								}

							} else {
								$error['empty_password'] = __('! More than 5 character','ace-user-management');	
							}

						} else {
							$error['empty_password'] = __('! Please Enter  password','ace-user-management');
						}
						foreach ($error as $error_key => $error_value) {?>
							<div class="ace-all-error"><?php print_r($error_value); ?></div>
				  <?php }
						
					}			
					require_once ( __DIR__ ). '/partials/ace-confirm-randnum.php';
					} else {
						 _e('This link has been expired.','ace-user-management');
					}		
		}
		add_shortcode( 'ace-random-code-page', 'ace_confirm_code_page' );

		if(!function_exists('ace_reg_form')){
	        function ace_reg_form(){
	        	global $wpdb, $user_ID;
	        	$login_permalink = get_page_by_title( 'login' );
	        	$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ace_register_fields ORDER BY sortby ASC ", OBJECT );
		            $firstname='';
		            $lastname='';
		            $username='';
		            $email='';
			    if(isset( $_POST['submit'] )  && sanitize_text_field( $_POST['submit']) != ''){
		            $username = sanitize_text_field(  $_REQUEST['username'] );
		            $email = sanitize_text_field(  $_REQUEST['email']  );
		            $password = $wpdb->escape( sanitize_text_field( $_REQUEST['password']));
		            $c_password = sanitize_text_field($_POST['c_password']);
		            $reCaptcha = ( isset ($_POST['g-recaptcha-response'])) ? sanitize_text_field($_POST['g-recaptcha-response'] ) : '' ;
		            $error = array( );
		            $success ='';
			        if(!empty($username)){
			            if(username_exists($username)){
			                $error['username_exists'] = __('Username exists','ace-user-management');
			            }
			        }else{
			                $error['username_empty'] = __('Enter Username','ace-user-management');
			           }
			        if(!empty($email)){
			            if(is_email($email)){
			                if(email_exists($email)) {
			                    $error['email_exists'] = __('This email already exists','ace-user-management');
			                }
			            } else {
			                $error['email_valid'] = __('Enter valid email','ace-user-management');
			            }
			        } else {
			            $error['email_empty'] = __('Enter email','ace-user-management');
			        }
			        if(!empty($password)){
			            if(!empty($c_password)){
			                if(strcmp($password, $c_password) != 0){
			                $error['con_password'] =__('password not same','ace-user-management');
			            	}
			            }else{
			                $error['confirm_empty'] = __('Enter Confirm Password','ace-user-management');    
			            }
			        }else{
			            $error['password_empty'] = __('Enter Password','ace-user-management');
			        }
			        $recaptcha_v = get_option('custom_reCapatcha_value');
			        if( $recaptcha_v['c_reCaptcha_value'] == 1 ){
				        if( empty( $reCaptcha ) ){
				        	$error['reCap'] = __('reCaptcha is not set','ace-user-management');
				        }
			    	}
			        if(empty($error)){
						$user_id = wp_create_user( $username, $password, $email );
				        unset($_POST['username']);
					    unset($_POST['email']);
					    unset($_POST['password']);
					    unset($_POST['c_password']);
					    unset($_POST['submit']);
					    $user_extrafields = Ace_User_Management_Function::sanitize($_POST );
					 	update_user_meta( $user_id, 'user_extrafields', $user_extrafields );
					 	$user_data = get_user_meta( $user_id, 'user_extrafields', true );
					 	foreach ( $user_data as $user_key => $user_value ) {
							update_user_meta( $user_id, $user_key , $user_value );
					 	} ?>
					 	<script>alert('You have successfully registered and logged in.');</script>
					 	<script>window.location = "<?php echo get_permalink( $login_permalink->ID );?>"</script>
			  <?php }
			    }
	            require_once  plugin_dir_path( __FILE__ ). 'partials/ace-registration.php';
	         }
	        add_shortcode( 'ace-registration-public-form', 'ace_reg_form');

	        function ace_current_user_profile(){
	        	require_once  plugin_dir_path( __FILE__ ). 'partials/ace-profile.php';
	        }
	        add_shortcode( 'ace-profile-page', 'ace_current_user_profile' );
    	}	
	}
}
