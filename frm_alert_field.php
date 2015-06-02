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
        add_filter('frm_pro_available_fields', array('Frm_Alert_Field', 'add_alert_field') );
/*
        //Set up default settings for alert field
        add_filter('frm_before_field_created', array('Frm_Alert_Field', 'set_alert_field_defaults') );

        //Show the field in the form builder
        add_action('frm_display_added_fields', array('Frm_Alert_Field', 'alert_field_admin') );

        //Set field options
        add_action('frm_field_options_form', array('Frm_Alert_Field', 'alert_field_options', 10, 3) );

        //Show field in the front end
        add_action('frm_form_fields', array('Frm_Alert_Field', 'alert_field_front_end', 10, 2) );
        */
    }

    //Add alert field to available formidable fields
    public static function add_alert_field($fields){
      $fields['frm_alert_field'] = __('Alert Field'); // the key for the field and the label
      return $fields;
    }

    //Set default options for the alert field
    public static function set_alert_field_defaults($field_data){
      if($field_data['type'] != 'frm_alert_field'){ //change to your field key
        return $field_data;
      }
      
      $field_data['name'] = __('frm_alert_field');
      $defaults = array(
        'size' => 400, 'max' => 150,
        'label1' => 'Draw It',
      );

      foreach($defaults as $k => $v) {
        $field_data['field_options'][$k] = $v;
      }
            
      return $field_data;
    }

    //Add button to display in the in form builder
    public static function alert_field_admin($field){
      if ( $field['type'] != 'frm_alert_field') {
        return;
      }
                
      $field_name = 'item_meta['. $field['id'] .']';
      ?>
    <div style="width:100%;margin-bottom:10px;text-align:center;">
    <div class="howto button-secondary frm_html_field">This is a placeholder for your frm_alert_field field.</div>   
    </div>
    <?php
    }

    //Add options to configure field in form builder
    public static function alert_field_options($field, $display, $values){
      if ( $field['type'] != 'frm_alert_field' ) {
        return;
      }
      
      $defaults = array(
        'size' => 400, 'max' => 150,
        'label1' => 'Draw It',
      );

      foreach($defaults as $k => $v){
        if ( ! isset($field[$k]) ) {
          $field[$k] = $v;
        }
      }
    ?>
    <tr><td><label>Field Size</label></td>
        <td>
        <input type="text" name="field_options[size_<?php echo $field['id'] ?>]" value="<?php echo esc_attr($field['size']); ?>" size="5" /> <span class="howto">pixels wide</span>

        <input type="text" name="field_options[max_<?php echo $field['id'] ?>]" value="<?php echo esc_attr($field['max']); ?>" size="5" /> <span class="howto">pixels high</span>
        </td>
    </tr>

    <tr><td><label>frm_alert_field Options</label></td>
        <td>
        <label for="label1_<?php echo $field['id'] ?>" class="howto">Draw It Label</label>
        <input type="text" name="field_options[label1_<?php echo $field['id'] ?>]" value="<?php echo esc_attr($field['label1']); ?>" class="frm_long_input" id="label1_<?php echo $field['id'] ?>"  />
        </td>
    </tr>
    <?php
    }

    //Show alert field when form is viewed on the front end
    public static function alert_field_front_end($field, $field_name){
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