<?php

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

function dropTable() {
        //drop frm_alert db table
        global $wpdb;
        $table_name = $wpdb->prefix . 'frm_alert';
        $wpdb->query( "DROP TABLE IF EXISTS " . $table_name );
}

if ( !is_multisite() ) {
        dropTable();
} else {
    global $wpdb;
    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    $original_blog_id = get_current_blog_id();

    foreach ( $blog_ids as $blog_id )
    {
        switch_to_blog( $blog_id );
                dropTable();
    }

    restore_current_blog();
}