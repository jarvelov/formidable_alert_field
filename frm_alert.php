<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

/*
Plugin Name: Frm Alert
Plugin URI: http://tobias.jarvelov.se/portfolio/frm_alert
Description: Extends Formidable plugin adding an alert field
Version: 0.1
Author: Tobias Järvelöv
Author Email: tobias@jarvelov.se
License:

  Copyright 2015 Tobias Järvelöv (tobias@jarvelov.se)

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
  
*/

// don't load directly
  if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
  }

  define( 'FADIR', WP_PLUGIN_DIR . '/formidable_alert_field' );
  define( 'FAURL', WP_PLUGIN_URL . '/formidable_alert_field' );

  class Frm_Alert {

    /*--------------------------------------------*
     * Constants
     *--------------------------------------------*/
    const name = 'Frm Alert';
    const slug = 'frm_alert';
    
    /**
     * Constructor
     */
    function __construct() {
        //Hook up to the init action
        add_action( 'init', array( $this, 'init_frm_alert' ) );
    }
    
    /**
     * Runs when the plugin is initialized
     */
    public function init_frm_alert() {
        if( !class_exists('Frm_Alert_Field') ) {
            include_once(FADIR . '/' . self::slug . '_field.php');
        }

        $frm_alert_field = new Frm_Alert_Field();
    }

    /** frm_alert_enqueue_file
     * Helper function for registering and enqueueing scripts and styles.
     *
     * @name            The ID to register with WordPress
     * @file_path       The path to the actual file, can be an URL
     * @is_script       Optional argument for if the incoming file_path is a JavaScript source file.
     * @dependencies    Optional argument to specifiy file dependencies such as jQuery, underscore etc.
     */
    public function frm_alert_enqueue_file( $name, $file_path, $is_script = false, $dependencies = 'jquery') {
        $path = plugin_dir_path(__FILE__) . $file_path;
        if( file_exists($path) ) {
            if( $is_script ) {
                wp_register_script( $name, $path, $dependencies );
                wp_enqueue_script( $name );
            } else {
                wp_register_style( $name, $path );
                wp_enqueue_style( $name );
            } // end if
        } // end if
    } // end frm_alert_enqueue_file
    
} // end class
new Frm_Alert();

?>