<?php

/**
 *
 * @link       http://acewebx.com
 * @since      1.0.0
 *
 * @package    Ace_User_Management
 * @subpackage Ace_User_Management/includes
 */
class Ace_User_Management_Function {
	/**
	 * A custom sanitization function that will take the incoming input, and sanitize
	 * the input before handing it back to WordPress to save to the database.
	 *
	 * @since    1.0.0
	 *
	 * @param    array    $input        The input.
	 * @return   array    $new_input    The sanitized input.
	 */
	static function sanitize( $input ) {
		// Initialize the new array that will hold the sanitize values
		$new_input = array();
		// Loop through the input and sanitize each of the values
		foreach ( $input as $key => $val ) {
			switch ( $key ) {
				case 'email':
					$new_input[ $key ] = sanitize_email_field( $val );
					break;
				default:
					$new_input[ $key ] = sanitize_text_field( $val );
					break;
			}
		}
		return $new_input;
	}
}
