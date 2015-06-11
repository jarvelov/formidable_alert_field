<?php

Class Frm_Alert_Controller Extends Frm_Alert {
    /*******************
    * Helper Functions *
    *******************/

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

    //Convert object to array
    private function objectToArray($obj) {
        if (is_object($obj)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $obj = get_object_vars($obj);
        }

        if (is_array($obj)) {
            /*
            * Return array converted to object
            * Using __CLASS__ and __FUNCTION__ (Magic constant)
            * for recursive call
            */
            return array_map( array(__CLASS__, __FUNCTION__), $obj);
        } else {
            // Return array
            return $obj;
        }
    }

    /****************
    * SQL Functions *
    ****************/

    /** insert_or_update_database_record
    *
    */
    private function insert_or_update_database_record($db_args) {
        global $wpdb;

        $Frm_Alert = new Frm_Alert();
        $table_name = $wpdb->prefix . $Frm_Alert::slug;

        //TODO check if field_id exists within the table and perform and update, or insert a new row

    } // end insert_or_update_database_record

    /**********************
    * Frm_Alert Functions *
    **********************/

    /** schedule_new_alert_action()
    * Adds an action to be performed when a specific condition is true
    */
    public function schedule_new_alert_action($field_options, $field) {
        $action = $field_options['alert_trigger_action'];

        switch ($action) {
            case 'email':
                $settings = array(
                    'email_address' => $field_options['alert_action_email'],
                    'subject' => 'Alert!',
                    'body' => 'Alert triggered!'
                );
                break;
            case 'frm_action':
                $settings = array(
                    'frm_action_id' => $field_options['alert_action_frm_action']
                );
                break;
            default:
                //Unsupported action
                break;
        }

        $db_args = array(
            'field_id' => $field->id,
            'form_id' => $field->form_id,
            'scheduled' => true,
            'next_run' => $next_run,
            'action' => $action,
            'settings' => $settings
        );

        $this->insert_or_update_database_record($db_args);
    } //end schedule_new_alert_action

    /** get_form_field_names_and_values()
    *   Returns an array with field names and their values,
    *   or values and label, if separate values are used
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
}
