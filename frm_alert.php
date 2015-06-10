<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

/*
Plugin Name: Formidable Alert
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

  define( 'FADIR', WP_PLUGIN_DIR . basename(dirname(__FILE__)) );
  define( 'FAURL', WP_PLUGIN_URL . basename(dirname(__FILE__)) );

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

        if( !class_exists('Frm_Alert_Controller') ) {
            include_once(FADIR . '/controller/' . self::slug . '_controller.php');
        }
        $frm_alert_field = new Frm_Alert_Field();
    }

} // end class
new Frm_Alert();

?>
