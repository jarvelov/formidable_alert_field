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

  define( 'FADIR', WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__)) );
  define( 'FAURL', WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)) );

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

        //Register activation hook to install database
        register_activation_hook( __FILE__, array($this, 'install_frm_alert') );

        //Remove cron schedule
        register_deactivation_hook( __FILE__, array($this, 'uninstall_frm_alert') );

        //Schedule cron
        /*
        if ( !wp_next_scheduled( 'frm_alert_cron' ) ) {
            wp_schedule_event( time(), 'hourly', 'frm_alert_cron' );
        }
        */
        add_action( 'frm_alert_cron', array($this, 'frm_alert_cron_callback') );
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

    /** install_frm_alert
    *
    */
    public function install_frm_alert() {
        /* Create database table */
        global $wpdb;
        $table_name = $wpdb->prefix . self::slug;

        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            field_id mediumint(9) NOT NULL,
            form_id mediumint(9) NOT NULL,
            scheduled tinyint(1) NOT NULL,
            last_run timestamp NULL,
            next_run timestamp NULL,
            action varchar(255) DEFAULT '' NOT NULL,
            settings text NULL,
            created timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
            UNIQUE KEY id (id)
            ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

    } // end install_frm_alert
    /** init_controller()
    *
    */
    private function init_controller() {
        try {
            $controller = new Frm_Alert_Controller();
            return $controller;
        } catch(Exception $e) {
            return false;
        }
    } //end init_controller

    /** frm_alert_cron_callback
    *
    */
    public function frm_alert_cron_callback() {
        //TODO break up this code and move the sql querying into a function in the controller class
        global $wpdb;
        $table_name = $wpdb->prefix . self::slug;

        /* Find entries which are scheduled to run and where the time
        *   for next run in the next_run column has been passed.
        *   Then do the associated action in the action column
        *   with the customized settings from the settings column
        */

        $result = $wpdb->get_results( "SELECT id, name FROM mytable", ARRAY_A );

        foreach ($result as $row => $columns) {
            var_dump($row, $columns);
        }

    } //end frm_alert_cron_callback

    /** uninstall_frm_alert()
    *
    */
    public function uninstall_frm_alert() {
        wp_clear_scheduled_hook('my_hourly_event');
    } // end uninstall_frm_alert
} // end class
new Frm_Alert();

?>