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

        //Set field option values
        add_filter( 'frm_setup_edit_field_vars', array($this, 'alert_field_options_values'), 30 );

        //Show field in the front end
        add_action('frm_form_fields', array($this, 'alert_field_front_end'), 10, 2);
    }

    /** get_alert_field_defaults()
    *   Returns an array with default values
    **/
    private function get_alert_field_defaults() {
        $defaults_array = array(
            'size' => 400,
            'customSetting' => 'Placeholder text'
        );

        return $defaults_array;
    }

    //Add alert field to available formidable fields
    public function add_alert_field($fields){
        $fields['frm_alert_field'] = __('Alert Field'); // the key for the field and the label
        return $fields;
    }

    //Set default options for the alert field
    public function set_alert_field_defaults($field_data){
        if($field_data['type'] != 'frm_alert_field'){
            return $field_data;
        }

        $field_data['name'] = __('Alert Field');
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

    //Set values for each field or fall back to the default value
    function alert_field_options_values( $values ) {
      var_dump($values);
      break;
          $defaults = $this->get_alert_field_defaults();

          foreach ( $defaults as $option => $default_value ) {
              $values[ $option ] = ( isset( $values['field_options'][ $option ] ) ) ? $values['field_options'][ $option ] : $default_value;
          }

          return $values;
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
        ?>
            <tr><td><label>My custom setting</label></td>
                <td>
                <?php
                    $html = '<input type="text" name="field_options[size_' . $field['id'] . ']" value="' . esc_attr($field['size']) . '" /> <span class="howto">Size</span>';
                    $html .= '<br />';
                    $html .= '<input type="text" name="field_options[customSetting_' . $field['id'] . ']" value="' . esc_attr($field['customSetting']) . '" /> <span class="howto">Custom Setting</span>';

                    echo $html;
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
