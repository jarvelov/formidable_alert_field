jQuery(document).ready(function($){
    console.log(this.value);
    $(document).on("change",".trigger_fields_select",function() {
        console.log(this.value);
        $('.alert_trigger_fields_container').hide(); //hide all trigger fields initially
        $('#alert_trigger_value_option_' + this.value).css( "display", "inline"); //show the appropriate trigger field's values
    });

    $(document).on("change",".alert_trigger_value_select",function() {
        console.log(this.value);
        if(this.value == 'custom_value') {
            id = this.id.substr( this.id.lastIndexOf('_') +1 ); //get id number of select element to know which custom_value field to show
            $('#alert_trigger_value_custom_value_' + id).show(); //show relevant custom_value field
        } else {
            $('.alert_trigger_value_custom_value').hide(); //hide custom_value fields
        }
    });

    $(document).on("change",".trigger_field_action",function() {
      console.log(this.value);
        $('.alert_action_field').hide();

        $('#alert_action_' + this.value).css( "display", "inline"); //show the appropriate trigger field's values
    });

    $(document).on("change","#alert_delay_active",function() {
        console.log(this.value);
      if(this.checked) {
        $('.alert_setting').show();
      } else {
        $('.alert_setting').hide();
      }
    });
});