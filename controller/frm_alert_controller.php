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

    /** schedule_new_alert_action()
    * Schedules an action using wp_cron
    */
    public function schedule_new_alert_action($settings) {
        switch($action) {
            case 'email':
                break;
            case 'frm_action':
                break;
            default:
                break;
        }
    } //end schedule_new_alert_action




    /** get_form_field_names_and_values()
    *   Returns an array with field names and their values, or values and label if separate values are used
    *
    **/
    public function get_form_field_names_and_values($form_id) {
        //Get all current fields in form
        $form_fields_obj = FrmField::get_all_for_form($form_id);

        //Convert form_fields_obj to array
        $form_fields = $this->objectToArray($form_fields_obj);

        $form_fields = array();

        //Go over all fields and get the name and value of it and push to trigger_fields array
        foreach ($form_fields as $form_field) {
            switch ($form_field['type']) {
                case 'frm_alert_field':
                    //Omit frm_alert_fields from being able to trigger on
                    break;
                case 'text':
                    $form_fields[] = array(
                            'name' => $form_field['name'],
                            'value' => $form_field['default_value']
                        );
                    break;
                case 'select':
                    $values = array();
                    foreach ($form_field['options'] as $key => $value) {
                        if(is_array($value)) { //drop down with separate values
                            $values[] = array(
                                    'label' => $value['label'],
                                    'value' => $value['value']
                                );
                        } else {
                            $values[] = array(
                                    'label' => $value,
                                    'value' => $value
                                );
                        }
                    }

                    $form_fields[] = array('name' => $form_field['name'], 'value' => $values);
                    break;
                default:
                    //Unsupported field type
                    break;
            }
        }

        return $form_fields;
    } //end get_form_field_names_and_values

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
    } // end load_file
}
