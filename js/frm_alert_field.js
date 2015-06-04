jQuery(document).ready(function($){
    $('.trigger_fields_select').change(function() {
        $('.alert_trigger_fields_container').hide(); //hide all trigger fields initially
        $('#alert_trigger_value_option_' + this.value).show(); //show the appropriate trigger field's values
    });
});