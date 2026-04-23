<?php
/**
 * Registry Form Pro - Builder AJAX Logic
 * Developed by: James P. Friday
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * FETCH EXISTING LAYOUT
 * Called by the JS Builder to populate the canvas on page load
 */
add_action('wp_ajax_rfp_load_builder_data', 'rfp_load_builder_data_handler');

function rfp_load_builder_data_handler() {
    // Security check
    if ( ! current_user_can('manage_options') ) wp_send_json_error('Unauthorized');

    // Retrieve the saved layout from WP Options
    $saved_layout = get_option('rfp_custom_form_layout', '');

    if ( ! empty($saved_layout) ) {
        wp_send_json_success( json_decode( stripslashes($saved_layout) ) );
    } else {
        wp_send_json_error('No layout found');
    }
}

/**
 * DATA SANITIZATION HELPER
 * Ensures field names are database-safe (lowercase, no spaces)
 */
function rfp_sanitize_field_name($name) {
    // Replace spaces with underscores and remove special characters
    $name = str_replace(' ', '_', strtolower(trim($name)));
    return preg_replace('/[^a-z0-9_]/', '', $name);
}

/**
 * EXPORT FORM CONFIG (Surgical Export)
 * Useful for moving form layouts between different registry sites
 */
add_action('wp_ajax_rfp_export_form', function() {
    if ( ! current_user_can('manage_options') ) wp_send_json_error('Unauthorized');
    
    $layout = get_option('rfp_custom_form_layout', '');
    wp_send_json_success(array(
        'export_data' => $layout,
        'filename'    => 'rfp-form-export-' . date('Y-m-d') . '.json'
    ));
});