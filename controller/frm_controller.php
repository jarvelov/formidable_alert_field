<?php

Class Frm_Alert_Controller {
    /* Helper Functions */

    //Convert object to array
    public function objectToArray($d) {
        if (is_object($d)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        }

        if (is_array($d)) {
            /*
            * Return array converted to object
            * Using __CLASS__ and __FUNCTION__ (Magic constant)
            * for recursive call
            */
            return array_map( array(__CLASS__, __FUNCTION__), $d);
        } else {
            // Return array
            return $d;
        }
    }

    /** load_file
     * Helper function for registering and enqueueing scripts and styles.
     *
     * @name            The ID to register with WordPress
     * @file_path       The path to the actual file, can be an URL
     * @is_script       Optional argument for if the incoming file_path is a JavaScript source file.
     * @dependencies    Optional argument to specifiy file dependencies such as jQuery, underscore etc.
     */
    public function load_file( $name, $file_path, $is_script = false, $dependencies = 'jquery') {
        $path = plugin_dir_path( dirname(__FILE__) ) . $file_path;
        if( file_exists($path) ) {
            $file_url = plugin_dir_url( dirname(__FILE__) ) . $file_path;
            if( $is_script ) {
                wp_register_script( $name, $file_url, $dependencies );
                wp_enqueue_script( $name );
            } else {
                wp_register_style( $name, $file_url );
                wp_enqueue_style( $name );
            } // end if
        } // end if
    } // end frm_alert_enqueue_file
}
