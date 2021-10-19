<?php
/**
 * Fired during plugin activation
 *
 * @link       http://acewebx.com
 * @since      1.0.0
 *
 * @package    Ace_User_Management
 * @subpackage Ace_User_Management/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ace_User_Management
 * @subpackage Ace_User_Management/includes
 * @author     Webbninja <webbninja2@gmail.com>
 */
class Ace_User_Management_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		global $wpdb;
		$tableName = $wpdb->prefix."ace_reset_password";
		if( count( $wpdb->get_var( 'SHOW TABLE LINK "$tableName"')) == 0 ){
				$sql_create_table = " CREATE TABLE `$tableName` (
									 `id` int(20) NOT NULL AUTO_INCREMENT,
									 `user_id` int(20) NOT NULL,
									 `email` varchar(100) NOT NULL,
									 `randomCode` varchar(100) NOT NULL,
									 `currentTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
									 PRIMARY KEY (`id`)
									) ENGINE=InnoDB DEFAULT CHARSET=latin1 ";

				require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql_create_table);
		}
		$tableName2 = $wpdb->prefix."ace_register_fields";
		if( count( $wpdb->get_var( 'SHOW TABLE LINK "$tableName2"')) == 0 ){
				$sql_create_table2 = " CREATE TABLE `$tableName2` (
									 `id` int(11) NOT NULL AUTO_INCREMENT,
									 `input_label` varchar(255) NOT NULL,
									 `input_placeholder` varchar(255) NOT NULL,
									 `input_name` varchar(255) NOT NULL,
									 `input_type` varchar(255) NOT NULL,
									 `dropdown_options` varchar(255) NOT NULL,
									 `sortby` int(11) NOT NULL,
									 PRIMARY KEY (`id`)
									) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1";

				require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql_create_table2);
		}
		// activation registration  page create 
		$registration = array(  'post_title' 	=> 'Registration',
								'post_name' 	=> 'registration',
								'post_content' 	=> '[ace-registration-public-form]',
								'post_status' 	=> 'publish',
								'post_slug' 	=> 'ace-register',
								'post_type' 	=> 'page'
						); 

		$registration_id = wp_insert_post($registration);

	    // activation login  page create
	    $login_page	= array( 'post_title' 	=> 'Login',
							 'post_name' 	=> 'loginx',
							 'post_content' => '[ace-login-public-form]',
							 'post_status' 	=> 'publish',
							 'post_slug' 	=> 'ace-login',
							 'post_type' 	=> 'page'
						); 

		$login_id = wp_insert_post($login_page);

		// activation forget password  page create
		/*$forget_page = array( 'post_title' 	=> 'Forget password',
							 'post_name' 	=> 'forget-password',
							 'post_content' => '[ace-forget-password]',
							 'post_status' 	=> 'publish',
							 'post_slug' 	=> 'ace-forgetpass',
							 'post_type' 	=> 'page'

						);*/

	/*	$forget_id = wp_insert_post($forget_page);*/

		// activation forget password  page create
		$random_code_page = array( 'post_title'   => 'Confirm Password',
								   'post_name' 	  => 'confirm-password',
								   'post_content' => '[ace-random-code-page]',
							 	   'post_status'  => 'publish',
							 	   'post_slug' 	  => 'ace-confirm-password',
							 	   'post_type' 	  => 'page'
							); 

		$confirm_id = wp_insert_post($random_code_page);

		// activation forget password  page create
		$profile_page = array( 'post_title'   => 'Profile',
							   'post_name' 	  => 'profile',
							   'post_content' => '[ace-profile-page]',
							   'post_status'  => 'publish',
							   'post_slug' 	  => 'ace-profile',
							   'post_type' 	  => 'page'  
						);

		$profile_id = wp_insert_post($profile_page);

		add_option('ace_auto_page_create', array( 'registration_page_id'   => $registration_id,
												  'login_page_id' 		   => $login_id, 
												  'forgetpassword_page_id' => $forget_id, 
												  'confirm_page_id' 	   => $confirm_id, 
												  'profile_page_id' 	   => $profile_id
										)
		);
	}
}