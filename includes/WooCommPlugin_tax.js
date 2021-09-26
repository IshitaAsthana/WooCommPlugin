jQuery(function($) {
    
    $('body').on('change', '.state_select', function() {
        var stateid = $(this).val();
        if(stateid != '') {

            var data = {
                'action': 'get_states_by_ajax',
                'state': stateid
            }
            $.post('../wp-content/plugins/WooCommPlugin/includes/WooCommPlugin_Tax_Modifier.php', {"state": stateid}, function () {
                
             });
             
        }
        
        jQuery('body').trigger('update_checkout');  
    });
});