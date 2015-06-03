<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

/** Frm_Alert_Field
* Adds an alert field to Formidable which can trigger actions on certain events such as
    a field's value is updated,
    value has not been updated for a specific time period
    etc.
* Notes: Requires Formidable v2.0+
*/

class Frm_Alert_Field Extends Frm_Alert {
    
    function __construct() {
        //Add alert field to available Formidable fields
        add_filter('frm_pro_available_fields', array($this, 'add_alert_field') );

        //Set up default settings for alert field
        add_filter('frm_before_field_created', array($this, 'set_alert_field_defaults') );

        //Show the field in the form builder
        add_action('frm_display_added_fields', array($this, 'alert_field_admin') );

        //Set field options
        add_action('frm_field_options_form', array($this, 'alert_field_options'), 10, 3);

        //Show field in the front end
        add_action('frm_form_fields', array($this, 'alert_field_front_end'), 10, 2);
    }

    /* Helper Functions */

    //Convert object to array
    private function objectToArray($d) {
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

    /** get_alert_field_defaults()
    *   Returns an array with default values
    **/
    private function get_alert_field_defaults() {
        $defaults_array = array(
            'size' => 400, 'max' => 150,
            'label1' => 'Draw It',
            'operators' => array(
                 '==' => 'equal to',
                 '!=' => 'NOT equal to',
                 '>' => 'greater than',
                 '<' => 'less than',
                 '>=' => 'greater than or equal to',
                 '<=' => 'less than or equal to',
            ),
            'durations' => array(
                1 => 'second(s)',
                60 => 'minute(s)',
                3600 => 'hour(s)',
                86400 => 'day(s)'
            ),
            'duration_start' => array(
                'created' => 'Created',
                'updated' => 'Updated'
            ),
            'actions' => array(
                'email' => 'Send E-mail'
            )
        );

        return $defaults_array;
    }

    /** get_form_field_names_and_values()
    *   Returns an array with field names and their values, or values and label if separate values are used
    *
    **/
    private function get_form_field_names_and_values($form_id) {
        //Get all current fields in form
        $form_fields_obj = FrmField::get_all_for_form($form_id);

        //Convert form_fields_obj to array
        $form_fields = $this->objectToArray($form_fields_obj);

        $trigger_fields = array();

        //Go over all fields and get the name and value of it and push to trigger_fields array
        foreach ($form_fields as $form_field) {
            switch ($form_field['type']) {
                case 'frm_alert_field':
                    //We don't want to trigger on our own field
                    break;
                case 'text':
                    $trigger_fields[] = array(
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

                    $trigger_fields[] = array('name' => $form_field['name'], 'value' => $values);
                    break;
                default:
                    //Unsupported field type
                    break;
            }
        }

        return $trigger_fields;
    }

    //Add alert field to available formidable fields
    public function add_alert_field($fields){
      $fields['frm_alert_field'] = __('Alert Field'); // the key for the field and the label
      return $fields;
    }

    //Set default options for the alert field
    public function set_alert_field_defaults($field_data){
      if($field_data['type'] != 'frm_alert_field'){ //change to your field key
        return $field_data;
      }
      
      $field_data['name'] = __('frm_alert_field');
      $defaults = $this->get_alert_field_defaults();

      foreach($defaults as $key => $value) {
        $field_data['field_options'][$key] = $value;
      }
            
      return $field_data;
    }

    //Add button to display in the in form builder
    public function alert_field_admin($field){
      if ( $field['type'] != 'frm_alert_field') {
        return;
      }
                
      $field_name = 'item_meta['. $field['id'] .']';
    }

    //Add options to configure field in form builder
    public function alert_field_options($field, $display, $values){
          if ( $field['type'] != 'frm_alert_field' ) {
            return;
          }

        $defaults = $this->get_alert_field_defaults();

        foreach($defaults as $key => $value){
          if ( ! isset($field[$key]) ) {
            $field[$key] = $value;
          }
        }

        //Get all fields in form to build trigger alert option
        $form_id = intval($field['form_id']);
        $trigger_fields = $this->get_form_field_names_and_values($form_id);
       
        ?>
            <tr><td><label>Alert Settings</label></td>
                <td>
                <?php
                    $trigger_field = '<select name="field_options[trigger_fields_select_' . $field['id'] . '">';
                    $trigger_field .= '<option value="">— Select —</option>';
                    $trigger_values = NULL;
                    //trigger_fields
                    foreach ($trigger_fields as $key => $value) {
                        $trigger_field .= '<option value="' . $key . '">' . $value['name'] . '</option>';

                        //trigger_values
                        if( is_array($value['value']) ) {
                            $trigger_values .= '<input type="text" name="field_options[alert_trigger_value_custom_value_' . $field['id'] . '_' . $key . ']" placeholder="Enter a custom value" id="alert_trigger_value_custom_value" />';

                            $trigger_values .= '<select name="field_options[alert_trigger_value_' . $field['id'] . '_' . $key . ']" id="alert_trigger_value_' . $key . '">';
                            $trigger_values .= '<option value="">— Select —</option>';

                            foreach ($value['value'] as $key => $value) {
                                $trigger_values .= '<option value="' . $value['value'] . '">' . $value['label'] . '</option>';    
                            }

                            $trigger_values .= '<option value="custom_value">Custom Value</option>';
                            $trigger_values .= '</select>';
                        } else {
                            $trigger_values .= '<input type="text" name="field_options[alert_trigger_value_' . $field['id'] . '_' . $key . ']" value="' . $value['value'] . '" id="alert_trigger_value_' . $key . '" />';
                        }
                    }
                    $trigger_field .= '</select>';

                    //trigger condition operators
                    $trigger_operator = '<select name="field_options[trigger_field_condition_operator_' . $field['id'] . ']">';
                    $trigger_operator .= '<option value="">— Select —</option>';
                    foreach ($defaults['operators'] as $key => $value) {
                        $trigger_operator .= '<option value="' . htmlspecialchars($key) . '">' . $value . '</option>';
                    }

                    //trigger condition duration
                    $trigger_duration = '<input type="number" name="field_options[trigger_field_duration_number_' . $field['id'] . ']" step="1" />';
                    $trigger_duration .= '<select name="field_options[trigger_field_condition_duration_' . $field['id'] . ']">';
                    $trigger_duration .= '<option value="">— Select —</option>';
                    foreach ($defaults['durations'] as $key => $value) {
                        $trigger_duration .= '<option value="' . $key . '">' . $value . '</option>';
                    }
                    $trigger_duration .= '</select>';

                    //trigger condition duration start - i.e. what time to consider the start time for duration
                    $trigger_duration_start = '<select name="field_options[trigger_field_condition_duration_start_' . $field['id'] . ']">';
                    $trigger_duration_start .= '<option value="">— Select —</option>';
                    foreach ($defaults['duration_start'] as $key => $value) {
                        $trigger_duration_start .= '<option value="' . $key . '">' . $value . '</option>';
                    }
                    $trigger_duration_start .= '</select>';

                    //trigger action
                    $trigger_action = '<select name="field_options[trigger_field_action_' . $field['id'] . ']">';
                    $trigger_action .= '<option value="">— Select —</option>';
                    foreach ($defaults['actions'] as $key => $value) {
                        $trigger_action .= '<option value="' . $key . '">' . $value . '</option>';
                    }
                    $trigger_action .= '</select>';

                    $alert_action_fields = '<input type="email" name="field_options[alert_action_email_' . $field['id'] . ' class="alert_actions" id="alert_action_email" placeholder="Ex. [admin_email] or [125]" />';

                    //Alert is active between these dates
                    $alert_period_start = '<input type="date" name="field_options[alert_period_start_' . $field['id'] . '" />';
                    $alert_period_stop = '<input type="date" name="field_options[alert_period_stop_' . $field['id'] . '" />';

                    echo '<div class="alert_trigger_field_container">' . $trigger_field . '</div>';
                    echo '<div class="alert_trigger_operator_container">' . $trigger_operator . '</div>';
                    echo '<div class="alert_trigger_value_container">' . $trigger_values . '</div>';
                    echo '<div class="alert_trigger_duration_container">' . $trigger_duration . ' after an entry is ' . $trigger_duration_start . '</div>';
                    echo '<div class="alert_trigger_action_container">' . 'then' . $trigger_action . '</div>';
                    echo '<div class="alert_action_fields_container">' . $alert_action_fields . '</div>';
                    echo '<div class="alert_active_period_container">' . 'from ' . $alert_period_start . ' to ' . $alert_period_stop .'</div>';
                    ?>
                </td>
            </tr>
        <?php
    }

    //Show alert field when form is viewed on the front end
    public function alert_field_front_end($field, $field_name){
      if ( $field['type'] != 'frm_alert_field' ) {
        return;
      }
      $field['value'] = stripslashes_deep($field['value']);
    ?>
    <input type="text" id="field_<?php echo $field['field_key'] ?>" name="item_meta[<?php echo $field['id'] ?>" value="<?php echo esc_attr($field['value']) ?>" <?php do_action('frm_field_input_html', $field) ?>/>
    <?php
    }
}

?>