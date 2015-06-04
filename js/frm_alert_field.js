jQuery(document).ready(function($){
    $('.trigger_fields_select').change(function() {
        $('.alert_trigger_fields_container').hide(); //hide all trigger fields initially
        $('#alert_trigger_value_option_' + this.value).css( "display", "inline"); //show the appropriate trigger field's values
    });

    $('.trigger_fields_select').change(function() {
        $('.alert_action_field').hide();
        $('#trigger_field_action_' + this.value).css( "display", "inline"); //show the appropriate trigger field's values
    });
});
