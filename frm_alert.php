<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

/*
Plugin Name: Frm Alert
Plugin URI: http://jarvelov.se/portfolio/frm_alert
Description: Extends Formidable plugin adding an alert field
Version: 0.1
Author: Tobias Järvelöv
Author Email: tobias@jarvelov.se
License:

  Copyright 2011 Tobias Järvelöv (tobias@jarvelov.se)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/

// don't load directly
  if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
  }

  define( 'FADIR', WP_PLUGIN_DIR . '/frm_alert' );
  define( 'FAURL', WP_PLUGIN_URL . '/frm_alert' );

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
        //register an activation hook for the plugin
        register_activation_hook( __FILE__, array( &$this, 'install_frm_alert' ) );

        //Hook up to the init action
        add_action( 'init', array( &$this, 'init_frm_alert' ) );
    }
    
    /**
     * Runs when the plugin is initialized
     */
    function init_frm_alert() {
        if( !class_exists('Frm_Alert_Field') ) {
            require(FADIR . '/' . self::slug . '_field.php');
        }
    }
    
} // end class
new Frm_Alert();

?>