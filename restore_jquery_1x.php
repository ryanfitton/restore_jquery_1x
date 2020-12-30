<?php
	/* 
		Plugin Name: Restore jQuery v1.x for WP
		Plugin URI: https://github.com/ryanfitton/restore-jquery-v1x-for-wp
		Description: Since WP 5.5. jQuery migrate is no longer enabled by default. And in WP 5.6 jQuery has been upgraded from v1.12.4 to v3.x. This plugin disables the default WP jQuery library and re-enables jQuery v1.12.4.
		Requires at least: 5.5
		Tested up to: 5.6
		Requires PHP: 5.6
		Version: 1.0.0
		Author: Ryan Fitton
		Author URI: https://ryanfitton.co.uk/
		License: GPLv2
		License URI: http://www.gnu.org/licenses/gpl-2.0.html
	*/ 

	$plugin_version = '1.0.0';


	/*******************************
		Replace jQuery version
	*******************************/
	function jquery_replace() {
		//Run if not in admin area
		if (!is_admin()) {
            get_bloginfo( 'version' );
            global $wp_version;

            //WordPress => 5.6
            if ( version_compare( $wp_version, '5.6', '>=' ) ) {
				$handle = 'jquery-core';										//Handle
				$file = plugin_dir_url( __FILE__ ) . 'jquery-1.12.4.js'; 		//Specific jQuery version 1.12.4 (/wp-content/plugins/restore_jquery_1x/jquery-1.12.4.js)

            //Wordpress <= 5.5
            } else {
				$handle = 'jquery';												//Handle
				$file = includes_url( '/js/jquery/jquery.js' );					//jQuery included with WordPress 5.5 core (/wp-includes/js/jquery/jquery.js)
            }
            
            wp_deregister_script( $handle );
			wp_enqueue_script( $handle, $file, false, $plugin_version, false );

    
			//When using the jQuery Migrate plugin: https://wordpress.org/plugins/enable-jquery-migrate-helper/ or default WP
			if( !wp_script_is( 'jquery-migrate', 'enqueued' ) ) {
				wp_deregister_script( 'jquery-migrate' );   //De-register handle

				/*
					//Not required anymore
					wp_register_script( 'jquery-migrate', plugin_dir_url( __FILE__ ) . 'jquery-migrate-1.4.1.min.js', false, $plugin_version, false );
					wp_enqueue_script( 'jquery-migrate' );      				//Re-enqueue - so the script is loaded after the jQuery file above
				*/
			}
		}
	}
	add_action('wp_enqueue_scripts', 'jquery_replace', 999999); //Higher the number, lower the priority. The goal is to have this run last.



	/*******************************
		Remove Async or Defer tags
	*******************************/
	function jquery_attributes( $tag, $handle ) {
		//If these handles:
		if ( 
			'jquery' === $handle || 
			'jquery-core' === $handle || 
			'jquery-migrate' === $handle
		) {
			return str_replace( "defer='defer'", "", $tag );
			return str_replace( "async='async'", "", $tag );
		}
		return $tag;
	}
	add_filter( 'script_loader_tag', 'jquery_attributes', 999999, 2 ); //Higher the number, lower the priority. The goal is to have this run last.
?>
