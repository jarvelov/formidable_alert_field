jQuery(document).ready(function($){
    $('.trigger_fields_select').change(function() {
        $('.alert_trigger_fields_container').hide(); //hide all trigger fields initially
        $('#alert_trigger_value_option_' + this.value).css( "display", "inline"); //show the appropriate trigger field's values
    });

    $('.trigger_field_action').change(function() {
      console.log(this.value);
        $('.alert_action_field').hide();

        $('#alert_action_' + this.value).css( "display", "inline"); //show the appropriate trigger field's values
    });

    $('#alert_delay_active').change(function() {
      if(this.checked) {
        $('.alert_setting').show();
      } else {
        $('.alert_setting').hide();
      }
    });
});
